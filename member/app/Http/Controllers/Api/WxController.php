<?php

namespace App\Http\Controllers\Api;

use App\Model\SocialUsers;
use App\Service\Guide\EmployeeService;
use App\Service\UsersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WechatUsers;
use App\Model\Users;
use App\Model\CrmAuthToken;
use App\Jobs\CustomerInfoToDb;
use Exception;
use Validator;
use App\Lib\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\MiniAppAccessToken;
use App\Support\CaptchaApi;
use App\Model\Redis;


class WxController extends Controller
{


    // 小程序用户登录
    public function signin(Request $request)
    {
        try {

            $param = $request->all();
            $v = Validator::make($param, [
                'code' => 'required',
            ]);

            if ($v->fails()) {
                return $this->error('授权信息不完整，请重新授权');

            }
            $code = $request->input('code');
            $signature = $request->input('signature', '');
            $rawData = $request->input('rawData', '');
            $iv = $request->input('iv', '');
            $encryptedData = $request->input('encryptedData', '');

            $appid = config('wechat.mini_program.app_id');
            $appSecret = config('wechat.mini_program.secret');
            $postUrl = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=' . $appid . '&secret=' . $appSecret . '&js_code=' . $code;

            $response = http_request($postUrl, false, false, 'GET', '小程序用户登录：');
            $responseData = json_decode($response['data'], true);
            loger(['request' => $param, 'response' => $responseData], 'miniapp');
            if ($response['httpCode'] != 200) {
                throw new Exception("获取openid失败", 0);
            }


            if (isset($responseData['errcode']) && $responseData['errcode'] === 40163) {
                throw new Exception("code 已被使用，请更换", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] === 40029) {
                throw new Exception("code 无效", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] == -1) {
                throw new Exception("系统繁忙，请重试", 0);
            }
            $openId = $responseData['openid'] ? $responseData['openid'] : '';

            if ('' === $openId) {
                throw new Exception("获取openid失败", 0);
            }


            //判断用户是否存在
            $userObj = Users::where('open_id', $openId)->first();

            // 匿名登录
            if (!$userObj) {

                if ($encryptedData) {
                    // 验签

//                    if ($signature != sha1($rawData . $responseData['session_key'])) {
//
//                        loger(['miniapp_signature' => 'fail', 'response' => $responseData], 'miniapp');
//                    } else {
//                        loger(['miniapp_signature' => 'success', 'response' => $responseData], 'miniapp');
//
//                    }
//                    $userifo = new \WXBizDataCrypt($appid, $responseData['session_key']);
//
//                    $errCode = $userifo->decryptData($encryptedData, $iv, $data);
//                    if ($errCode == '-41003') {
//                        throw new Exception('不合法的 session_key', 0);
//                    }

                    socialUsers::updateOrCreate(['open_id' => $openId], [
                        'social_type' => 'miniapp',
//                        'union_id' => $arr['unionId'],
                        'open_id' => $openId,
                        'social_info' => $rawData
                    ]);

                } else {

                    socialUsers::updateOrCreate(['open_id' => $openId], [
                        'social_type' => 'miniapp',
//                        'union_id' => $arr['unionId'],
                        'open_id' => $openId,
                    ]);
                }
                $userObj = [
                    'open_id' => $openId,
                    'pos_id' => '',
                ];
            }
            // 返回加密token
            $data = $userObj;
            if ($data['pos_id']) {
                $data = UsersService::getUserInfo($userObj['id']);
                if ($data['user_id'] != 0) {
                    UsersService::updateLogin($userObj['id'], 1);
                    $data['guid_info'] = EmployeeService::getGuidInfo($data['phone']);
                    return $this->success('success', $data, $data['user_id'], $openId);
                }

                return $this->success('success', $data);


            }
            return $this->success('success', $data);


        } catch (Exception $e) {
            Log::error($e);
            return response()->errorAjax($e);
        }
    }


    // 小程序用户登录
    public function signinTest(Request $request)
    {
        try {
            $signature = '6b763cbefb878718ba1f772c41e09472c630051a';
            $rawData = '{"nickName":"/暖暖的燃烧着","gender":1,"language":"zh_CN","city":"","province":"","country":"Algeria","avatarUrl":"https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJxoV3C6H94w0MYgZEYhPsCVdUbKN96hsQT4Pic9ETuScXNU45Bibu6whD23gt8otA0thECF3YrD3Bw/132"}';
            $responseData['session_key'] = 'jTzUR6BDXv6rrXh/yg17WA==';
            if ($signature != sha1($rawData . $responseData['session_key'])) {
//
                throw new Exception('验签失败', 0);
            }
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }


    // 解密获取微信手机号
    public function getPhoneNumber(Request $request)
    {
        try {

            $param = $request->all();
            $v = Validator::make($param, [
                'code' => 'required',
                'iv' => 'required',
                'encryptedData' => 'required'
            ]);

            if ($v->fails()) {
                return $this->error('授权信息不完整，请重新授权');

            }
            $code = $request->input('code');
            $iv = $request->input('iv');
            $encryptedData = $request->input('encryptedData');
            $appid = config('wechat.mini_program.app_id');
            $appSecret = config('wechat.mini_program.secret');

            $postUrl = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=' . $appid . '&secret=' . $appSecret . '&js_code=' . $code;

            $response = http_request($postUrl, false, false, 'GET', '小程序用户登录：');
            $responseData = json_decode($response['data'], true);
            if ($response['httpCode'] != 200) {
                throw new Exception("获取openid失败", 0);
            }
            if (isset($responseData['errcode']) && $responseData['errcode'] === 40163) {
                throw new Exception("code 已被使用，请更换", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] === 40029) {
                throw new Exception("code 无效", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] == -1) {
                throw new Exception("系统繁忙，请重试", 0);
            } else {


                $userifo = new \WXBizDataCrypt($appid, $responseData['session_key']);

                $errCode = $userifo->decryptData($encryptedData, $iv, $data);

                if ($errCode == '-41003') {
                    throw new Exception('不合法的 session_key', 0);
                }


                $info = json_decode($data, true);


                if (!$info) {
                    throw new Exception("解密失败", 0);
                }

                logger('微信解密手机号：', $info);

                $phone = $info['purePhoneNumber'];

                if ($errCode == 0) {

                    return $this->success('成功', ['mobile' => $phone]);

                } else if ($errCode == '-41003') {
                    throw new Exception('不合法的 session_key', 0);
                } else {
                    throw new Exception("解密手机号错误！", 0);
                }
            }
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
                'code' => 'required',
                'iv' => 'required',
                'encryptedData' => 'required',
                'open_id' => 'required',
            ]);

            if ($v->fails()) {
                return $this->error('授权信息不完整，请重新授权');

            }
            $code = $request->input('code');
            $iv = $request->input('iv');
            $encryptedData = $request->input('encryptedData');
            $appid = config('wechat.mini_program.app_id');
            $appSecret = config('wechat.mini_program.secret');

            $postUrl = 'https://api.weixin.qq.com/sns/jscode2session?grant_type=authorization_code&appid=' . $appid . '&secret=' . $appSecret . '&js_code=' . $code;

            $response = http_request($postUrl, false, false, 'GET', '小程序用户登录：');
            $responseData = json_decode($response['data'], true);
            if ($response['httpCode'] != 200) {
                throw new Exception("获取openid失败", 0);
            }
            if (isset($responseData['errcode']) && $responseData['errcode'] === 40163) {
                throw new Exception("code 已被使用，请更换", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] === 40029) {
                throw new Exception("code 无效", 0);
            } else if (isset($responseData['errcode']) && $responseData['errcode'] == -1) {
                throw new Exception("系统繁忙，请重试", 0);
            } else {


                $userifo = new \WXBizDataCrypt($appid, $responseData['session_key']);

                $errCode = $userifo->decryptData($encryptedData, $iv, $data);

                if ($errCode == '-41003') {
                    throw new Exception('不合法的 session_key', 0);
                }


                $info = json_decode($data, true);


                if (!$info) {
                    throw new Exception("解密失败", 0);
                }

                logger('微信解密手机号：', $info);

                $phone = $info['purePhoneNumber'];
                $is_first = 0;
                if ($errCode == 0) {

                    if (!Users::where('phone', $phone)->exists()) {
                        $load_user = UsersService::loadOtherUser($phone, 1);

                        if (!$load_user) {

                            return $this->success('手机号不存在，请注册', [
                                'need_register' => 1,
                                'phone' => $phone,
                            ]);
                        }
                        $is_first = 1;
                    }
                    $userInfo = Users::where('phone', $phone)->first();

                    Users::where('phone', $phone)->update([
                        'open_id' => $param['open_id'],
                    ]);
                    socialUsers::where('open_id', $param['open_id'])->update([
                        'user_id' => $userInfo['id'],
                    ]);

                    if ($userInfo['open_id']) {
                        if ($is_first == 1 or (empty($userInfo['updated_at']) && $userInfo['source_type'] == 0)) {
                            $client = new Http();
                            $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $userInfo['user_id']]);
                        }
                        $data = UsersService::getUserInfo($userInfo['id']);
                        UsersService::updateLogin($userInfo['id'], 1);
                        if ($data['user_id'] != 0) {
                            $data['guid_info'] = EmployeeService::getGuidInfo($data['phone']);
                            return $this->success('成功', $data, $data['user_id'], $param['open_id']);
                        }
                        return $this->success('成功', $data);

                    }


                } else if ($errCode == '-41003') {
                    throw new Exception('不合法的 session_key', 0);
                } else {
                    throw new Exception("解密手机号错误！", 0);
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return response()->errorAjax($e);
        }

    }

    public function bingUser(Request $request)
    {

        $param = $request->all();
        $v = Validator::make($param, [
            'phone' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'birthday' => 'required|date',
            'open_id' => 'required',
            'password' => 'required|string|min:6',
            'name' => [
                'required',
                'string',
                'max:20',
                'regex:/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u'
            ],

        ], [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
            'birthday.required' => '生日不能为空',
            'birthday.date' => '生日格式不正确',
            'password' => '密码最少为6位字符',
            'open_id' => 'open_id必填',
            'name.required' => '不能为空，必须为汉字或字母',
            'name.max' => '长度不能超过20位字符',
            'name.regex' => '仅支持汉字或字母，不支持特殊字符、数字、空格',

        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());

        }
        $parent_id = $request->get('parentid', 0);
        $extra['from_activity'] = $request->get('activityChannel', null);
        $extra['from_entrance'] = $request->get('entrance', null);
        list($success, $message, $data) = UsersService::registerMiniApp($param['phone'], $param['password'], $param['birthday'], $param['name'], $param['sex'], $param['open_id'], $parent_id,$extra);
        if ($success) {
            return $this->success('success', $data, $data['user_id'], $param['open_id']);
        }

        return $this->error($message);


    }


    /**
     * 登录
     * @param Request $request
     * @return mixed
     */
    public function wxLogin(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'open_id' => 'required',
            'account' => 'required_with:password|required_without:phone',
            'password' => 'required_with:account|string|min:6',
            'phone' => 'required_with:msg_code|required_without:account',
            'phone' => array('regex:/^1(3|4|5|6|7|8|9)\d{9}$/'),
            'msg_code' => 'required_with:phone|numeric|min:6',

        ], [
            'password.string' => '密码是字符串',
            'password.min' => '密码最短不能低于6个字符',
            'phone.regex' => '手机号格式不正确',
            'msg_code.numeric' => '验证码是6位数字',
            'msg_code.min' => '验证码最短不能低于6个字符',


        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());

        }
        $channel = $request->header('from', 1);
        $redis = Redis::getRedis();
        $key = config('app.name').':login:lock:';
        $openid = $param['open_id'];
        if (isset($param['phone'])) {
            $fail_mark = UsersService::getFailMark($param['phone']);
            if ($fail_mark) {
                if (!isset($param['valid_code']) || !isset($param['valid_id'])) {
                    return $this->error('请填写图形验证码', ['fail_mark' => 1]);
                }
                $captcha = new CaptchaApi;
                $success = $captcha::checkCaptch($param['valid_code'], $param['valid_id']);
                if ($success < 0) {
                    return $this->error('图形验证码已失效，请换一张图形验证码', ['fail_mark' => 1]);
                }
                if (!$success) {
                    return $this->error('图形验证验证失败', ['fail_mark' => 1]);
                }

            }
            if (!$redis->set($key . $param['phone'], 1, 'ex', 1, 'nx')) return $this->error('请求过于频繁');
            list($success, $message, $data) = UsersService::loginByphone($param['phone'], $param['msg_code'], $channel, $openid);

        } else {


            if (!$redis->set($key . $param['account'], 1, 'ex', 1, 'nx')) return $this->error('请求过于频繁');

            $pattern = "/^1(3|5|4|5|6|7|8|9)\d{9}$/";
            if (preg_match($pattern, $param['account'], $match)) {
                //手机号
                list($success, $message, $data) = UsersService::loginByPassword($param['account'], $param['password'], 1, $openid, $channel);

            } else {
                $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                if (preg_match($pattern, $param['account'], $match)) {
                    list($success, $message, $data) = UsersService::loginByPassword($param['account'], $param['password'], 2, $openid, $channel);
                } else {
                    return $this->error('登录账号必须为手机号或邮箱');
                }
            }
        }


        if ($success) {
            if ($data['user_id'] != 0) {
                $data['guid_info'] = EmployeeService::getGuidInfo($data['phone']);
                return $this->success('success', $data, $data['user_id'], $data['open_id']);
            }
            return $this->success('success', $data);
        }


        return $this->error($message, $data);
    }


}