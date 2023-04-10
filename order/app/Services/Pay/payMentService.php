<?php
namespace App\Services\Pay;
use App\Services\Pay\WxPayService;
use App\Services\Pay\WxMiniAppService;
use App\Services\Pay\AliPayService;
use App\Services\Pay\UnionPayService;

class payMentService
{   

    public function __construct()
    {
        $this->WxPayService = new WxPayService();
        $this->AliPayService = new  AliPayService();
        $this->UnionPayService = new UnionPayService();
    }


	public function pay($param)
	{
		//type 类型  WX、ALI、UNION、UPAY
        //METHOD H5、小程序、pc
        $params = [
            'title'        => $param['title'],//支付显示标题
            'total_amount' => $param['total_amount'], //订单金额  分为单位
            'order_sn'     => $param['order_sn'],//订单号
            'order_id'     => $param['order_id'],//订单id
            'openid'       => $param['openid'], //小程序支付 必传
            'desc'        => $param['desc'],//描述
            'trade_type' => $param['trade_type'] ?? '',//交易类型  JSAPI--JSAPI支付   、minApp 小程序支付、NATIVE --Native支付、APP--app支付，MWEB--H5支付
            'type'       => $param['type'],//支付方式Ali,Union,Wx
        ];
        if(env('APP_ENV') == 'local' || env('APP_ENV') == 'uat')
        {
            $params['total_amount'] = 0.01;
        }
        $Services = $param['type'].'PayService';
            //查询订单是否支付，防止一次订单被多次支付
           /* $queryResult = $this->$Services->orderQuery($params);
            if($queryResult)
            {
                if(($queryResult['trade_state'] !== 'NOTPAY'))
                {
                    throw new ApiPlaintextException("订单异常，或者已经支付无法继续付款");
                }
            }*/
            $result = $this->$Services->Pay($params);
            return $result;
            //return $this->success($result);
            /*if($params['trade_type'] == 'NATIVE')
            {
                if($result['code'] == 0)
                {
                    throw new ApiPlaintextException($result['message']);
                }
                
            }*/
         //jsapi
        //$result = $this->WxMiniAppService->Pay($params); // 小程序
        //$result = $this->WxPayService->miniApp($params);
        //$result = $this->AliPayService->miniApp($params);
        //$result = $this->UnionPayService->mulyiCertPay($params);

	}
}
?>