<?php namespace App\Services\Api;

use App\Repositories\{UserRepository};

class UserServices
{
    public function passwordHash($password){
        return password_hash($password,PASSWORD_DEFAULT);
    }

    public function passwordHashVerify($password,$passwordHash){
        return password_verify($password,$passwordHash);
    }

    public function mobile2Email($mobile){
        return $mobile.'@magento.com';
    }

    public function email2Mobile($email){
        return rtrim($email,'@magento.com');
    }

    public function getUidByUsername($mobile){
        $email = $this->mobile2Email($mobile);
        $userInfo = UserRepository::getDBUserInfo(compact('email'));
        return object_get($userInfo,'entity_id');
    }

    public function getUserInfoByUsername($mobile){
        $email = $this->mobile2Email($mobile);
        return UserRepository::getDBUserInfo(compact('email'));
    }

    /**
     * 根据用户ID获取用户信息(Redis)
     * @param $entity_id
     * @return array|mixed
     */
    public function getUserInfoByIdCache($entity_id){
        //先从redis中获取
        $userInfo = UserRepository::getUserInfo($entity_id);
        //如果redis中不存在 则从mysql获取
        if(empty($userInfo)){
            $userInfo = (array)UserRepository::getDBUserInfo(compact('entity_id'));
            //将mysql中的数据保存到redis中
            UserRepository::setUserInfo($entity_id,$userInfo);
        }
        $userInfo['mobile'] = $this->email2Mobile($userInfo['email']);
        return $userInfo;
    }

    public function checkPassword($entity_id,$password){
        $userInfo = UserRepository::getDBUserInfo(compact('entity_id'));
        $passwordHash = object_get($userInfo,'password_hash');
        if($passwordHash){
            return $this->passwordHashVerify($password,$passwordHash);
        }return false;
    }

    public function setPasswordById($entity_id,$password){
        $password_hash = $this->passwordHash($password);
        return UserRepository::setDBUserInfo(compact('entity_id'),compact('password_hash'));
    }

    public function setPasswordByMobile($mobile,$password){
        $email = $this->mobile2Email($mobile);
        $password_hash = $this->passwordHash($password);
        return UserRepository::setDBUserInfo(compact('email'),compact('password_hash'));
    }

    public function setImgCode($session_id,$code){
        return UserRepository::setCode('ImgCode',$session_id,$code);
    }

    public function getImgCode($session_id){
        return UserRepository::getCode('ImgCode',$session_id);
    }

    public function delImgCode($session_id){
        return UserRepository::delCode('ImgCode',$session_id);
    }

    public function setSmsCode($session_id,$code){
        return UserRepository::setCode('SmsCode',$session_id,$code);
    }

    public function getSmsCode($session_id){
        return UserRepository::getCode('SmsCode',$session_id);
    }

    public function delSmsCode($session_id){
        return UserRepository::delCode('SmsCode',$session_id);
    }

    /**
     * 检查smsCode
     * @param $session_id
     * @param $code
     * @return bool|int
     */
    public function checkSmsCode($session_id,$code){
        //hardCode Start>>>>>>>>>>>>>>>>>,非生产环境7788通用验证码
        if($code == '7788'){
            if(env('APP_ENV')!='production'){
                $this->delSmsCode($session_id);
                return 1;
            }
        }
        //hardCode End<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $sessionCode = $this->getSmsCode($session_id);
        if(empty($sessionCode)){
            //验证码失效
            return 0;
        }elseif($sessionCode != $code){
            //验证码错误
            return false;
        }
        $this->delSmsCode($session_id);
        return 1;
    }
}
