<?php
namespace App\Services\Checkout\Save;
use Illuminate\Support\Facades\Redis;
use App\Services\Checkout\Data\RedisKey;

class Ordersn
{
    public function makeSerialsNumber(){
        $date = date('mdHis');
        $inc = sprintf("%04d", self::getInc());
        $rand = rand(100,999);
        return "{$date}{$inc}{$rand}";
    }

    public static function getInc(){
        $key = (new RedisKey())->getKey('ordersn');
        $incr = Redis::INCR($key);
        //0点重置自增
        $expireTime = strtotime(date("Y-m-d 23:59:59"));
        Redis::EXPIREAT($key, $expireTime);
        return $incr;
    }
}