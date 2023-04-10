<?php
namespace App\Services\Checkout\Save;

use App\Services\Checkout\Data\RedisKey;
use Illuminate\Support\Facades\Redis;
//保存到redis里面
class RedisSave
{
    public $expire_seconds = 60*60*24*30;//30天
    public function save($data){
        $order_sn = (new Ordersn())->makeSerialsNumber();
        $key = RedisKey::getOrderKey($order_sn);
        Redis::SETEX($key,$this->expire_seconds,$data);
        $this->pushToOms($order_sn);
        return $order_sn;
    }
    //存放在redis list中，后续通过脚本来推送到oms
    public function pushToOms($order_sn){
        $key = RedisKey::getSingleton()->getKey('push_new_order_to_oms');
        Redis::LPUSH($key,$order_sn);
    }
}