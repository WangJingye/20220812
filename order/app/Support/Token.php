<?php


namespace App\Support;

use Illuminate\Support\Facades\Redis;
use Exception;

class Token
{
    public static function getRedis()
    {
        return Redis::connection('goods');
    }

    /**
     * 校验token
     * @param $uid
     * @param $token
     * @param $refresh_token
     * @return bool
     */
    public static function checkToken($token, $refresh_token)
    {
        try {
            $decrypted_data = decrypt($token);

            if (!array_key_exists('uid', $decrypted_data)) {
                return false;
            }

            $uid = $decrypted_data['uid'];
            $openid = $decrypted_data['openid'];

            if ($openid) {
                $token_key = 'refresh.token.' . $uid . 'miniapp';
                $refresh_token_key = 'refresh.token.' . $uid . 'miniapp';
            } else {
                $token_key = 'refresh.token.' . $uid;
                $refresh_token_key = 'refresh.token.' . $uid;
            }

//        if(empty($decrypted_data['uid'])) {  //为空，匿名登录
//            return false; // 登录
//        }
            $redis = self::getRedis();
            $user_token = $redis->get($token_key);
            if ($token === $user_token) {
                return $uid;
            } else {
                $user_token = $redis->get($refresh_token_key);
                if ($user_token === $refresh_token) {

                    $redis->setex($token_key, 259200, $token);
                    $redis->setex($refresh_token_key, 604800, $refresh_token);
                    return $uid;
                }

                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getUidByToken($token){
        if(!$token) return false;
        $decrypted_data = decrypt($token);

        if (!array_key_exists('uid', $decrypted_data)) {
            return false;
        }

        $uid = $decrypted_data['uid']??0;
        //检查token是否失效
        $key = 'token.' . $uid . 'miniapp';
        $redis = self::getRedis();
        $curr_token = $redis->get($key);
        if($curr_token && $curr_token==$token){
            return $uid;
        }
        return false;
    }

    public static function getOpenidByToken($token){
        if(!$token) return false;
        $decrypted_data = decrypt($token);

        if (!array_key_exists('openid', $decrypted_data)) {

            return false;
        }

        $uid = $decrypted_data['openid']??0;
        return $uid;
    }

    /**
     * 删除用户token
     * @param $uid
     */
    public static function deleteToken($uid)
    {
        $redis = self::getRedis();
        $redis->del('token.' . $uid);
        $redis->del('refresh.token.' . $uid);
    }

}
