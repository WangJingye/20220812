<?php
namespace App\Services\Checkout\Oms;

use App\Services\Checkout\Data\RedisKey;
use Illuminate\Support\Facades\Redis ;
//创建的新订单推送给oms
class PushToOms
{
    //把redis中的订单推送给oms
    //OMS需要能够接受重复数据，或者这里处理不推送重复数据
    public function push(){
        $key = RedisKey::getSingleton()->getKey('push_new_order_to_oms');
        $len = Redis::LLEN($key);
        if($len){
            for($k=1;$k<=$len;$k++){
                $order_sn  = Redis::LPOP($key);
                $order_data = RedisKey::getOrderKey($order_sn);
                $flag = $this->toOms($order_data);
                if(!$flag){//推送oms失败，重新推送直到成功
                    Redis::LPUSH($key,$order_sn);
                }
            }
        }
    }
    public function toOms($data){

        return true;
    }
}