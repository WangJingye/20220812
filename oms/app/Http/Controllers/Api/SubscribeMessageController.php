<?php

namespace App\Http\Controllers\Api;
use App\Model\SubscribeShipped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Support\Token;

class SubscribeMessageController extends ApiController
{


    function sendMessage(){

        $params = request()->all();
        $validator=\Illuminate\Support\Facades\Validator::Make($params,[
            'token'=>'required',
            'orderId'=>'required',
            'templateId'=>'required',
            'templateStatus'=>'required',
        ]);
        //小程序版本
        $state = request()->header('wxVersion')?:'';
        $token = request()->input('token');
        if(!$token) return false;
        $decrypted_data = decrypt($token);
        if (!array_key_exists('openid', $decrypted_data)) {

            return false;
        }
        $openid = $decrypted_data['openid'];
        $orderId=request('orderId');
        $templateId=explode(',', request('templateId'));
        $templateStatus=explode(',', request('templateStatus'));
        Log::info('templateId'.json_encode($templateId));
        try{
            $subscribeShipped= new SubscribeShipped();
            foreach ($templateId as $key => $value) {
                $data[$key]['openid'] = $openid;
                $data[$key]['order_sn'] = $orderId;
                $data[$key]['template_id'] = $templateId[$key];
                $data[$key]['template_status'] = $templateStatus[$key];
                $data[$key]['state'] = $state;
                $type = 0;
                if($templateId[$key] == env('PENDING_ID'))
                {
                    $type  = 1;
                }elseif($templateId[$key] == env('PAID_ID'))
                {
                    $type = 2;
                }elseif($templateId[$key] == env('SHIPPED_ID'))
                {
                    $type = 3;
                }
//                elseif($templateId[$key] == env('REFUND_ID'))
//                {
//                    $type  = 4;
//                }
                elseif($templateId[$key] == env('CANCEL_ID'))
                {
                    $type = 5;
                }elseif($templateId[$key] == env('FINISHED_ID'))
                {
                    $type = 6;
                }
                $data[$key]['type'] = $type; 
            }
            $subscribeShipped->insert($data);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
        return $this->success([$openid,$orderId,$templateId,$templateStatus]);
    }
    
    function subscribeMessageSend(){
        $orderId=request('orderId');
        $orderSn=request('orderSn');
        $shippedMethod=request('shippedMethod');
        $customerName=request('customerName');
        $address=request('address');
        $shipCode=request('shipCode');
        $result= (new SubscribeShipped())->subscribeMessageSend($orderId,$orderSn,$shippedMethod,$customerName,$address,$shipCode);
        return $this->success($result);
    }

    /**
     * [refundMessage 退款通知]
     * @Author   Peien
     * @DateTime 2020-09-10T14:07:48+0800
     * @return   [type]                   [description]
     */
    public  function refundMessage()
    {
        $orderId=request('orderId');
        $result = (new SubscribeShipped())->refundMessage($orderId);
        return $this->success($result);
    }

    /**
     * [paidMessage 付款]
     * @Author   Peien
     * @DateTime 2020-09-10T14:10:47+0800
     * @return   [type]                   [description]
     */
    public  function paidMessage()
    {
        $orderId=request('orderId');
        $result = (new SubscribeShipped())->paidMessage($orderId);
        return $this->success($result);
    }

    public  function pendMessage()
    {
        $orderId=request('orderId');
        $result = (new SubscribeShipped())->pendingMessage($orderId);
        return $this->success($result);
    }
}
