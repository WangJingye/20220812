<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CheckoutRepository
{
    const Cart = '_Cart';
    public static $maxQty = 6;
    /**
     * 获取购物车
     * @param $uid
     * @return array
     */
    public static function getCart($uid){
        $key = env('APP_NAME').self::Cart.':'.$uid;
        return Redis::HGETALL($key);
    }

    /**
     * 添加购物车
     * @param $uid
     * @param $sku
     * @param $qty
     * @return array
     */
    public static function addCart($uid,$sku,$qty){
        $key = env('APP_NAME').self::Cart.':'.$uid;
        $old_qty = Redis::HGET($key,$sku)?:0;
        $sum = $old_qty+$qty;
        //新库存不得大于max
        $sum = $sum>self::$maxQty?self::$maxQty:$sum;
        Redis::HSET($key,$sku,$sum);
        return [$sku=>$sum];
    }

    /**
     * 更新购物车
     * @param $uid
     * @param $items [SKU=>QTY]
     * @return mixed
     */
    public static function updateCart($uid,$items){
        $key = env('APP_NAME').self::Cart.':'.$uid;
        Redis::MULTI();
        foreach($items as $sku=>$qty){
            $qty = $qty>self::$maxQty?self::$maxQty:$qty;
            if($qty<=0){
                Redis::HDEL($key,$sku);
                unset($items[$sku]);
            }else{
                Redis::HSET($key,$sku,$qty);
            }
        }
        Redis::EXEC();
        return $items;
    }

    /**
     * 删除购物车项
     * @param $uid
     * @param $sku
     */
    public static function delCart($uid,$sku){
        $key = env('APP_NAME').self::Cart.':'.$uid;
        Redis::HDEL($key,$sku);
    }

    /**
     * 从数据库获取订单金额
     * @param $uid
     * @param $orderId
     * @return mixed
     */
    public static function getAmount($uid,$orderId){
        return DB::table('sales_order')->where('increment_id',$orderId)
            ->where('customer_id',$uid)
            ->value('grand_total');
    }

    /**
     * 根据订单号获取对应的WS客户端ID
     * @param $order_id
     * @return mixed
     */
    public static function getFd($order_id){
        $key = env('APP_NAME').'_Fd';
        return Redis::ZSCORE($key,$order_id);
    }

    /**
     * 将订单流水号保存到数据库
     * @param $order_id
     * @param $num
     * @return int
     */
    public static function setSerialsNumberToDB($order_id,$num){
        return DB::table('sales_order')->where('increment_id',$order_id)
            ->update(['serials_number'=>$num]);
    }

    /**
     * 从DB中获取订单流水号
     * @param $order_id
     * @return mixed
     */
    public static function getSerialsNumberFromDB($order_id){
        return DB::table('sales_order')->where('increment_id',$order_id)
            ->value('serials_number');
    }

    /**
     * 获取自增值
     * @param string $bizCode
     * @return mixed
     */
    public static function getInc($bizCode = '_SerialsNumberInc'){
        $key = env('APP_NAME').$bizCode;
        $incr = Redis::INCR($key);
        //0点重置自增
        $expireTime = strtotime(date("Y-m-d 23:59:59"));
        Redis::EXPIREAT($key, $expireTime);
        return $incr;
    }

    /**
     * 根据订单号保存电子发票
     * @param $order_id
     * @param $data
     * @param $whereStatus
     * @return int
     */
    public static function setInvoiceByTid($order_id,$data,$whereStatus = null){
        $model = DB::table('ot_invoice')->where('tid',$order_id);
        if($whereStatus){
            $model->where('status',$whereStatus);
        }
        return $model->update($data);
    }

    public static function setInvoiceByTaskSn($task_sn,$data,$whereStatus = null){
        $model = DB::table('ot_invoice')->where('task_sn',$task_sn);
        if($whereStatus){
            $model->where('status',$whereStatus);
        }
        return $model->update($data);
    }
}
