<?php namespace App\Http\Controllers\Api;

use App\Services\Checkout\Cart\Cart;
use App\Services\Checkout\Checkout;
use Illuminate\Http\Request;

class CheckoutController extends ApiController
{

    public function __construct(){
        bcscale(2);
    }


    //下单页面
    public function confirm(Request $request){
        try{
        $data = $request->all();
        $uid = $this->getUid(1);
        $openid = $this->getOpenid(1);
        $data['customer_id'] = $uid;
        $data['openid'] = $openid;
        $data['channel'] = $this->getFrom(1);
        $LTINFO = request()->cookie('LTINFO');
        $data['ltinfo'] = $LTINFO;
        $flag = (new Cart())->setCheckoutItems($data);
        if(!$flag){
            return $this->error(['error'=>['empty_cart1'=>true,'uid'=>$uid]],'空购物车');
        }
        $return = (new Checkout())->confirm($data);
        $deleted_shipping_address = $return['error']['deleted_shipping_address']??false;
        if($deleted_shipping_address){//极端情况下，下单createOrder失败，但是保存了shipping_address_id,又删除了shipping_address_id
            $data['shipping_address_id'] = '';//清除shipping_address_id
            (new Cart())->setCheckoutItems($data);
        }
        if(isset($return['error'])){
            $msg = $return['msg']??'请重试';
            return $this->error($return,$msg);
        }
        \App\Services\Dlc\CouponCode::exists($return,$uid);
        return $this->success($return);
        }catch (\Exception $e){
            var_dump($e);die;
            return $this->error([],$e->getMessage());
        }
    }

    //创建订单
    public function createOrder(Request $request){
        $data = $request->all();
        $uid = $this->getUid(1);
        $openid = $this->getOpenid(1);
        $data['customer_id'] = $uid;
        $data['openid'] = $openid;
        $data['channel'] = $this->getFrom(1);
        $LTINFO = request()->cookie('LTINFO');
        $data['ltinfo'] = $LTINFO;
        $data['share_uid'] = $request->get('share_uid');
        //立即购买的sku
        $data['sku'] = $request->get('sku');
        (new Cart())->setCheckoutItems($data);
        $return = (new Checkout())->process($data);
        if(isset($return['error'])){
            $msg = $return['msg']??'下单失败，请重试';
            return $this->error($return,$msg);
        }
        return $this->success($return);
    }

    //保存checkout 选项
    public function updateCheckoutOptions(Request $request){
        $data = $request->all();
        $uid = $this->getUid(1);
        $data['customer_id'] = $uid;
        $data['channel'] = $this->getFrom(1);;
        (new Cart())->setCheckoutItems($data);
        if(!$this->neededCalculatePrice($data)){
            return $this->success(['success']);
        }
        $return = (new Checkout())->process($data);
        return $this->success($return);
    }

    //是否需要重新计算价格
    private function neededCalculatePrice($data){
        $coupon_id = $data['coupon_id']??'';
        $coupon_code = $data['coupon_code']??'';
        $used_points = $data['used_points']??'';
        if($coupon_id or $coupon_code or $used_points){
            return true;
        }
        return false;
    }



}
