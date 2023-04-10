<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\SocialUsers;
use App\Service\UsersService;
use Overtrue\Socialite\SocialiteManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Support\Token;
use Validator;
use App\Support\CaptchaApi;

class SocialCallbackController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     * @api {get} /get/captcha
     * @apiName 获取图片验证码base64
     * @apiGroup 用户
     */
    public function sso($socialite_name, Request $request)
    {

        try {
            $config = config('socialite');
            $request_state = $request->get('state');
            $state = Redis::get($socialite_name . '_state' . $request_state);
            $state_info = explode('|', $state);
            $return_url = $state_info[1];
            Log::info('sso_request'.'return_url'.$return_url, $request->all());

            if ($socialite_name == 'alipay') {
                $auth = new \Yurun\OAuthLogin\Alipay\OAuth2($config['alipay']['client_id'], $config['alipay']['client_secret'], $config['alipay']['redirect']);
                $auth->appPrivateKey = $config['alipay']['secret_key'];
                $accessToken = $auth->getAccessToken($state_info[0], $request->get('auth_code'), $request_state);
                $userInfo = $auth->getUserInfo($accessToken);
                if (!$userInfo) {
                    header("Location: " . env('WEB_URL'));
                    exit;
                }
                $open_id = $userInfo['user_id'];
                $sso_code = $request->get('auth_code');
            }
            if ($socialite_name == 'wechat') {
                $auth = new \Yurun\OAuthLogin\Weixin\OAuth2($config['wechat']['client_id'], $config['wechat']['client_secret'], $config['wechat']['redirect']);
                $accessToken = $auth->getAccessToken($state_info[0], $request->get('code'), $request_state);
                $userInfo = $auth->getUserInfo($accessToken);
                if (!$userInfo) {
                    header("Location: " . env('WEB_URL'));
                    exit;
                }
                $open_id = $userInfo['openid'];
                $sso_code = $request->get('code');
            }


            $social_info = socialUsers::where('open_id', $open_id)->first();
            Redis::set($sso_code, $open_id, 'ex', 60 * 60 * 12);
            if ($social_info) {
                if ($social_info['user_id']) {

//                    $data = UsersService::getUserInfo($social_info['user_id']);
//                    if ($data['is_complete'] == 1) {
//                        setcookie("sso_code", $sso_code, time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
//                        setcookie("uid", $social_info['user_id'], time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
//                        header("Location: " . env('WEB_URL') . '/improve');
//                        exit;
//                    }

                    $token_info = Token::createToken($social_info['user_id']);
                    setcookie("sso_code", $sso_code, time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
                    setcookie("token", $token_info['token'], time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
                    setcookie("refresh_token", $token_info['refresh_token'], time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
                    header("Location: " .  env('WEB_URL').$return_url);
                    exit;


                }
                // 如果用户未绑定用户信息 提示绑定用户信息

                setcookie("sso_code", $sso_code, time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
                header("Location: " . env('WEB_URL') . '/improve');
                exit;
            }
            $social = socialUsers::create([
                'social_type' => $socialite_name,
                'open_id' => $open_id,
                'social_info' => json_encode($userInfo)
            ]);

            if ($social) {
                setcookie("sso_code", $sso_code, time() + 3600 * 24 * 30, "/", env('WEB_DOMAIN'));
                header("Location: " . env('WEB_URL') . '/improve');
                exit;
            }

            header("Location: " . env('WEB_URL'));
            exit;
        } catch (\Exception $e) {

            Log::error('sso_request'.$e);
            header("Location: " . env('WEB_URL'));
            exit;
        }

    }

    public function getSocialite($socialite_name, Request $request)
    {

        $config = config('socialite');
        $return_url = $request->get('returnUrl');

        if ($socialite_name == 'wechat') {
            $auth = new \Yurun\OAuthLogin\Weixin\OAuth2($config['wechat']['client_id'], $config['wechat']['client_secret'], $config['wechat']['redirect']);
        }

        if ($socialite_name == 'alipay') {
            $auth = new \Yurun\OAuthLogin\Alipay\OAuth2($config['alipay']['client_id'], $config['alipay']['client_secret'], $config['alipay']['redirect']);
        }
        $url = $auth->getAuthUrl();
        Redis::set($socialite_name . '_state' . $auth->state, $auth->state . '|' . $return_url, 'ex', 60 * 60 * 12);
        header('location:' . $url);

    }

    /**
     * @api {post} /social/login
     * @apiName 社交账号绑定手机号
     * @apiGroup 用户
     *
     * @apiDescription  绑定微信
     * @apiParam {String}   phone 手机号
     * @apiParam {int}      msg_code 短信验证码
     */
    public function socialLogin(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'phone' => array('regex:/^1(3|4|5|6|7|8|9)\d{9}$/'),
            'msg_code' => 'required_with:phone|numeric|min:6',
            'valid_id' => 'required',
            'valid_code' => 'required',

        ], [
            'phone.regex' => '手机号格式不正确',
            'msg_code.numeric' => '验证码是6位数字',
            'msg_code.min' => '验证码最短不能低于6个字符',
            'valid_id' => '图形验证码必填',
            'valid_code' => '图形验证码必填',


        ]);

        if ($v->fails()) {

            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }

        $param['sso_code'] = $request->get('sso_code', '');

        $open_id = '';
        if($param['sso_code']){
            $open_id = Redis::get($param['sso_code']);
        }
        if(!$open_id){
            return $this->error('社交信息失效请重新授权',[]);
        }
        $captcha = new CaptchaApi;
        $success = $captcha::checkCaptch($param['valid_code'], $param['valid_id']);
        if ($success < 0) {
            return $this->error('图形验证码已失效，请换一张图形验证码', [
                'fail_mark' => 1,
                'field' => 'valid_code',
            ]);
        }

        if (!$success) {
            return $this->error('图形验证码校验失败', [
                'fail_mark' => 1,
                'field' => 'valid_code',
            ]);
        }


        $key = config('app.name').':social:lock:';


        if (!Redis::set($key . $param['phone'], 1, 'ex', 5, 'nx')) return $this->error('请求过于频繁', [
            'field' => 'phone',
        ]);

        //验证短信
        list($success, $message) = UsersService::checkMsgCode($param['phone'], $param['msg_code'], 5);
        if (!$success) {
            return [false, $message, [
                'field' => 'msg_code',
                'fail_mark' => 1,
            ]];
        }
        list($success, $message, $data) = UsersService::loginBySocial($param['phone'],$open_id);

        if ($success) {
            if ($data['user_id'] != 0) {
                return $this->success('success', $data, $data['user_id']);
            }
            return $this->success('success', $data);
        }


        return $this->error($message, $data);
    }

}