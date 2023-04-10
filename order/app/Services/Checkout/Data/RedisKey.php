<?php
namespace App\Services\Checkout\Data;


class RedisKey
{
        public static $instance = '';

        public function getKey($key){
            $map = [
                'ordersn'=>env('APP_NAME').'_OrderSerialsNumberInc',//@TODO,还需要标识订单所属于的用户
                'push_new_order_to_oms'=>env('APP_NAME').'_pushNewOrderToOms',//list，订单创建之后推送给oms
                'checkout_info'=>env('APP_NAME').'_checkout_info',//string,下单结算时，保存用户的选择，包括商品信息，优惠券，优惠码，配送地址等
            ];
            return $map[$key];
        }

        //付邮试用订单, hash
        public static function getShipFeeTryKey(){
            return 'order_ship_fee_try';
        }

        //付邮试用订单信息,hash，保存付邮订单的信息
        public static function getShipFeeTryOrderInfoKey(){
            return 'order_ship_fee_try_info';
        }

        //购物车和结算页面用户的选择项，包括选择的商品，配送地址，优惠券，等
        //string
        public function getCheckoutInfoKey($customer_id){
            return 'checkout_info:'.$customer_id;
        }

        //订单存放在redis的key, string 类型
        public static function getOrderKey($order_sn){
            return 'order:'.$order_sn;
        }

        //单例
        public static function getSingleton(){
            if(self::$instance){
                return self::$instance;
            }
            self::$instance = (new self());
            return self::$instance;
        }
}
