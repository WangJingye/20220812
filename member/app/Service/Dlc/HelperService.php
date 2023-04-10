<?php namespace App\Service\Dlc;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HelperService
{
    /**
     * 获取广告位
     * @param $name
     * @return mixed
     */
    public static function getAd($name){
        $key = config('app.name').":ad:item:list:{$name}";
        $json = Redis::get($key);
        return json_decode($json,true);
    }

    const OPENID_SESSIONKEY = 'dlc_openid_sessionkey';
    public static function setSessionKeyByOpenId($openId,$sessionKey){
        Redis::hset(self::OPENID_SESSIONKEY,$openId,$sessionKey);
        return true;
    }

    public static function getSessionKeyByOpenId($openId){
        return Redis::hget(self::OPENID_SESSIONKEY,$openId);
    }

    const OPENID_MAP_INDEX = 'openid_map:index';
    const OPENID_MAP_VALUE = 'openid_map:value';
    /**
     * 生成并获取唯一ID(自动生成索引)
     * @param $openid
     * @return string
     */
    public static function setOpenidIndexMap($openid){
        $index = Redis::hget(config('app.name').':'.self::OPENID_MAP_VALUE,$openid);
        if(!$index){
            $index = self::getRandOnlyId();
            Redis::hset(config('app.name').':'.self::OPENID_MAP_INDEX,$index,$openid);
            Redis::hset(config('app.name').':'.self::OPENID_MAP_VALUE,$openid,$index);
        }
        return $index;
    }

    /**
     * 根据索引获取openid
     * @param $index
     * @return mixed
     */
    public static function getOpenidByIndex($index){
        return Redis::hget(config('app.name').':'.self::OPENID_MAP_INDEX,$index);
    }

    /**
     * 生成简短的唯一ID
     * @return string
     */
    public static function getRandOnlyId() {
        $endtime=1356019200;//2012-12-21时间戳
        $curtime=time();//当前时间戳
        $newtime=$curtime-$endtime;//新时间戳
        $rand=rand(0,99);//两位随机
        $all=$rand.$newtime;
        $onlyid=base_convert($all,10,36);//把10进制转为36进制的唯一ID
        return $onlyid;
    }

    const OPENID_UNIONID = 'dlc_openid_unionid';
    public static function setUnionIdByOpenId($openId,$unionId){
        Redis::hset(self::OPENID_UNIONID,$openId,$unionId);
        return true;
    }

    public static function getUnionIdByOpenId($openId){
        return Redis::hget(self::OPENID_UNIONID,$openId);
    }
}