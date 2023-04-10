<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/18
 * Time: 17:45
 */
namespace App\Repositories\Oms;

use Illuminate\Support\Facades\Redis;
class OmsOrderIdRepository
{
    static  $channel_list = [
        'pc' => 1,
        'mobile' =>2,
        'wechat' => 3
        ];

    /**
     * 通过Redis生成自增的订单号
     * @param $channel
     * @param $order_id
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Model|null|object|string|static
     */
    public static function incrementOrderId($channel,$order_id, $key = 'order-primary-key')
    {
        //订单前6位是年月日，第7位为来源，第8位为测试或身缠
        $order_prefix = date("ymd");
        $source_id = self::$channel_list[$channel];
        $order_sn = OmsOrderMainRepository::getOrderSn($order_id);
        //如果已经存在了oms_order_sn订单号，就自动返回，不去redis自增
        if($order_sn){
            return $order_sn;
        }
        return $order_prefix . $source_id .Redis::incr(self::formatKey($key));
    }

    /**
     * 格式化redis的key为大写
     * @param $key
     * @return string
     */
    public static function formatKey($key)
    {
        return strtoupper($key);
    }
}
