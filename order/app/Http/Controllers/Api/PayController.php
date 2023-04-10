<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
/*use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\DB;*/
use App\Services\Pay\WxPayService;
use App\Services\Pay\WxMiniAppService;
use App\Services\Pay\AliPayService;
use App\Services\Pay\UnionPayService;
use App\Services\Pay\ChinaPayService;
use App\Exceptions\ApiPlaintextException;
use Illuminate\Support\Facades\Redis;
use App\Services\Pay\updatePendingOrder;
use Exception;
use App\Lib\Http;
use Illuminate\Support\Facades\Log;

class PayController extends ApiController
{
	private $app_id;//app id，从服务商平台获取
    private $code; //激活码内容
    public  $device_id;//设备唯一身份ID
    public function __construct()
    {
        // //付款方式 1微信支付js api、2web支付宝支付 Alipay 、3花呗支付Huabai、4银联支付UnionPay、5货到付款Codpay 6miniapp微信小程序支付， 7native网页 微信扫码 ，8 h5 mweb微信 9pc 支付宝
        $this->WxPayService = new WxPayService();
        $this->AliPayService = new  AliPayService();
        $this->UnionPayService = new UnionPayService();
        $this->ChinaPayService = new ChinaPayService();
        $this->pendingOrderKey = env('APP_NAME').'pendingOrder';
    }
    public function pay(Request $request)
    {

        Log::info('pay 支付入参',[$request->all()]);
        //获取order_info
        $order_sn = $request->get('order_sn','');
        $trade_type = $request->get('trade_type','');
        $type = $request->get('payment_method','');
        $again = $request->get('again','');
        //TODO更新订单状态
        $api = app('ApiRequestInner',['module'=>'oms']);
        $result = $api->request('orders/details','GET',['order_sn'=>$order_sn]);
        if($result['code'] == 0)  return false;
        $orderInfo = $result['data'][0];
        if($orderInfo['order_status'] !== 1)
        {
            Log::info('pay 订单已取消或者不可支付');
            return $this->error([],'订单已取消或者不可支付');
        }
        $payType = $this->getPayType($type, $trade_type);
        //特殊化处理付款order_sn
        if(env('AMOUNT_PAY','PROD') == 'UAT')
        {
            $orderInfo['total_amount'] = '0.01';
        }
        //type 类型  WX、ALI、UNION、UPAY
        //METHOD H5、小程序、pc
        $params = [
            'title'        => config('pay.NAME'),//支付显示标题
            'total_amount' => $orderInfo['total_amount'], //订单金额  分为单位
            'order_sn'     => $orderInfo['order_sn'],//订单号
            'order_id'     => $orderInfo['id'],//订单id
            'openid'       => $request->get('openid'), //小程序支付 必传
            'desc'        => $orderInfo['order_data_item'][0]['name'],//描述
            'trade_type' => $payType['trade_type'] ?? '',//交易类型  JSAPI--JSAPI支付   、minApp 小程序支付、NATIVE --Native支付、APP--app支付，MWEB--H5支付，支付宝   TradeWapPay 手机网站支付   -HuabeiPay  花呗    TradePcPay pc支付
            'type'       => $payType['type'],//支付方式Ali,Union,Wx,China
            'hb_fq_num'     => $request->get('hb_fp_num',0),
            'redis_time'    =>time()
        ];
        Log::info('pay 支付参数 ',[$params]);
        $Services = $params['type'].'PayService';
        //微信再次发起支付 特殊处理
        if($again)
        {
            $againPay = $this->WxPayment($orderInfo, $trade_type,$type);
            if($againPay['code'] == 1)
            {
                return $this->success($againPay['data']);
            }else
            {
                $params['order_sn'] =$this->createOrderNo();
            }
        }
        $result = $this->$Services->Pay($params);
        Log::info('请求支付后返回参数',[$result]);
        if($result['code'] == 1)
        {
             //记录redis 值到hash
            $redis_params = $params;
            $redis_params['type_num'] = $type;
            $redis_params['trade_type_num'] = $trade_type;

            Redis::HSET($this->pendingOrderKey,$params['order_sn'],json_encode($redis_params));
            $params = [
                'pay_order_sn' => $params['order_sn'],
                'order_sn'     => $order_sn,
                'type'     => array_search($type,$this->getTypeDB()),
                'tradeType' =>array_search($trade_type,$this->getTradeTypeDB()),
                'time'    =>date('Y-m-d H:i:s'),
                'payment'   => json_encode($result['data']),
                'huabei_period' => $params['hb_fq_num']
            ];
            Log::info('pay  再次发起支付参数',[$params]);
            $api = app('ApiRequestInner',['module'=>'oms']);
            $payUpdate = $api->request('pay/update','POST',$params);
            Log::info('pay 再次发起支付请求oms返回结果',[$payUpdate]);
            if(isset($payUpdate) && $payUpdate['code'] == 0)
            {
                return $this->error([], $payUpdate['message']);
            }
            return $this->success($result['data']);
        }
        return $this->error([],$result['msg']);
    }

    /**
     * [WxPayment description]
     * @Author   Peien
     * @DateTime 2020-08-10T11:45:37+0800
     * @param    [type]                   $params [description]
     */
    public function  WxPayment($orderInfo=[],$trade_type='',$type ='')
    {
        Log::info('WxPayment ',[$orderInfo]);
        //检测支付类型是否与原有的支付类型一致
        if($type  == $this->getTypeDB($orderInfo['payment_type']) && $trade_type == $this->getTradeTypeDB($orderInfo['trade_type'])){
            //检测数据库中的支付信息是否有效
            //7200为微信prepay_id  有效时间
            $endtime = strtotime($orderInfo['payment_at'])+7200;
            if($endtime> time())
            {
                return ['code' => 1, 'data' => json_decode($orderInfo['payment'])];
            }
            return ['code' => 0];
        }
        Log::info('再次下单去支付的支付信息超时');
        return ['code'=>0];
    }

    public function   orderQuery($Services, $params)
    {
        //查询订单是否支付，防止一 个订单被多次支付
        $queryResult = $this->$Services->orderQuery($params);
        if(isset($queryResult) && $queryResult['code'] == 1)
        {
            return ['code'=>1];
        }
        return ['code' =>0];
    }

    /**
     * [refund 退款]
     * @Author   Peien
     * @DateTime 2020-06-22T17:15:03+0800
     * @param    Request                  $request [description]
     * @return   [type]                            [description]
     */
    public function  refund(Request $request)
    {
        $from = $request->header('dlc-inner-invoke-from')?? '';
        if($from != 'order')
        {
            //记录下ip 地址
            Log::info('refund  退款异常ip',[$this->getRealIp()]);
            return $this->error('退款异常，请稍后重试');
        }
        Log::info('refund 退款入参',[$request->all()]);
        $trade_type = $request->get('trade_type','');
        $type = $request->get('type','');
        if($trade_type == 6)
        {
            $trade_type = 'minApp';
        }else
        {
            $trade_type = 'jsapi';
        }
        $payType = $this->getPayType($type, $trade_type);
        $total_fee = $request->get('total_fee')?? 0;
        $refund_fee = $request->get('refund_fee')?? 0;
        if(env('AMOUNT_PAY','PROD') == 'UAT')
        {
            $total_fee = bcdiv($total_fee, 1000,2);
            $refund_fee = bcdiv($refund_fee, 1000,2);
        }
        $params = [
            'total_fee'      => $total_fee,//订单总金额   微信使用
            'refund_fee'     => $refund_fee,//退款金额
            'order_sn'   => $request->get('order_sn'),//重新生成一个退款的订单号
            'order_id'   => $request->get('order_id',''),
            'trade_type' => $payType['trade_type'] ?? '',
            'type'       => $payType['type'] ?? '',//'Ali', //Ali,Union,Wx;$request->get('type'),
            'OriOrderNo' => $request->get('order_sn'),
            'OriTranDate' => $request->get('ori_tran_date'?? ''),
        ];
        if(empty($params)) return $this->error([],'订单交易号为空');
        $Services = $payType['type'].'PayService';
        $queryResult = $this->$Services->orderQuery($params);
        Log::info('refund 订单查询结果',$queryResult);
        if(!isset($queryResult) || $queryResult['code'] == 0)
        {

            Log::info('付款成功异常订单order_sn='.$params['order_sn']);
            //修改订单状态
            $updateparams = [
                'trade_no' => $queryResult['data']['trade_no'],
                'order_sn' => $params['order_sn'],
                'type'     => $type,
                'tradeType' => $trade_type,
                'payTime'    =>$queryResult['data']['trade_no'] ?? date('Y-m-d H:i:s'),
            ];
            $this->updateOrder($updateparams);
           return $this->error($queryResult['msg']?? '');
        }
        Log::info('refund 退款参数=',[$params]);
        $result = $this->$Services->refund($params);
        Log::info('refund 退款返回结果',[$result]);
        if(isset($result) && $result['code'] == 1)
        {
            /*$api = app('ApiRequestInner',['module'=>'oms']);
            $api->request('message/refund','POST',['order_id' => $params['order_id']]);*/
            return $this->success($result['data']);
        }
        return $this->error([],$result['msg']?? '');
    }

    /**
     * [query 订单查询]
     * @Author   Peien
     * @DateTime 2020-06-22T17:15:35+0800
     * @param    Request                  $request [description]
     * @return   [type]                            [description]
     */
    public function query(Request $request)
    {
        $params = [
            'order_sn'   => $request->get('order_sn'),//"71609271",//$params['order_sn'],
            'order_id'   => $request->get('order_id'),
            'trade_type' => $request->get('trade_type') ?? '',
            'type'       => $request->get('type'),//'Union',
            'OriTranDate' => $request->get('ori_tran_date'),//'Union',
        ];
        $queryServices = $request->get('type').'PayService';
        //查询订单是否支付，防止一次订单被多次支付
        $queryResult = $this->$queryServices->orderQuery($params);
        if(isset($queryResult) && $queryResult['code'] == 1)
        {
            return $this->success($queryResult['data']);
        }
        return $this->error([],$queryResult['msg']?? 'false');
    }

     /**
     * [GaQuery Ga 查询订单]
     * @Author   Peien
     * @DateTime 2020-09-25T15:14:58+0800
     */
    public function GaQuery(Request $request)
    {
        $order_sn = $request->get('order_sn');
        $api = app('ApiRequestInner',['module'=>'oms']);
        $result = $api->request('orders/details','GET',['pay_order_sn'=>$order_sn,'type' => 'GA']);
        if($result['code'] == 0)  return $this->error(['payResult' => 'error']);
        $orderInfo = $result['data'] ?? [];
        $orderInfo['payResult'] = 'error';
        if($orderInfo['payment_type'] == 5)
        {
            $orderInfo['payment_type'] = 'Offline';
            $orderInfo['trade_type'] = $this->getChannelDb($orderInfo['channel'] ?? 3);
            $orderInfo['payResult'] = 'success';
            return $this->success($orderInfo);
        }
        $payType = $this->getPayType($orderInfo['payment_type'],$orderInfo['trade_type']);
        $params = [
            'order_sn'     => $orderInfo['pay_order_sn'],//订单号
            'order_id'     => $orderInfo['order_id'],//订单id
            'trade_type' => $payType['trade_type'] ?? '',
            'type'       => $payType['type'] ?? '',
            'OriTranDate' => $orderInfo['transaction_date'] ?? ''
        ];
        $services = $params['type'].'PayService';
        $info  = $this->orderQuery($services, $params);
        $payGaTypes = $this->GaPayResult($orderInfo);
        $orderInfo['payment_type'] = $payGaTypes['payment'];
        $orderInfo['trade_type'] = $payGaTypes['type'];
        if($info['code'] == 1){
            $orderInfo['payResult'] = 'success';
            return $this->success($orderInfo);
        }
        return $this->error($orderInfo,'error');
    }
    /**
     * [NativeNotify description]
     * @Author   Peien
     * @DateTime 2020-08-04T13:52:39+0800
     */
    public function NativeNotify()
    {
        $xml = file_get_contents("php://input");
        Log::info('nativeNotify支付回调开始xml='.$xml);
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转成数组
        $result = json_decode($jsonxml, true);
        if($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS')
        {
            //订单查询
            $params = [
                'order_sn'   => $result['out_trade_no'],
                'trade_type' => 'NATIVE'
            ];
            $queryResult = $this->WxPayService->orderQuery($params);
            if($queryResult['code'] == 0)
            {
                return $this->error([],$queryResult['msg']);
            }
            //修改订单状态
            $params = [
                'trade_no' => $result['transaction_id'],
                'order_sn' => $result['out_trade_no'],
                'type'     => 2,
                'tradeType' => 7,
                'payTime'    =>date('Y-m-d H:i:s'),
            ];
            return $this->updateOrder($params);
        }
        return $this->error([],'false');
    }

     /**
     * [minAppNotify description]
     * @Author   Peien
     * @DateTime 2020-08-04T13:52:39+0800
     */
    public function minAppNotify()
    {
        $xml = file_get_contents("php://input");
        Log::info('nativeNotify支付回调开始xml='.$xml);
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转成数组
        $result = json_decode($jsonxml, true);
        if($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS')
        {
            //订单查询
            $params = [
                'order_sn'   => $result['out_trade_no'],
                'trade_type' => 'minApp'
            ];
            $queryResult = $this->WxPayService->orderQuery($params);
            if($queryResult['code'] == 0)
            {
                return $this->error([],$queryResult['msg']);
            }
            //修改订单状态
            $params = [
                'trade_no' => $result['transaction_id'],
                'order_sn' => $result['out_trade_no'],
                'type'     => 2,
                'tradeType' => 6,
                'payTime'    =>date('Y-m-d H:i:s'),
            ];
            return $this->updateOrder($params);
        }
        return $this->error([],'false');
    }


    /**
     * [webNotify description]
     * @Author   Peien
     * @DateTime 2020-08-04T13:52:44+0800
     * @return   [type]                   [description]
     */
    public function webNotify()
    {
        $xml = file_get_contents("php://input");
        Log::info('webNotify支付回调开始xml='.$xml);
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转成数组
        $result = json_decode($jsonxml, true);
        if($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS')
        {
            //订单查询
            $params = [
                'order_sn'   => $result['out_trade_no'],
                'trade_type' => 'MWEB'
            ];
            $queryResult = $this->WxPayService->orderQuery($params);
            if($queryResult['code'] == 0)
            {
                return $this->error([],$queryResult['msg']);
            }
            //修改订单状态
            $params = [
                'trade_no' => $result['transaction_id'],
                'order_sn' => $result['out_trade_no'],
                'type'     => 2,
                'tradeType' => 8,//$result['trade_type'],
                'payTime'    =>date('Y-m-d H:i:s'),
            ];
            return $this->updateOrder($params);
        }
        return $this->error([],'false');
    }

    /**
     * [BackReceive 银联后台通知参数（异步应答）]
     * @Author   Peien
     * @DateTime 2020-06-29T18:10:38+0800
     * @param    Request                  $request [description]
     */
    public function BackReceive(Request $request)
    {
        $params = [

            'queryId' =>  '',//消费交易的流水号，供后续查询用
            'currencyCode' => 156,
            'traceTime'    => date('mdHis'),
            'exchangeDate' => date('md'),
            'respMsg'      => '',
            'signature'    => '',
            'signMethod'   => '',
            'settleCurrencyCode' => '',
            'settleAmt'    => '',
            'settleDate'   => '',
            'respCode'     => '',
            'traceNo'      => '',
            'respMsg'      => '',
        ];
        $notify = $this->UnionPayService->notify($params);
        return $notify;
    }

    /**
     * [AliNotify Ali的支付回调]
     * @Author   Peien
     * @DateTime 2020-07-20T11:05:57+0800
     * @param    Request                  $request [description]
     */
    public function AliNotify(Request $request)
    {
        Log::info('支付宝回调信息='.json_encode($request->all()));
        $info =$request->all();
        if($info['trade_status'] == 'TRADE_SUCCESS')
        {
            //1AliPay,2 WeixinPay,3 UnionPay 4HuabeiPay 5 Offline
            if($info['type'] =='ali')
            {
                $type = 1;
            }else
            {
              $type = 4;
            }
            //付款方式 1微信支付js api、2web支付宝支付 Alipay 、3花呗支付Huabai、4银联支付UnionPay、5货到付款Codpay 6miniapp微信小程序支付， 7native网页 微信扫码 ，8 h5 mweb微信 9pc 支付宝
            if($info['trade_type'] == 'wap')
            {
                $trade_type = 2;
            }else
            {
                $trade_type = 7;
            }
            //订单查询
            $params = [
                'order_sn'   => $info['order_sn'],
                'trade_type' => $info['trade_type']
            ];
            $queryResult = $this->AliPayService->orderQuery($params);
            if($queryResult['code'] == 0)
            {
                return $this->error([],$queryResult['msg']);
            }
            //修改订单状态
            $params = [
                'trade_no' => $info['trade_no'],
                'order_sn' => $info['order_sn'],
                'type'     => $type,
                'tradeType' => $trade_type,//$result['trade_type'],
                'payTime'    =>$info['gmt_payment'],
            ];
            return $this->updateOrder($params);
        }
        return 'false';
    }
    /**
     * [WxNotify 微信支付回调]
     * @Author   Peien
     * @DateTime 2020-07-22T10:48:05+0800
     */
    public function WxNotify()
    {
        ////获取返回的xml
        $xml = file_get_contents("php://input");
        Log::info('WxNotify支付回调开始xml='.$xml);
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转成数组
        $result = json_decode($jsonxml, true);
        if($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS')
        {
            //订单查询
            $params = [
                'order_sn'   => $result['out_trade_no'],
                'trade_type' => $result['trade_type']
            ];
            $queryResult = $this->WxPayService->orderQuery($params);
            if($queryResult['code'] == 0)
            {
                return $this->error([],$queryResult['msg']);
            }
            //修改订单状态
            $params = [
                'trade_no' => $result['transaction_id'],
                'order_sn' => $result['out_trade_no'],
                'type'     => 2,
                'tradeType' => 1,//$result['trade_type'],
                'payTime'    =>date('Y-m-d H:i:s'),
            ];
            return $this->updateOrder($params);
        }
        return $this->error([],'false');
    }
    /**
     * [ChinaNotify chinaPay 支付回调]
     * @Author   Peien
     * @DateTime 2020-07-28T11:17:07+0800
     * @param    Request                  $request [description]
     */
    public function ChinaNotify(Request $request)
    {
        $infoArray = $request->all();
        Log::info('chinaPay支付回调',[$infoArray]);
        if($infoArray['OrderStatus'] !== '0000')
        {
            Log::info('chinaPay支付回调失败',[$infoArray]);
           throw new ApiPlaintextException(false);
        }
        $infoArray['OriTranDate'] = $infoArray['TranDate'];
        $infoArray['order_sn'] = $infoArray['MerOrderNo'];
        $queryResult = $this->ChinaPayService->orderQuery($infoArray);
        if($queryResult['code'] == 0)
        {
            return $this->error([],$queryResult['msg']);
        }
        $updateData = [
            'order_sn'  => $infoArray['MerOrderNo'],
            'payTime'   => date('Y-m-d ',strtotime($infoArray['TranDate'])).date('H:i:s'),
            'type'      => 3,
            'tradeType' => 4,
            'trade_no'  => $infoArray['MerId'],
        ];
        return $this->updateOrder($updateData);

    }

    /**
     * [getPayType description]
     * @Author   Peien
     * @DateTime 2020-07-29T14:00:17+0800
     * @return   [type]                   [description]
     */
    public function getPayType($type ='', $trade_type ='')
    {
        $result =[];
        //1AliPay,2 WeixinPay,3 UnionPay 4HuabeiPay 5 Offline
        //支付方式: AliPay, WeixinPay, UnionPay,HuabeiPay,Offline
        //微信支付必填（JSAPI--JSAPI支付 、minApp 小程序支付、NATIVE --Native支付、MWEB--H5支付）
        if($type == 'AliPay' or $type == 'HuabeiPay' or $type ==1   or $type ==4 )
        {
            $result['type'] ='Ali';
            if($trade_type == 'NATIVE')
            {
                $result['trade_type'] = 'TradePcPay';
            }elseif ($trade_type == 'MWEB') {
                $result['trade_type'] = 'TradeWapPay';
            }
        }elseif ($type == 'WeixinPay' or $type ==2) {
            $result['type'] ='Wx';
            $result['trade_type'] = $trade_type;
        }
        elseif ($type == 'UnionPay' or $type ==3) {
            $result['type'] ='China';
        }
        return $result;
    }

    /**
     * [getTradeTypeDB 数据库映射关系]
     * @Author   Peien
     * @DateTime 2020-08-10T13:07:48+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function getTradeTypeDB($params = [])
    {
        ////1微信支付js api、2web支付宝支付 Alipay 、3花呗支付Huabai、4银联支付UnionPay、5货到付款Codpay 6minapp微信小程序支付， 7native网页 微信扫码 ，8 h5 mweb微信 9pc 支付宝

        $trade_type= [
            '','jsapi', 'Alipay', 'Huabai', 'UnionPay', 'Codpay', 'minApp', 'NATIVE', 'MWEB', 'AlipayPC',
        ];
        if($params) return $trade_type[$params];
        return $trade_type;
    }

    public function getTypeDB($params =[])
    {
        $type = [
            '','AliPay','WeixinPay','UnionPay','HuabeiPay'
        ];
        if($params) return $type[$params];
        return $type;
    }

    public  function getChannelDb($params =''){
        if($params == 1){
            return 'JSAPI';
        }elseif($params == 2){
            return 'MWEB';
        }elseif($params == 3){
            return  'NATIVE';
        }else{
            return  'NATIVE';
        }
    }
    public  function updateOrder($infoArray)
    {
        $updateData = [
            'pay_order_sn'  => $infoArray['order_sn'] ?? '',
            'payTime'   => $infoArray['payTime'] ?? '',
            'type'      => $infoArray['type'],
            'tradeType' => $infoArray['tradeType'],
            'trade_no'  => $infoArray['trade_no'],
        ];
        Redis::HDEL($this->pendingOrderKey,$infoArray['order_sn']);
        //TODO更新订单状态
//        $api = app('ApiRequestInner',['module'=>'oms']);
//        $result = $api->request('orders/details','GET',['order_sn'=>'','pay_order_sn' => $infoArray['order_sn']]);
//        if(isset($result) && $result['code'] == 0) return 'fail';
//        $orderInfo = $result['data'][0];
//        \App\Services\WsServices::Notify($orderInfo['order_sn']);
        Log::info('修改订单参数'.json_encode($updateData));
        //TODO更新订单状态
        $api = app('ApiRequestInner',['module'=>'oms']);
        $successResult = $api->request('pay/success','POST',$updateData);
         Log::info('修改订单状态返回=',[$successResult]);
        if(isset($successResult) && $successResult['code'] == 0) return 'fail';
        //发送付款成功通知
        //$api = app('ApiRequestInner',['module'=>'oms']);
        //$result = $api->request('message/paid','POST',['orderId' => $orderInfo['id']]);
        return 'success';
    }

    /**
     * [getStagesInfo 获取花呗分期信息]
     * @Author   Peien
     * @DateTime 2020-08-03T11:21:04+0800
     * @param    [type]                   $money [description]
     * @return   [type]                          [description]
     */
    public function getStagesInfo(Request $request)
    {
        $money = request('money');
        $hb_fq_seller_percent = env('HUABEIPERCENT',100);
        if($hb_fq_seller_percent == '') $hb_fq_seller_percent = 100;
        //客户承担手续费
        $result = AliPayService::getStagesInfo($money,$hb_fq_seller_percent);
        if($result['code'] == 1)
        {
            return $this->success($result['data']);
        }
        return $this->error([],'获取花呗分期信息异常');
    }

    public function wechatNotify()
    {
        $http = new Http();
        Log::info('网页授权='.json_encode(request()->all()));
        $code = request('code');
        $from = request('url','');
        $result = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".config('pay.WX.appid.jsapi')."&secret=".  config('pay.WX.secret')."&code=".$code."&grant_type=authorization_code");
        //$result = $http->api_request("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".config('pay.WX.appid.jsapi')."&secret=".config('pay.WX.secret')."&code=".$code."&grant_type=authorization_code");
        $info = json_decode($result,true);
        Log::info(json_encode($info));
        Log::info('url地址'.$from);
        if(stripos($from,'?') === false)
        {
           $url =  $from.'?openid='.$info['openid'];
        }else
        {
            $url = $from.'&openid='.$info['openid'] ;
        }
        Header('Location:'.$url);exit;
    }

    /**
     * [snsapiBase description]
     * @Author   Peien
     * @DateTime 2020-08-06T20:51:14+0800
     * @return   [type]                   [description]
     */
    public function  getAccess(Request $request)
    {
        $href =$request->get('href');
        Log::info($href);
        $result = $this->WxPayService->getOpenid($href);
        Log::info($result);
        return $this->success($result);
    }

    /**
     * [closeOrder description]
     * @Author   Peien
     * @DateTime 2020-08-09T11:26:26+0800
     * @param    Request                  $request [description]
     * @return   [type]                            [description]
     */
    public function closeOrder(Request $request)
    {
        Log::info('closeOrder 开始入参',[$request->all()]);
        //获取order_info
        $order_sn = $request->get('order_sn','');
        $type =  $request->get('type','');
        $trade_type =  $request->get('trade_type','');
        $payType = $this->getPayType($type, $trade_type);
        $ori_tran_date = $request->get('ori_tran_date','');
        //type 类型  WX、ALI、UNION、UPAY
        //METHOD H5、小程序、pc
        $params = [
            'order_sn'     => $order_sn,//订单号
            'trade_type' => $payType['trade_type'] ?? '',//交易类型  JSAPI--JSAPI支付   、minApp 小程序支付、NATIVE --Native支付、APP--app支付，MWEB--H5支付，支付宝   TradeWapPay 手机网站支付   -HuabeiPay  花呗    TradePcPay pc支付
            'type'       => $payType['type'],//支付方式Ali,Union,Wx,China
            'OriTranDate' => $ori_tran_date,
        ];
        //关单操作之前请求查询接口
        $Services = $params['type'].'PayService';
        $queryResult = $this->$Services->orderQuery($params);
        Log::info('closeOrder 请求查询接口',[$queryResult]);
        if(!isset($queryResult) || $queryResult['code'] == 1)
        {
            //订单已付款
            $updateData = [
                //'status' => 'paid',
                //'TranDate' =>  $infoArray['transDate'],
                //'TranTime' => '',
                'order_sn'  => $order_sn,
                'payTime'   => $queryResult['data']['payTime']?? date('Y-m-d H:i:s'),
                'type'      => $type,
                'tradeType' => $trade_type,
                'trade_no'  => $queryResult['data']['trade_no']?? '',
            ];
            $this->updateOrder($updateData);
            return $this->error([],$queryResult['msg']?? '');
        }else
        {
                if($params['type'] == 'Ali')
                {
                    if($queryResult['msg']  == 'Business Failed')
                    {
                        return $this->success('成功');
                    }
                }
        }
        $result = $this->$Services->closeOrder($params);
        Log::info('closeOrder 请求关单操作',[$request->all()]);
        if($result['code'] == 1)
        {
            return $this->success($result['data']);
        }
        return $this->error([], $result['msg']);
    }

    /**
     * [createOrderNo 生成订单号-copy oms]
     * @Author   Peien
     * @DateTime 2020-08-10T13:22:36+0800
     * @param    integer                  $channel_id [description]
     * @param    integer                  $is_test    [description]
     * @return   [type]                               [description]
     */
    public static function createOrderNo($channel_id = 1, $is_test = 2)
    {   $redis = Redis::connection('orderSn');
        $nums = $redis->incr('create_order_no');
        return date('ymd') . $is_test . $channel_id . sprintf("%08d", $nums);
    }
    public function updatePendingOrder()
    {
         (new updatePendingOrder())->updatePendingOrder();
    }

    /**
     * @param array $info
     *
     */
    public  function GaPayResult($info = [])
    {
        if($info['payment_type'] == 3)
        {
            if($info['channel'] == 1){
                return ['payment'=>'UnionPay','type' => 'JSAPI'];
            }elseif($info['channel'] == 2){
                return ['payment'=>'UnionPay','type' => 'MWEB'];
            }
            elseif ($info['channel'] == 3)
            {
                return ['payment'=>'UnionPay','type' => 'NATIVE'];
            }
        }
        //1 Ali   2 weixin    3 Union  4  Huabei  5 货到付款
        $paymentType =  ['','AliPay','WeixinPay','UnionPay','HuabeiPay'];
        //2 MWEB 6,JSAPI   7,NATIVE  8,MWEB  9 NATIVE
        $tradeType = ['','jsapi','MWEB','','','','JSAPI','NATIVE','MWEB','NATIVE'];
        return ['payment'=>$paymentType[$info['payment_type']],'type' => $tradeType[$info['trade_type']] ];
    }
}
?>
