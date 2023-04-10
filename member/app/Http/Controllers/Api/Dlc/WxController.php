<?php namespace App\Http\Controllers\Api\Dlc;

use App\Model\User;
use App\Service\Dlc\{UsersService,GuideService};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Validator;
use App\Lib\Http;
use Illuminate\Support\Facades\Log;
use App\Support\Token;
use App\Service\Dlc\HelperService;
use App\Exceptions\ApiPlaintextException;
use App\Service\Dlc\WxService;


class WxController extends ApiController
{
    public function wxLogin(Request $request){
        try{
            $param = $request->all();
            $v = Validator::make($param, [
                'code' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error('授权信息不完整');
            }
            $code = $request->input('code');
            $appid = config('wechat.mini_program.app_id');
            $appSecret = config('wechat.mini_program.secret');
            $postUrl = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=' . $appid . '&secret=' . $appSecret . '&js_code=' . $code;

            $response = http_request($postUrl, false, false, 'GET', '小程序用户登录：');
            $responseData = json_decode($response['data'], true);
            Log::info('wxlogin',[
                'requestData'=>$param,
                'authResp'=>$response
            ]);
            if ($response['httpCode'] != 200) {
                throw new Exception("微信授权失败，请稍后重试。", 0);
            }
            if (isset($responseData['errcode']) && $responseData['errcode'] === 40163) {
                throw new Exception("code 已被使用，请更换", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] === 40029) {
                throw new Exception("code 无效", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] == -1) {
                throw new Exception("系统繁忙，请重试", 0);
            }
            $open_id = $responseData['openid']?:'';
            $union_id = $responseData['unionid']?:'';
            //保存sessionkey
            $session_key = $responseData['session_key'];
            HelperService::setSessionKeyByOpenId($open_id,$session_key);
            //查询用户是否已存在
            $user = UsersService::getUserInfoByOpenid($open_id);
            if($user){
                $uid = $user->id;
            }else{
                //保存unionid
                $update_data = compact('open_id','union_id');
                $uid = UsersService::userUpdateOrInsertByOpenId(compact('open_id'),$update_data);
            }
            return $this->success('success', compact('open_id','union_id'));
        } catch (Exception $e) {
            Log::error($e);
            return response()->errorAjax($e);
        }
    }
    // 小程序授权登录
    public function signin(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'openid' => 'required',
                'rawData' => 'nullable',
            ]);
            Log::info('signInRequestData',$param);
            if ($v->fails()) {
                return $this->error('授权信息不完整，请重新授权');
            }
            $open_id = $param['openid']?:'';
            $rawData = $request->input('rawData', '');
            if ($open_id === '') {
                throw new Exception("微信授权失败，请稍后重试。", 0);
            }
            if($rawData){
                $rawDataArr = json_decode($rawData,true);
                $nickname = array_get($rawDataArr,'nickName');
                $sex = array_get($rawDataArr,'gender');
                $avatar_url = array_get($rawDataArr,'avatarUrl');
                $update_data = compact('nickname','avatar_url','sex');
                $user = UsersService::userUpdateOrInsertByOpenId(compact('open_id'),$update_data);
            }else{
                $user = UsersService::getUserInfoByOpenid($open_id);
            }
            $uid = $user->id;
            $data = compact('open_id');
            //如果会员已经授权过手机号则直接返回token无需再次授权手机号
            if($user->phone){
                $token = Token::createTokenByOpenId($uid,$open_id);
                $data['token'] = $token;
            }
            return $this->success('success', $data);
        } catch (Exception $e) {
            Log::error($e);
            return response()->errorAjax($e);
        }
    }

    // 解密获取微信手机号
    public function wxPhoneLogin(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'iv' => 'required',
                'encryptedData' => 'required',
                'open_id' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error('授权信息不完整，请重新授权');
            }
            $open_id = $request->input('open_id');
            $code = $request->input('code');
            $iv = $request->input('iv');
            $encryptedData = $request->input('encryptedData');
            $appid = config('wechat.mini_program.app_id');
            $appSecret = config('wechat.mini_program.secret');
            if($code){
                $postUrl = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=' . $appid . '&secret=' . $appSecret . '&js_code=' . $code;

                $response = http_request($postUrl, false, false, 'GET', '小程序用户登录：');
                Log::info('wxPhoneLoginC2SResp',$response);
                $responseData = json_decode($response['data'], true);
                if ($response['httpCode'] != 200) {
                    throw new Exception("微信授权失败，请稍后重试。", 0);
                }
                if (isset($responseData['errcode']) && $responseData['errcode'] === 40163) {
                    throw new Exception("code 已被使用，请更换", 0);
                } else if (isset($responseData['errcode']) && $responseData['errcode'] === 40029) {
                    throw new Exception("code 无效", 0);
                } else if (isset($responseData['errcode']) && $responseData['errcode'] == -1) {
                    throw new Exception("系统繁忙，请重试", 0);
                }
                $session_key = $responseData['session_key'];
            }else{
                $session_key = HelperService::getSessionKeyByOpenId($open_id);
            }
            $userifo = new \WXBizDataCrypt($appid, $session_key);
            $errCode = $userifo->decryptData($encryptedData, $iv, $data);
            Log::info(__FUNCTION__.'_code2session',[
                'req'=>$param,
                'session_key'=>$session_key,
                'errCode'=>$errCode,
            ]);
            if ($errCode == '-41003') {
                throw new Exception('不合法的 session_key', 0);
            }
            $info = json_decode($data, true);
            if (!$info) {
                throw new Exception("解密失败", 0);
            }
            logger('微信解密手机号：', $info);
            $phone = $info['purePhoneNumber'];
            if($errCode == 0) {
                $uid = UsersService::userRegister($open_id,$phone);
                $token = Token::createTokenByOpenId($uid,$open_id);
                return $this->success('success',compact('token'));
            } else if ($errCode == '-41003') {
                throw new Exception('不合法的 session_key', 0);
            } else {
                throw new Exception("解密手机号错误！", 0);
            }
        } catch (Exception $e) {
            Log::error($e);
            return response()->errorAjax($e);
        }
    }

    /**
     * 生成海报
     * @param Request $request
     * @return mixed
     */
    public function getQrCode(Request $request)
    {
        try {
            $params = $request->all();
            $v = Validator::make($params, [
                'pages' => 'required',
                'openid' => 'required',
                'sku' => 'nullable',
                'args' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $pages = $params['pages'];
            $args = $params['args'];
            //分享者openid临时索引(因为二维码的地址不能过长所以为openid建立一个临时索引 用索引ID代替)
            $openid = $params['openid'];
            $openid_index = HelperService::setOpenidIndexMap($openid);
            $args['uid'] = $openid_index;
            //追加导购ID
            if($uid = $this->getUid(1)){
                $user = UsersService::getUserInfo($uid);
                $sku = $request->get('sku');
                if(!empty($user->guid_id) && $sku){
                    //生成导购参数
                    $args = array_merge($args,GuideService::bindGuide($user->guid_id,$sku));
                }
            }
            $scene = http_build_query($args);
            $wx = new WxService;
            $data['imgUrl'] = $wx->getQrCode($pages,$scene);
            $data['args'] = $args;
            return $this->success('成功', $data);
        }catch (ApiPlaintextException $e){
            return $this->expire();
        }catch (\Exception $e) {
            return $this->error($e->getMessage().$e->getFile().$e->getLine());
        }

    }

}