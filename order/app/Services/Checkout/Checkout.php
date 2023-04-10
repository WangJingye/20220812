<?php
namespace App\Services\Checkout;

use App\Services\Checkout\Depend\Member;
use App\Services\Checkout\Data\Data;
use App\Services\Checkout\Depend\Stock;
use App\Services\Checkout\Save\Save;
use App\Services\Checkout\Depend\Promotion;
use App\Services\Checkout\Depend\Product;
use App\Services\Checkout\Cart\Cart;
use App\Services\Checkout\Payment\Payment;
use App\Services\Checkout\Promotion\Suit;
use App\Services\Checkout\Depend\Redemption;

//update:2020/06/24,库存和积分放在oms扣除
class Checkout
{
    //创建订单
    public function process($data){
        $data_obj = new Data();
        $cart_obj = new Cart();
        $promotion_suit_obj = new Suit();
        $data_obj = $data_obj->initData();
        $data_obj = $data_obj->setPromotionSuitObj($promotion_suit_obj);
        $data_obj = $data_obj->setCustomerId($data['customer_id'])->setOpenId($data['openid']);
        //立即购买的sku
        $sku = array_get($data,'sku')?:'';
        //获取选择下单结算的商品，包括试用装
        $data_obj = $cart_obj->getCheckoutItems($data_obj,$sku);
        if(!$data_obj){//购物车空
            return ['error'=>['empty_cart'=>true],'msg'=>'空购物车'];
        }
        //获取用户输入的数据，用户输入数据将会替换购物车过来的数据,coupon_id,code
        $data_obj = $data_obj->mappingInput($data);
        $member_obj = new Member();
        $data_obj = $member_obj->getMemberInfo($data_obj);
        if(!$data_obj){//没有获取到配送地址，说明被删除了
            return ['error'=>['deleted_shipping_address'=>true],'msg'=>'配送地址有误'];
        }
        $data_obj = $member_obj->decreasePoints($data_obj);
        if(!$data_obj){//扣除积分失败，还积分
            return ['error'=>['out_points'=>true],'msg'=>'积分错误'];
        }
        //先获取商品信息，是因为需要获取商品价格
        $product_obj = new Product();
        //组装商品信息
        $data_obj = $product_obj->getProductInfo($data_obj);
        if($data_obj == false){
            return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
        }
        //先运行促销，是促销有返回赠品，试用装，需要检测库存。赠品，促销之后还需要获取一次商品信息？
        //如果再次运行促销的时候跟当时提交的试用装不同，报错
        $promotion_obj = new Promotion();
        $data_obj = $promotion_obj->getPromotionInfo($data_obj);//如果促销超时，就系统异常了
        if(!$data_obj){//当时提交的试用装没有了，促销被禁止了?
            return ['error'=>['out_stock_free_try'=>true],'msg'=>'试用装没库存'];
        }
        if($data_obj->getData()['total']['total_amount'] == 0){//0元订单，不能下单
            return ['error'=>['zero amount'=>true],'msg'=>'交易异常，请联系客服'];
        }
        //如果有促销就再拿一次商品信息
        if($data_obj->getData()['gift'] or $data_obj->getdata()['product_coupon_sku']){
            $data_obj = $product_obj->getProductInfo($data_obj);
            if($data_obj == false){
                return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
            }
        }else{
            //促销之后，需要重新组装数据
            $data_obj = $data_obj->setProductsInfo();
        }
        $used_points = $data_obj->getUsedPoints();
        //组装增加打包的sku start
        $data_obj = $product_obj->setIncludeSkus($data_obj);
        //组装增加打包的sku end
        $stock_obj = new Stock();
        $data_obj = $stock_obj->decreaseStock($data_obj);
        if(!$data_obj){//扣除库存失败，还库存，还需要归还用户积分
            $member_obj->restorePoints($used_points);
            return ['error'=>['out_stock_skus'=>true],'msg'=>'无库存'];
        }
        $return = (new Save())->save($data_obj);
        if($return['code'] == 0){
            $out_stock = $return['data']['fail_array']??[];
            if($out_stock){
                $msg = $return['message']??'下单失败，请重试';
                return ['error'=>['out_stock_skus'=>true],'oms_return'=>$return,'msg'=>$msg];
            }
        }
        if($return['code'] == 1){
            //清除购物车
            $return_data = $return['data']??[];
            if(empty($data['sku'])){//如果不属于立即购买则清除购物车
                $cart_obj->delCartAfterSubmitOrder($data['customer_id']);
            }
            $order_sn = $return_data['order_sn']??'';
            $order_id = $return_data['order_id']??'';
            $data = $data_obj->getData();
            $data['order_sn'] = $order_sn;
            $data['order_id'] = $order_id;
            try{
                return [
                    'order_sn'=>$order_sn,
                    'oms_return'=>$return_data
                ];
            }catch (\Exception $e){
                return ['error'=>['payment_error'=>true,],'order_id'=>$order_id,'order_sn'=>$order_sn,'msg'=>'下单失败，请重试'];
            }
        }
        return ['error'=>['system_error'=>true],'data'=>$data_obj->getData(),'oms_return'=>$return,'msg'=>'下单失败，请重试'];
        return $data_obj->getData();
    }

    //下单确认页面
    public function confirm($data){
        $data_obj = new Data();
        $cart_obj = new Cart();
        $promotion_suit_obj = new Suit();
        $data_obj = $data_obj->initData();
        $data_obj = $data_obj->setPromotionSuitObj($promotion_suit_obj);
        $data_obj = $data_obj->setCustomerId($data['customer_id'])->setOpenId($data['openid']);;
        //立即购买的sku
        $sku = array_get($data,'sku')?:'';
        //获取选择下单结算的商品，包括试用装
        $data_obj = $cart_obj->getCheckoutItems($data_obj,$sku);
        if(!$data_obj){//购物车空
            return ['error'=>['empty_cart'=>true],'msg'=>'空购物车'];
        }
        //获取用户输入的数据，用户输入数据将会替换购物车过来的数据,coupon_id,code
        $data_obj = $data_obj->mappingInput($data);
        $member_obj = new Member();
        $data_obj = $member_obj->getMemberInfo($data_obj);
        if(!$data_obj){//没有获取到配送地址，说明被删除了
            return ['error'=>['deleted_shipping_address'=>true],'msg'=>'配送地址有误'];
        }
        //先获取商品信息，是因为需要获取商品价格
        $product_obj = new Product();
        $data_obj = $product_obj->getProductInfo($data_obj);
        if($data_obj == false){
            return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
        }
        //先运行促销，是促销有返回赠品，试用装，需要检测库存。赠品，促销之后还需要获取一次商品信息？
        //如果再次运行促销的时候跟当时提交的试用装不同，报错
        $promotion_obj = new Promotion();
        $data_obj = $promotion_obj->getPromotionInfo($data_obj);//如果促销超时，就系统异常了
        if(!$data_obj){//当时提交的试用装没有了，促销被禁止了?
            return ['error'=>['out_stock_free_try'=>true],'msg'=>'试用装没库存'];
        }
        //如果有促销就再拿一次商品信息
        if($data_obj->getData()['gift'] or $data_obj->getData()['product_coupon_sku'] ){
            $data_obj = $product_obj->getProductInfo($data_obj);
            if($data_obj == false){
                return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
            }
        }else{
            //促销之后，需要重新组装数据
            $data_obj = $data_obj->setProductsInfo();
        }
        $data = $data_obj->getData();
//        unset($data['product_data']);
//        unset($data['promotion_data']);
        return $data;
    }
}
