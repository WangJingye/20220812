<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\UsersService;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\MessageBag;
use Exception;
use Response;
use App\Support\CaptchaApi;
use Illuminate\Support\Facades\Redis;
use App\Support\Token;

class CustomerLoginController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     * @api {get} /get/captcha
     * @apiName 获取图形验证码base64
     * @apiGroup 用户
     */
    public function getCaptcha(Request $request)
    {
        $captcha = new CaptchaApi;
        $data = $captcha::getCaptcha();
        return $this->success('success', $data);

    }

    /**
     * @param Request $request
     * @return mixed
     * @api {post} /check/captcha
     * @apiName 验证图形验证码
     * @apiGroup 用户
     */
    public function checkCaptcha(Request $request)
    {
        $v = Validator::make($request->all(), [
            'valid_id' => 'required',
            'valid_code' => 'required',

        ], [

            'valid_code.required' => '请输入图形验证码',

        ]);

        if ($v->fails()) {

            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }


        $captcha = new CaptchaApi;
        $success = $captcha::checkCaptch($request->get('valid_code'), $request->get('valid_id'));
        if ($success < 0) {
            return $this->error('图形验证码已失效，请换一张图形验证码', [
                'field' => 'valid_code',
            ]);
        }

        if ($success) {
            return $this->success('验证成功');
        }
        return $this->error('图形验证码校验失败', [
            'field' => 'valid_code',
        ]);
    }





    /**
     * @api {get} /user/exist
     * @apiName 验证用户是否存在
     * @apiGroup 用户
     *
     * @apiDescription
     * @apiParam {String}   phone 手机号
     */
    public function userExist(Request $request)
    {
        $v = Validator::make($request->all(), [
            'phone' => 'required',
            'phone' => array('regex:/^1(3|4|5|6|7|8|9)\d{9}$/'),
        ], [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',

        ]);

        if ($v->fails()) {
            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }
        $redis = Redis::connection('default');
        $key = config('app.name').':login:lock:';
        if (!$redis->set($key . $request->phone, 1, 'ex', 3, 'nx')) return $this->error('请求过于频繁', [
            'field' => 'phone',
        ]);

        $exist = UsersService::checkUserExist($request->get('phone'));
        if ($exist) {

            return $this->error('此号码已注册过Sisley会员，请登录', [
                'field' => 'phone',
            ]);
        }

        return $this->success('用户不存在');
    }

    /**
     * @api {post} /send/msg
     * @apiName 发送短信
     * @apiGroup 用户
     *
     * @apiDescription
     * @apiParam {String}   phone 手机号
     * @apiParam {int}      msg_code 短信验证码
     */
    public function sendMsg(Request $request)
    {

        $v = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'type' => 'required',

        ], [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',

        ]);
        $param = $request->all();
        if ($v->fails()) {
            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }

        $fail_mark = UsersService::getSendMsgMark($request->phone);
        if ($fail_mark) {
            if (!isset($param['valid_code']) || !isset($param['valid_id'])) {
                return $this->error('请填写图形验证码', [
                    'fail_mark' => 1,
                    'field' => 'valid_code',
                ]);
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

        }
        list($success, $message, $data) = UsersService::sendMessage($request->phone, $request->type);
        //todo 手机号发送服务
        if ($success) {
            return $this->success($message);
        }
        if (isset($data['tag']) && $data['tag'] == 1) {
            return Response::json(['code' => 3, 'message' => $message, 'data' => $data]);
        }
        return $this->error($message, $data);
    }

    /**
     * @api {get} /social/list
     * @apiName 社交登录列表
     * @apiGroup 用户
     */
    public function socialList()
    {
        $social_list = array(
            [
                'name' => 'wechat_web',
                'appid' => 'wx23423423423',

            ],
            [
                'name' => 'weibo',
                'appid' => 'wb23423423423',

            ],
            [
                'name' => 'qq',
                'appid' => 'qq23423423423',

            ],
        );

        return $this->success('success', $social_list);
    }

    /**
     * @api {post} /user/register
     * @apiName 注册
     * @apiGroup 用户
     */
    public function registerUser(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'phone' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'msg_code' => 'required|digits:6',
            'birthday' => 'required|date',
            'valid_id' => 'required',
            'valid_code' => 'required',
            'email' => 'email',
            'sex' => 'required',
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
            'msg_code.required' => '短信验证不能为空',
            'msg_code.digits' => '短信验证码必须为6位数字',
            'birthday.required' => '生日不能为空',
            'birthday.date' => '生日格式不正确',
            'password' => '密码最少为6位字符',
            'name.required' => '姓名不能为空，必须为汉字或字母',
            'name.max' => '长度不能超过20位字符',
            'name.regex' => '仅支持汉字或字母，不支持特殊字符、数字、空格',
            'email' => '邮箱格式错误',
            'valid_id' => '图形验证码必填',
            'valid_code' => '图形验证码必填',

        ]);


        if ($v->fails()) {
            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }
        $open_id = '';
        $param['sso_code'] = $request->get('sso_code', '');
        $redis = Redis::connection('default');
        if($redis->exists($param['sso_code']) && $param['sso_code']){
            $open_id = $redis->get($param['sso_code']);
        }

        $captcha = new CaptchaApi;
        $success = $captcha::checkCaptch($request->get('valid_code'), $request->get('valid_id'));
        if ($success < 0) {
            return $this->error('图形验证码已失效，请换一张图形验证码', [
                'field' => 'valid_code',
            ]);
        }

        if (!$success) {

            return $this->error('图形验证码校验失败', [
                'field' => 'valid_code',
            ]);
        }

        $f = $redis->setnx("lock_register" . $param['phone'],date("Y-m-d H:i:s"));
        if ($f != 1) {
            $data['field'] = 'phone';
            return $this->error('请求频繁', $data);
        }
        $redis->expire("lock_register" . $param['phone'], 3);



        $param['email'] = $request->get('email', '');
        $param['channel'] = $request->header('from', 1);

        $parent_id = $request->get('parentid', 0);


        $extra['from_activity'] = $request->get('activityChannel', null);
        $extra['from_entrance'] = $request->header('entrance', null);

        list($success, $message, $data) = UsersService::register($param['phone'], $param['msg_code'], $param['password'], $param['birthday'], $param['sex'], $param['name'], $param['email'], $param['channel'], $open_id, $parent_id,$extra);
        if ($success) {

            return $this->success('success', $data, $data['user_id']);
        }

        return $this->error($message, $data);
    }

    /**
     * 重置登录密码
     * @param Request $request
     * @return mixed
     */
    public function setPassword(Request $request)
    {

        $v = Validator::make($request->all(), [
            'phone' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'password' => 'required|string|min:6',
            'msg_code' => 'required|digits:6',

        ], [
            'phone.required' => '手机号不能为空',
            'phone.regex' => '手机号格式不正确',
            'msg_code.required' => '短信验证不能为空',
            'msg_code.digits' => '短信验证码必须为6位数字',
            'password.min' => '密码最少为6位字符',
            'password.required' => '密码最少为6位字符',

        ]);

        if ($v->fails()) {
            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }
        //验证短信
        list($success, $message, $data) = UsersService::checkMsgCode($request->get('phone'), $request->get('msg_code'), 3);
        if (!$success) {
            return $this->error($message, $data);
        }
        $success = UsersService::changePassword($request->get('phone'), $request->get('password'));
        if (!$success) {
            return $this->error('密码修改失败，请重试');
        }
        return $this->success('修改成功，请重新登录');
    }

    /**
     * 登录
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
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

            $data['field'] = $v->getMessageBag()->keys()[0];
            return $this->error($v->errors()->first(), $data);

        }


        if (isset($param['account']) && !empty($param['account'])) {
            $account = $param['account'];
        } else {
            $account = $param['phone'];
            $fail_mark = UsersService::getFailMark($account);
            if ($fail_mark) {
                if (!isset($param['valid_code']) || !isset($param['valid_id'])) {
                    return $this->error('请填写图形验证码', [
                        'fail_mark' => 1,
                        'field' => 'valid_code',
                    ]);
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

            }

        }


        $redis = Redis::connection('default');
        $key = config('app.name').':login:lock:';
        $channel = $request->header('from', 1);
        if (isset($param['phone'])) {
            if (!$redis->set($key . $param['phone'], 1, 'ex', 5, 'nx')) return $this->error('请求过于频繁', [
                'field' => 'phone',
            ]);

            list($success, $message, $data) = UsersService::loginByphone($param['phone'], $param['msg_code'], $channel);

        } else {


            if (!$redis->set($key . $param['account'], 1, 'ex', 5, 'nx')) return $this->error('请求过于频繁', [
                'field' => 'account',
            ]);

            $pattern = "/^1(3|5|4|5|6|7|8|9)\d{9}$/";
            if (preg_match($pattern, $param['account'], $match)) {
                //手机号
                list($success, $message, $data) = UsersService::loginByPassword($param['account'], $param['password'], 1, '', $channel);

            } else {
                $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                if (preg_match($pattern, $param['account'], $match)) {
                    list($success, $message, $data) = UsersService::loginByPassword($param['account'], $param['password'], 2, '', $channel);
                } else {
                    return $this->error('登录账号必须为手机号或邮箱', [
                        'field' => 'account',
                    ]);
                }
            }
        }


        if ($success) {
            if ($data['user_id'] != 0) {
                return $this->success('success', $data, $data['user_id']);
            }
            return $this->success('success', $data);
        }


        return $this->error($message, $data);
    }


    /**
     * 退出登录
     * @param Request $request
     * @return mixed
     */
    public function logout(Request $request)
    {
        if (!$request->user_id) {
            return $this->error("未登陆");
        }
//        Token::deleteToken($this->user_id);
        Token::deleteToken($this->user_id);
        return $this->success('success');
    }



}