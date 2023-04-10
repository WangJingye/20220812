<?php
namespace App\Services\Checkout;

use App\Services\Checkout\Depend\Member;
use App\Services\Checkout\ShipfeeTry\Data;
use App\Services\Checkout\Depend\Stock;
use App\Services\Checkout\Save\Save;
use App\Services\Checkout\Depend\Promotion;
use App\Services\Checkout\Depend\Product;
use App\Services\Checkout\Depend\ShipfeeTry as DependShipfeeTry;
use App\Services\Checkout\Cart\Cart;
use App\Services\Checkout\Promotion\Suit;
use App\Services\Checkout\Depend\Redemption;
use App\Services\Checkout\ShipfeeTry\ShipFeeTryValidation;
use App\Services\Checkout\ShipfeeTry\OrderInfo;

//付邮试用
class ShipfeeTry
{
    //创建订单
    public function process($data){
        $data_obj = new Data();
        $data_obj = $data_obj->setCustomerId($data['customer_id'])->setOpenId($data['openid']);
        //获取用户输入的数据，
        $data_obj = $data_obj->initData()->mappingInput($data);
        //获取付邮活动信息
        $depend_shipfee_try_obj = new DependShipfeeTry();
        $data_obj = $depend_shipfee_try_obj->getCampaignInfo($data_obj);
        if(!is_object($data_obj)){
            return ['error'=>['ship_fee_try_error'=>true,'error_code'=>$data_obj],'msg'=>'付邮活动已过期'];
        }
        //验证一个付邮活动一个用户只能下单一次
        $ship_fee_try_validation_obj = new ShipFeeTryValidation();
        $validation_result = $ship_fee_try_validation_obj->validateTimes($data_obj);
        if($validation_result){
            return ['error'=>['ship_fee_try_error'=>true,'duplicate_error_msg'=>'每个账户仅限购买一份'],'msg'=>'每个账户仅限购买一份'];
        }
        $data_obj = $data_obj->composeToCheckoutFormat();
        $member_obj = new Member();
        $data_obj = $member_obj->getMemberInfo($data_obj);
        if(!$data_obj){//没有获取到配送地址，说明被删除了
            return ['error'=>['deleted_shipping_address'=>true],'msg'=>'请填写完整配送地址'];
        }
        //先获取商品信息，是因为需要获取商品价格
        $product_obj = new Product();
        $data_obj = $product_obj->getProductInfoForShipfeeTry($data_obj);
        if($data_obj == false){
            return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
        }
        //邮费
        $data_obj = $data_obj->setOrderAmount();
        //oms创建订单
        $return = (new Save())->save($data_obj);
        if($return['code'] == 0){
            $out_stock = $return['data']['fail_array']??[];
            if($out_stock){
                $msg = $return['message']??'下单失败，请重试';
                return ['error'=>['out_stock_skus'=>true],'oms_return'=>$return,'msg'=>$msg];
            }
        }
        if($return['code'] == 1){
            //设置这个用户下单这个付邮活动了
            $ship_fee_try_validation_obj->setData($data_obj);
            $return_data = $return['data']??[];
            $order_sn = $return_data['order_sn']??'';
            $order_id = $return_data['order_id']??'';
            $created_at = $return_data['created_at']??'';
            $data = $data_obj->getData();
            $data['order_sn'] = $order_sn;
            $data['order_id'] = $order_id;
            $data['created_at'] = $created_at;
            $order_data = [
                'order_sn'=>$order_sn,
                'customer_id'=>$data['customer_id'],
                'ship_fee_try_campaign_id'=>$data['ship_fee_try_campaign_id'],
            ];
            //保存付邮订单的一些信息，为了后面未支付付邮订单取消，可以再下一单
            (new OrderInfo())->saveData($order_data);
            try{
                return ['order_sn'=>$order_sn,
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
        $data_obj = $data_obj->setCustomerId($data['customer_id'])->setOpenId($data['openid']);;
        //获取用户输入的数据，
        $data_obj = $data_obj->initData()->mappingInput($data);
        //获取付邮活动信息
        $depend_shipfee_try_obj = new DependShipfeeTry();
        $data_obj = $depend_shipfee_try_obj->getCampaignInfo($data_obj);
        if(!is_object($data_obj)){
            return ['error'=>['ship_fee_try_error'=>true,'error_code'=>$data_obj],'msg'=>'付邮活动过期'];
        }
        $data_obj = $data_obj->composeToCheckoutFormat();
        $member_obj = new Member();
        $data_obj = $member_obj->getMemberInfo($data_obj);
        if(!$data_obj){//没有获取到配送地址，说明被删除了
            return ['error'=>['deleted_shipping_address'=>true],'msg'=>'请填写配送地址'];
        }
        //先获取商品信息，是因为需要获取商品价格
        $product_obj = new Product();
        $data_obj = $product_obj->getProductInfoForShipfeeTry($data_obj);
        if($data_obj == false){
            return ['error'=>['product offline'=>true],'msg'=>'商品已经下架'];
        }
        //邮费
        $data_obj = $data_obj->setOrderAmount();
        $data = $data_obj->getData();
//        unset($data['product_data']);
//        unset($data['promotion_data']);
        return $data;
    }

}
