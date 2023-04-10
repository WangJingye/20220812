<?php namespace App\Services\Dlc;

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
        $redis = Redis::connection('goods');
        $key = env('APP_NAME').":ad:item:list:{$name}";
        $json = $redis->get($key);
        return json_decode($json,true);
    }

}
