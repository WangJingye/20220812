<?php
namespace App\Services\Checkout\Payment;

use App\Services\Pay\payMentService;
//支付
class Payment
{
    public $payment_mapping = [
        'AliPay'=>'Ali',
        'WeixinPay'=>'Wx',
        'UnionPay'=>'Union',
        'HuabeiPay'=>'Ali',
        'Offline'=>'',
    ];
    //获取支付的参数，传递给前端
    public function getPaymentParams($data){
        if($data['payment_method'] == 'Offline'){
            return ['payment_method'=>'Offline'];
        }
        $params = $this->composePaymentParams($data);
        $payment_params = (new payMentService())->pay($params);
        return $payment_params;
    }

    private function composePaymentParams($data){
        $params = [
            'title'=>'Sisley订单支付',
            'total_amount'=>$data['total']['total_amount'],
            'order_sn'=>$data['order_sn'],
            'order_id'=>$data['order_id'],
            'openid'=>$data['openid'],
            'desc'=>'',
            'type'=>$this->payment_mapping[$data['payment_method']],
            'trade_type'=>$data['trade_type'],//微信支付必填（JSAPI--JSAPI支付 、minApp 小程序支付、NATIVE --Native支付、MWEB--H5支付）,支付宝
            'buyer_id'=>'',
        ];
        return $params;
    }
}