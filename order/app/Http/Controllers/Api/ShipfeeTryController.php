<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Checkout\ShipfeeTry;
use App\Services\Checkout\ShipfeeTry\OrderInfo;
use App\Services\Checkout\ShipfeeTry\ShipFeeTryValidation;

//付邮试用
class ShipfeeTryController extends ApiController
{

    public function __construct(){
        bcscale(2);
    }

    //未支付付邮订单取消，可以再下一单
    public function revert(Request $request){
        $data = $request->all();
        $order_sn = $data['order_sn'];
        $order_info = (new OrderInfo())->getDataByOrderSn($order_sn);
        $customer_id = $order_info['customer_id']??'';
        $ship_fee_try_order_info = $order_info['campaign_id']??'';
        $reset_data = [
            'customer_id'=>$customer_id,
            'ship_fee_try_campaign_id'=>$ship_fee_try_order_info,
        ];
        (new ShipFeeTryValidation())->resetData($reset_data);
        return $this->success($reset_data);
    }

    //下单页面
    public function confirm(Request $request){
        $data = $request->all();
        $uid = $this->getUid(1);
        $openid = $this->getOpenid(1);
        $data['customer_id'] = $uid;
        $data['openid'] = $openid;
        $data['channel'] = $this->getFrom(1);
        $LTINFO = request()->cookie('LTINFO');
        $data['ltinfo'] = $LTINFO;
        $return = (new ShipfeeTry())->confirm($data);
        if(isset($return['error'])){
            $msg = $return['msg']??'请重试';
            return $this->error($return,$msg);
        }
        return $this->success($return);
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
        $return = (new ShipfeeTry())->process($data);
        if(isset($return['error'])){
            $msg = $return['msg']??'下单失败，请重试';
            return $this->error($return,$msg);
        }
        return $this->success($return);
    }

}
