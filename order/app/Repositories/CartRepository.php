<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class CartRepository
{
    const Cart = 'Cart';
    public static $maxQty = 12;
    /**
     * 获取购物车
     * @param $uid
     * @return array
     */
    public static function getCart($uid){
        $key = self::Cart.':'.$uid;
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
        $key = self::Cart.':'.$uid;
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
        $key = self::Cart.':'.$uid;
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

    public static function replaceCart($uid,$sku,$qty,$old_sku){
        $key = self::Cart.':'.$uid;
        $qty = $qty>self::$maxQty?self::$maxQty:$qty;
        if($qty<0){
            Redis::HDEL($key,$old_sku);
        }else{
            $items = Redis::HGETALL($key);
            Redis::DEL($key);
            $new_items = [];
            //如果新的sku已存在(购物车存在相同的spu不通的规格) 则先获取叠加数量再删除
            if(array_key_exists($sku,$items)){
                $qty = $items[$sku]+$qty;
                $qty = $qty>self::$maxQty?self::$maxQty:$qty;
                unset($items[$sku]);
            }
            foreach($items as $k=>$q){
                //遍历 如果是老的sku则替换为新的sku
                if($k==$old_sku){
                    $k = $sku;
                    $q = $qty;
                }
                $new_items[$k] = $q;
            }
            Redis::HMSET($key,$new_items);
        }
    }

    /**
     * 删除购物车项
     * @param $uid
     * @param $sku
     * @return bool
     */
    public static function delCart($uid,$sku){
        $key = self::Cart.':'.$uid;
        if(Redis::EXISTS($key)){
            Redis::HDEL($key,$sku);
        }return true;
    }

    //购物车选中项和优惠券
    const CheckoutInfo = 'checkout_info';

    public static function getCheckoutInfo($uid){
        $data = [];
        if($uid){
            $key = self::CheckoutInfo.':'.$uid;
            $data = Redis::GET($key);
            if($data){
                $data = json_decode($data,true);
            }
        }
        return $data;
    }

    public static function setCheckoutInfo($uid,$data){
        $key = self::CheckoutInfo.':'.$uid;
        return Redis::SET($key,json_encode($data,320));
    }

    public static function delCheckoutInfo($uid){
        $key = self::CheckoutInfo.':'.$uid;
        return Redis::DEL($key);
    }
}
