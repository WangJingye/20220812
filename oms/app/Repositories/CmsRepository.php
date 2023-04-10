<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CmsRepository
{
    const Block = '_Block';
    public static function setBlock($identifier,$content){
        $key = env('APP_NAME').self::Block.':'.$identifier;
        Redis::SET($key,$content);
        //token有效期为1天
        $expireTime = strtotime(date("Y-m-d H:i:s"))+3600*24*1;
        Redis::EXPIREAT($key, $expireTime);
        return true;
    }

    public static function getBlock($identifier){
        $key = env('APP_NAME').self::Block.':'.$identifier;
        $content = Redis::GET($key);
        if(empty($content)){
            //如果缓存中没有则去DB取
            $content = self::getBlockFromDB($identifier)?:'[]';
            //再放入缓存中
            self::setBlock($identifier,$content);
        }
        return $content;
    }

    public static function delBlock($identifier){
        $key = env('APP_NAME').self::Block.':'.$identifier;
        return Redis::DEL($key);
    }

    public static function getBlockFromDB($identifier){
        return DB::table('cms_block')->where('identifier',$identifier)->value('content');
    }
}
