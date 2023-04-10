<?php
namespace App\Services\Checkout\ShipfeeTry;

use App\Services\Checkout\Data\RedisKey;
use Illuminate\Support\Facades\Redis;

//根据customer_id, 付邮campaign_id，验证只能下单一次
class ShipFeeTryValidation
{

    //判断是否这个付邮活动已经下过单
    public function validateTimes($data_obj){
        $data = $data_obj->getData();
        $customer_id = (int) $data['customer_id'];
        $campaign_id = (int) $data['ship_fee_try_campaign_id'];
        $key = RedisKey::getShipFeeTryKey();
        $field = $customer_id.'_'.$campaign_id;
        $has = Redis::HEXISTS($key,$field);
        if($has){//下过这个付邮单
            return true;
        }
        return false;
    }

    //设置用户这个付邮活动已经下单了
    public function setData($data_obj){
        $data = $data_obj->getData();
        $customer_id = (int) $data['customer_id'];
        $campaign_id = (int) $data['ship_fee_try_campaign_id'];
        $key = RedisKey::getShipFeeTryKey();
        $field = $customer_id.'_'.$campaign_id;
        Redis::HSET($key,$field,1);
    }

    //重置用户可以购买付邮活动订单
    public function resetData($data){
        $customer_id = (int) $data['customer_id'];
        $campaign_id = (int) $data['ship_fee_try_campaign_id'];
        $key = RedisKey::getShipFeeTryKey();
        $field = $customer_id.'_'.$campaign_id;
        Redis::HDEL($key,$field);
    }
}
