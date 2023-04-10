<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis as RedisClient;

class Redis
{

    public static $redis = null;

    public static function getRedis($db = 'default'){
        if(empty(self::$redis[$db])){
            self::$redis[$db] = RedisClient::connection($db);
        }
        return self::$redis[$db];
    }

    public static function getKey($key,$params = []){
        $path = config($key);
        if(!$path) return false;
        foreach($params as $k=>$v){
            $path = str_replace('{'.$k.'}',$v,$path);
        }
        return $path;
    }

}
