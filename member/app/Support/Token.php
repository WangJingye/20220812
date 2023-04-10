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
     * 用户登录成功创建Token
     * @param $uid
     * @return array|bool
     */
    public static function createToken($uid, $openid = '')
    {
        if (intval($uid) < 1) {
            return false;
        }

        $redis = self::getRedis();
        $info = [
            'uid' => $uid,
            'openid' => $openid,
            'createTime' => time()
        ];

        //加密
        $data['token'] = encrypt($info);
        $data['refresh_token'] = encrypt($info);
        if ($openid) {
            //3天
            $redis->setex('token.' . $uid . 'miniapp', 31104000000, $data['token']);
            //7天
            $redis->setex('refresh.token.' . $uid . 'miniapp', 31104000000 * 2, $data['refresh_token']);
        } else {
            //3天
            $redis->setex('token.' . $uid, 259200, $data['token']);
            //7天
            $redis->setex('refresh.token.' . $uid, 604800, $data['refresh_token']);
        }
        return $data;
    }

    public static function createTokenByOpenId($uid, $openid){
        $data = self::createToken($uid,$openid);
        return $data['token'];
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

    public static function getUidByToken($token)
    {
        if (!$token) return false;
        $decrypted_data = decrypt($token);

        if (!array_key_exists('uid', $decrypted_data)) {
            return false;
        }

        $uid = $decrypted_data['uid'] ?? 0;
        //检查token是否失效
        $key = 'token.' . $uid . 'miniapp';
        $redis = self::getRedis();
        $curr_token = $redis->get($key);
        if($curr_token && $curr_token==$token){
            return $uid;
        }
        return false;
    }

    public static function getInfoByToken($token)
    {
        if (!$token) return false;
        return decrypt($token);
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
        $redis->del('token.' . $uid.'miniapp');
        $redis->del('refresh.token.' . $uid.'miniapp');
    }

}