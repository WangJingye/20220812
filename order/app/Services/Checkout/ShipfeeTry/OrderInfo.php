<?php
namespace App\Services\Checkout\ShipfeeTry;
use App\Services\Checkout\Data\RedisKey;
use Illuminate\Support\Facades\Redis;

//保存付邮订单的一些信息到redis缓存，主要是为了后来未支付付邮单独取消的时候，可以再下一单，
//这时需要重置付邮验证的信息，需要拿到订单对应的customer_id,shipfeetry_campaign_id
class OrderInfo
{
    //保存订单信息
    public function saveData($data){
        $key = RedisKey::getShipFeeTryOrderInfoKey();
        $customer_id = (int) $data['customer_id'];
        $campaign_id = (int) $data['ship_fee_try_campaign_id'];
        $order_sn = $data['order_sn'];
        $save_data = [
            'customer_id'=>$customer_id,
            'campaign_id'=>$campaign_id,
            'order_sn'=>$order_sn,
        ];
        $save_data_json = json_encode($save_data);
        Redis::HSET($key,$order_sn,$save_data_json);
    }
    //根据订单号获取订单信息
    public function getDataByOrderSn($order_sn){
        $key = RedisKey::getShipFeeTryOrderInfoKey();
        $data = Redis::HGET($key,$order_sn);
        $json_data = json_decode($data,true);
        return $json_data;
    }
}