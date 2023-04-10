<?php


namespace App\Support;

use Illuminate\Support\Facades\Redis;
use Exception;

class Token
{


    public static function getRedis()
    {
        return Redis::connection('default');
    }

    /**
     * 校验token
     * @param $uid
     * @param $token
     * @param $refresh_token
     * @return bool
     */
    public static function checkToken($token)
    {
        try {
            $decrypted_data = decrypt($token);
            if (!array_key_exists('uid', $decrypted_data)) {
                return false;
            }
            $uid = $decrypted_data['uid'];
            $openid = $decrypted_data['openid'];
            if ($openid) {
                $token_key = 'token.' . $uid . 'miniapp';
            } else {
                $token_key = 'token.' . $uid;
            }
            $redis = self::getRedis();
            $user_token = $redis->get($token_key);
            if ($token === $user_token) {
                return $uid;
            } else {
                $user_token = $redis->get($token_key);
                if ($user_token === $token) {
                    $redis->setex($token_key, 604800, $token);
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


}