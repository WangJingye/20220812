<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UserRepository
{
    const TOKEN = '_UserToken';

    /**
     * 设置登录token
     * @param $uid
     * @return string
     */
    public static function setToken($uid){
        $token = md5(uniqid());
        $key = env('APP_NAME').self::TOKEN.':'.$token;
        Redis::SET($key,$uid);
        //token有效期为4小时
        $expireTime = strtotime(date("Y-m-d H:i:s"))+3600*4;
        Redis::EXPIREAT($key, $expireTime);
        return $token;
    }

    /**
     * 登出删除token
     * @param $token
     * @return mixed
     */
    public static function delToken($token){
        $key = env('APP_NAME').self::TOKEN.':'.$token;
        return Redis::DEL($key);
    }

    /**
     * 通过token获取用户ID
     * @param $token
     * @return mixed
     */
    public static function getUidByToken($token){
        $key = env('APP_NAME').self::TOKEN.':'.$token;
        //访问后重置token有效时间
        $expireTime = strtotime(date("Y-m-d H:i:s"))+3600*4;
        Redis::EXPIREAT($key, $expireTime);
        return Redis::GET($key);
    }

    const INFO = '_UserInfo';

    /**
     * 保存用户信息
     * @param $uid
     * @param $info
     */
    public static function setUserInfo($uid,$info){
        $key = env('APP_NAME').self::INFO.':'.$uid;
        Redis::HMSET($key,$info);
        //有效期为3天
        $expireTime = strtotime(date("Y-m-d H:i:s"))+3600*24*3;
        Redis::EXPIREAT($key, $expireTime);
    }

    /**
     * 获取用户信息
     * @param $uid
     * @return mixed
     */
    public static function getUserInfo($uid){
        $key = env('APP_NAME').self::INFO.':'.$uid;
        return Redis::HGETALL($key);
    }


    /**
     * DB获取用户信息
     * @param array $condition
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getDBUserInfo(array $condition){
        $model = DB::table('customer_entity')->where($condition);
        return $model->first();
    }

    /**
     * DB更新用户信息
     * @param array $condition
     * @param array $update
     * @return int
     */
    public static function setDBUserInfo(array $condition, array $update){
        return DB::table('customer_entity')->where($condition)->update($update);
    }

    /**
     * 储存验证码
     * @param $category
     * @param $key
     * @param $code
     * @return bool
     */
    public static function setCode($category,$key,$code){
        $key = env('APP_NAME').'_'.$category.':'.$key;
        Redis::SET($key,$code);
        //有效期为4分钟
        $expireTime = strtotime(date("Y-m-d H:i:s"))+240;
        Redis::EXPIREAT($key, $expireTime);
        return true;
    }

    /**
     * 获取验证码
     * @param $category
     * @param $key
     * @return mixed
     */
    public static function getCode($category,$key){
        $key = env('APP_NAME').'_'.$category.':'.$key;
        return Redis::GET($key);
    }

    /**
     * 删除验证码
     * @param $category
     * @param $key
     * @return bool
     */
    public static function delCode($category,$key){
        $key = env('APP_NAME').'_'.$category.':'.$key;
        if(Redis::EXISTS($key)){
            return Redis::DEL($key);
        }return false;
    }
}
