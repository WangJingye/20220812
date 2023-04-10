<?php
namespace App\Services\Checkout\Cart;

use App\Services\Api\CartServices;
use Illuminate\Support\Facades\Redis;
use App\Services\Checkout\Data\RedisKey;
//购物车相关操作
class Cart
{
    public $expire_seconds = 60*60*24*30;//30天
    //保存在redis缓存的数据结构
    public $sample_data_format = [
        'coupon_code'=>'',
        'coupon_id'=>'',
        'main'=>[//主商品
            ['cart_item_id'=>'','sku'=>'','qty'=>'','product_type'=>'','products'=>['sku',],],
        ],
        'free_try'=>[
            ['sku'=>''],
        ],//选择下单的试用装
        'checkout'=>[
            'channel'=>'',
            'shipping_address_id'=>'',
            'used_points'=>'',
            'shipping_method'=>'',
            'payment_method'=>'',
            'accepted_flag'=>'',
            'guide'=>'',//导购
            'invoice'=>[
                'type'=>'',
                'title'=>'',
                'id'=>'',
                'email'=>'',
            ],
        ],
    ];

    //下单之后，删除购物车中已经下单的商品
    public function delCartAfterSubmitOrder($customer_id){
        (new CartServices())->clearSelect($customer_id);
    }

    //获取选择下单的商品数据,sku,qty，包括了试用装
    //直接从redis缓存取，跟购物车公用
    /** @var \App\Services\Checkout\Data\Data $data_obj */
    public function getCheckoutItems($data_obj,$sku=''){
        $data = $data_obj->getData();
        $customer_id = $data['customer_id'];
        $checkout_data_arr = CartServices::getCheckoutInfo($customer_id);
        if($sku){
            //如果有立即购买的sku则替换
            $checkout_data_arr['main'] = [['sku'=>$sku,'qty'=>1,]];
        }
        $flag = $this->validateEmptyCart($checkout_data_arr);
        if(!$flag){
            return false;
        }
        $check_data = $this->adaptCart($checkout_data_arr);
        $data_obj->setCheckoutItems($check_data);
        return $data_obj;
    }

    //检测购物车不能为空
    private function validateEmptyCart($cart_data){
        if(!$cart_data){//空购物车
            return false;
        }
        $main = $cart_data['main']??[];
        if(!$main){//空选中
            return false;
        }
        foreach ($main as $item){
            if($item['qty'] < 1){//购物车数量异常
                return false;
            }
        }
        return true;
    }

    //兼容购物车的格式，把购物车格式转化为checkout格式
    public function adaptCart($cart_data){
        $main = $cart_data['main'];
        $new_main = [];
        foreach ($main as $item){
            $products = [];
            $product_type = 1;
            $sku = $item['sku'];//{{套装ID}}{{SKU1}}{{SKU2}}
            if(strpos($sku,"{{") !== false){ //套装
                $product_type = 2;
                $suit_data = $this->parseSuit($sku);
                $sku = $suit_data['suit_sku'];
                $products = $suit_data['child_skus'];
            }
            $new_main[] = [
                'cart_item_id'=>$item['sku'],//唯一值
                'sku'=>$sku,//真的sku
                'qty'=>$item['qty'],
                'product_type'=>$product_type,
                'products'=>$products,
            ];
        }
        $cart_data['main'] = $new_main;
        return $cart_data;
    }
    //解析套装sku
    private function parseSuit($suit_sku){
        $flag = preg_match_all ('/{{(.*)}}/U',$suit_sku,$result);
        if(!$flag){//不是套装
            return $suit_sku;
        }
        $child_skus = $result[1];
        array_shift($child_skus);//去掉第一个套装
        $suit_sku = $result[1][0];
        return [
            'suit_sku'=>$suit_sku,
            'child_skus'=>$child_skus,
        ];
    }

    //保存选择项
    public function setCheckoutItems($input_data){
        $customer_id = $input_data['customer_id'];
        $key = RedisKey::getSingleton()->getCheckoutInfoKey($customer_id);
        $cache_checkout_data = Redis::GET($key);
        //是否有立即购买
        $is_quick_buy = array_get($input_data,'sku')?1:0;
        if(!$cache_checkout_data&&!$is_quick_buy){
            return false;
        }
        $default_checkout_data = [
            'channel'=>'',
            'shipping_address_id'=>'',
            'used_points'=>'',
            'shipping_method'=>'',
            'payment_method'=>'',
            'accepted_flag'=>'',
            'guide'=>'',//导购
            'invoice'=>[
                'type'=>'',
                'id'=>'',
                'title'=>'',
                'email'=>'',
            ],
            'card'=>[
                'from'=>'',
                'to'=>'',
                'content'=>'',
            ],
        ];
        $cache_checkout_data = json_decode($cache_checkout_data,true);
        $cache_checkout_data['coupon_id'] = $input_data['coupon_id']??($cache_checkout_data['coupon_id']??'');
        $cache_checkout_data['coupon_code'] = $input_data['coupon_code']??($cache_checkout_data['coupon_code']??'');//优惠码
        $checkout_data = $cache_checkout_data['checkout']??$default_checkout_data;
        $backup_checkout_data = $checkout_data;
        $checkout_data['shipping_address_id'] = $input_data['shipping_address_id']??$checkout_data['shipping_address_id'];
        $checkout_data['used_points'] = $input_data['used_points']??$checkout_data['used_points'];//是否使用积分
        $checkout_data['shipping_method'] = $input_data['shipping_method']??$checkout_data['shipping_method'];
        $checkout_data['payment_method'] = $input_data['payment_method']??$checkout_data['payment_method'];
        $checkout_data['accepted_flag'] = $input_data['accepted_flag']??$checkout_data['accepted_flag'];
        $checkout_data['guide'] = $input_data['guide']??$checkout_data['guide'];//导购信息
        $checkout_data['invoice'] = $input_data['invoice']??$checkout_data['invoice'];
        $checkout_data['invoice']['type'] = isset($input_data['invoice']['type'])?$input_data['invoice']['type']:'';
        $checkout_data['invoice']['title'] = isset($input_data['invoice']['title'])?$input_data['invoice']['title']:'';
        $checkout_data['invoice']['id'] = isset($input_data['invoice']['id'])?$input_data['invoice']['id']:'';
        $checkout_data['invoice']['email'] = isset($input_data['invoice']['email'])?$input_data['invoice']['email']:'';
        $checkout_data['invoice']['mobile'] = isset($input_data['invoice']['mobile'])?$input_data['invoice']['mobile']:'';
        $checkout_data['card'] = $input_data['card']??$checkout_data['card'];
        $checkout_data['card']['from'] = isset($input_data['card']['from'])?$input_data['card']['from']:'';
        $checkout_data['card']['to'] = isset($input_data['card']['to'])?$input_data['card']['to']:'';
        $checkout_data['card']['content'] = isset($input_data['card']['content'])?$input_data['card']['content']:'';

        $cache_checkout_data['checkout'] = $checkout_data;

        $checkout_data_json = json_encode($cache_checkout_data);
        Redis::SETEX($key,$this->expire_seconds,$checkout_data_json);
        return true;

    }
}
