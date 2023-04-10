<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\{RegisterRequest,LoginRequest,ForgotRequest,ChangePasswordRequest,MobileRequest,SmsRequest};
use App\Repositories\{UserRepository};
use App\Exceptions\ApiPlaintextException;
use Gregwar\Captcha\CaptchaBuilder;

class UserController extends ApiController
{
    /**
     * @var \App\Services\Api\UserServices
     */
    public $userServices;
    public function __construct(){
        $this->userServices = app('UserServices');
    }

    /**
     * 用户注册
     * @param RegisterRequest $request
     * @return array
     * @throws \Exception
     */
    public function register(RegisterRequest $request){
        $api = app('ApiRequestMagento');
        //短信验证
        $code = $request->get('code');
        $session_id = $this->getCookieId();
        if(!$this->userServices->checkSmsCode($session_id,$code)){
            throw new ApiPlaintextException('验证码错误');
        }
        //创建用户
        $customer = array_filter([
            'email'=>$request->get('mobile').'@magento.com',
            'firstname'=>$request->get('username'),
            'lastname'=>$request->get('username'),
            'gender'=>$request->get('sex'),
            'dob'=>$request->get('birth'),
        ]);
        $resp = $api->exec(['url'=>'V1/connext/customers','method'=>'POST'],[
            'customer'=>$customer,
            'password'=>$request->get('password'),
        ]);
        $resp = json_decode($resp);
        if($resp->code == 1){
            $token = UserRepository::setToken($resp->cutomer_id);
            //用户数据放入缓存
            UserRepository::setUserInfo($resp->cutomer_id,$customer);
            return $this->success(compact('token'));
        }elseif($resp->code == 0){
            throw new ApiPlaintextException($resp->message);
        }throw new \Exception('注册失败');
    }

    /**
     * 用户登录
     * @param LoginRequest $request
     * @return array
     * @throws \Exception
     */
    public function login(LoginRequest $request){
        //1为验证码登录，2为密码登录
        $type = $request->get('type');
        $mobile = $request->get('mobile');
        $code = $request->get('code');
        if($type==1){
            //短信验证
            $session_id = $this->getCookieId();
            if(!$this->userServices->checkSmsCode($session_id,$code)){
                throw new ApiPlaintextException('验证码错误');
            }
            $uid = $this->userServices->getUidByUsername($mobile);
            if(empty($uid))throw new ApiPlaintextException('手机号不正确');
        }elseif($type==2){
            $userInfo = $this->userServices->getUserInfoByUsername($mobile);
            if($userInfo){
                if($this->userServices->passwordHashVerify($code,$userInfo->password_hash)){
                    $uid = $userInfo->entity_id;
                }
            }if(empty($uid))throw new ApiPlaintextException('用户名或密码错误');
        }else{
            throw new \Exception('错误的type');
        }
        $token = UserRepository::setToken($uid);
        return $this->success(compact('token'));
    }

    /**
     * 登出
     * @param Request $request
     * @return array
     */
    public function logout(Request $request){
        $token = $request->header('token');
        UserRepository::delToken($token);
        return $this->success();
    }

    /**
     * 获取个人信息
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getProfile(Request $request){
        $userInfo = $this->userServices->getUserInfoByIdCache($this->getUid());
        if($userInfo){
            return $this->success([
                'username'=>array_get($userInfo,'firstname'),
                'gender'=>array_get($userInfo,'gender'),
                'birthday'=>array_get($userInfo,'dob'),
                'mobile'=>array_get($userInfo,'mobile'),
                'email'=>array_get($userInfo,'real_email'),
            ]);
        }throw new ApiPlaintextException('获取个人信息失败');
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function updateProfile(Request $request){
        $uid = $this->getUid();
        $customerData = array_filter([
            'firstname'=>$request->get('username'),
            'gender'=>$request->get('gender'),
            'dob'=>$request->get('birthday'),
            'real_email'=>$request->get('email'),
        ]);
        if($customerData){
            $api = app('ApiRequestMagento');
            $resp = $api->exec(['url'=>"V1/connext/customers/{$uid}",'method'=>'POST'],compact('customerData'));
            $resp = json_decode($resp);
            if($resp->code == 1){
                //用户数据放入缓存
                UserRepository::setUserInfo($uid,$customerData);
                return $this->success();
            }elseif($resp->code == 0){
                throw new ApiPlaintextException($resp->message);
            }
        }throw new \Exception('更新失败');
    }

    /**
     * 忘记密码
     * @param ForgotRequest $request
     * @return array
     * @throws \Exception
     */
    public function forgot(ForgotRequest $request){
        $mobile = $request->get('mobile');
        //短信验证
        $code = $request->get('code');
        $session_id = $this->getCookieId();
        if(!$this->userServices->checkSmsCode($session_id,$code)){
            throw new ApiPlaintextException('验证码错误');
        }
        //更改密码
        $this->userServices->setPasswordByMobile($mobile,$request->get('password'));
        return $this->success();
    }

    /**
     * 更新密码
     * @param ChangePasswordRequest $request
     * @return array
     * @throws \Exception
     */
    public function changePassword(ChangePasswordRequest $request){
        $uid = $this->getUid();
        if($this->userServices->checkPassword($uid,$request->get('oldPwd'))){
            $this->userServices->setPasswordById($uid,$request->get('newPwd'));
            return $this->success();
        }throw new ApiPlaintextException('密码不正确');
    }

    /**
     * 发送短信
     * @param SmsRequest $request
     * @return array
     * @throws \Exception
     */
    public function sendSMS(SmsRequest $request){
        $mobile = $request->get('mobile');
        $imgCode = $request->get('imgCode');
        $session_id = $this->getCookieId();
        //验证图形验证码
        $session_code = $this->userServices->getImgCode($session_id);
        if(empty($session_code)){
            throw new ApiPlaintextException('请重新获取验证码');
        }
        if(strtolower($session_code) != strtolower($imgCode)){
            throw new ApiPlaintextException('图形验证码错误');
        }
        //发送短信
        /** @var \App\Services\Sms\Alibaba $sms */
        $sms = app('Sms');
        $code = rand(100000,999999);
        $this->userServices->setSmsCode($session_id,$code);
        if($sms->register($mobile,compact('code'))){
            //成功后清除图形验证码记录
            $this->userServices->delImgCode($session_id);
            return $this->success();
        }throw new ApiPlaintextException('发送验证码失败');
    }

    /**
     * 获取验证码
     * @param Request $request
     */
    public function captcha(Request $request){
        $builder = new CaptchaBuilder;
        $builder->build($width = 100, $height = 40, $font = null);
        //获取并保存验证码的内容
        $session_id = $this->getCookieId();
        $this->userServices->setImgCode($session_id,$builder->getPhrase());
        header("Cache-Control: no-cache, must-revalidate");
        header('Content-Type: image/jpeg');
        $builder->output();exit;
    }


}
