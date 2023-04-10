<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/1
 * Time: 11:35
 */

namespace App\Model\Search;
use App\Model\Redis as RedisM;
use App\Model\Help;


class BlackList extends Model
{

    /**
     * 匹配违禁词、如匹配则停止搜索返回无结果
     * @param $keywords
     * @return mixed
     */
    public static function matchBlackList($keywords)
    {
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.search.blacklist',['brand'=>$brand]);
        $isBlack = $redis->sismember($key, $keywords);
        $isEmoji = self::have_special_char($keywords);
        if($isEmoji){
            $isBlack = true;
        }
        return $isBlack;

    }

    /**
     * 判断是否存在Emoji表情
     * @param $str
     * @return bool
     */
    public static function have_special_char($str)
    {
        $length = mb_strlen($str);
        $array = [];
        for ($i=0; $i<$length; $i++) {
            $array[] = mb_substr($str, $i, 1, 'utf-8');
            if( strlen($array[$i]) >= 4 ){
                return true;

            }
        }
        return false;
    }

    /**
     * 设置违禁词信息
     * @param $keywords
     * @return mixed
     */
    public static function setBlackList($keywords){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.search.blacklist',['brand'=>$brand]);
//        $redis = app('redis.connection');
        $blacklist_array = explode(" ",$keywords);
        foreach ($blacklist_array as    $blacklist_detail){
            $redis->Sadd($key, $blacklist_detail);
        }
//        $redis->Sadd(config('app.name') . "_BlackList", "I DO");
//        $redis->Sadd(config('app.name') . "_BlackList", "darry ring");
        return $redis->Smembers($key);

    }

    /**
     * 删除违禁词信息
     * @param $keywords
     * @return mixed
     */
    public static function delBlackList($keywords){
        $brand = Help::getBrandCode();
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.search.blacklist',['brand'=>$brand]);
//        $redis = app('redis.connection');
        $blacklist_array = explode(" ",$keywords);
        foreach ($blacklist_array as    $blacklist_detail){
            $redis->Srem($key, $blacklist_detail);
        }
//        $redis->Sadd(config('app.name') . "_BlackList", "I DO");
//        $redis->Sadd(config('app.name') . "_BlackList", "darry ring");
        return true;

    }

}