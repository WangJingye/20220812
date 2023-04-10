<?php namespace App\Support;

use Illuminate\Support\Facades\Redis;
use Exception;

class Token
{
    public static function getRedis()
    {
        return Redis::connection('default');
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