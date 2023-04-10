<?php
namespace App\Services\Pay;

use App\Lib\Pay\AliPay\Lib\AopClient;
use App\Lib\Pay\AliPay\Lib\AopCertification;
use App\Lib\Pay\AliPay\Lib\AlipayTradeQueryRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeWapPayRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeAppPayRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeFastpayRefundQueryRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradePrecreateRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeCreateRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeRefundRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradePagePayRequest;
use App\Lib\Pay\AliPay\Lib\AlipayTradeCloseRequest;

use App\Lib\Http;

class AliPayService
{
	public function __construct()
	{

		//我这里使用的是utf8编码，所以http头信息中设置编码为utf8
		header("Content-type: text/html; charset=utf-8");
		$aop = new AopClient();
		$aop->gatewayUrl = config('pay.ALI.gatewayUrl');//'你的appid';
		$aop->appId = config('pay.ALI.appid');//'你的appid';
		$aop->rsaPrivateKey = config('pay.ALI.rsaPrivateKey');//应用私钥
		$aop->alipayrsaPublicKey = config('pay.ALI.alipayrsaPublicKey');//'你的支付宝公钥';
		$aop->apiVersion = '1.0';
		$aop->signType = 'RSA2';
		$aop->postCharset = 'UTF-8';
		$aop->format = 'json';
		$this->notifyUrl = env('NOTIFY_DOMAIN').'/order/api/pay/AliNotify';
		$this->aop = $aop;
	}

	public function  Pay($params = [])
	{
		$fun = $params['trade_type'];
		$params['total_amount'] = bcadd($params['total_amount'],0,2);
        $params['endTime'] = date("Y-m-d H:i:s", time() + config('pay.TIME_EXPIRE'));
		return $this->$fun($params);
	}

	/**
	 * [TradeWapPay alipay.trade.wap.pay(手机网站支付接口2.0)]
	 * @Author   Peien
	 * @DateTime 2020-07-13T14:28:48+0800
	 * @param    array                    $params [description]
	 */
	public function TradeWapPay($params = [])
	{
		$info = [
			"body" => $params['desc'],
			"subject" => $params['title'],
			"out_trade_no" =>$params['order_sn'],
			"time_express" => $params['endTime'],//该笔订单允许的最晚付款时间，逾期将关闭交易
			//"time_expire" => "2016-12-31 10:05",//绝对超时时间，格式为yyyy-MM-dd HH:mm
			"total_amount" => $params['total_amount'],
			"seller_id" => "",//收款支付宝用户ID
			//"auth_token" => "appopenBb64d181d0146481ab6a762c00714cC27",//针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
			//"goods_type" => "0",//商品主类型 :0-虚拟类商品,1-实物类商品
			//"quit_url" => "http://www.taobao.com/product/113714.html",//用户付款中途退出返回商户网站的地址
			//"passback_params" => "merchantBizType%3d3C%26merchantBizNo%3d2016010101111"
			"product_code" => "QUICK_WAP_WAY",//销售产品码，商家和支付宝签约的产品码
			/*"promo_params" => "{"storeIdType":"1"}"
			"royalty_info" => json_encode([
				"royalty_type" => "ROYALTY",
				"royalty_detail_infos" => [
					[
					"serial_no" => 1,
					"trans_in_type" => "userId",
					"batch_no" => "123",
					"out_relation_id" => "20131124001",
					"trans_out_type" => "userId",
					"trans_out" => "2088101126765726",
					"trans_in" => "2088101126708402",
					"amount" => 0.1,
					"desc" => "分账测试1",
					"amount_percentage" => "100",
					]
				]
			]),*/
			//"extend_params" => $this->extendParams($params),

			/*
			"sub_merchant" => [
				"merchant_id" => "2088000603999128",
				"merchant_type" => "alipay: 支付宝分配的间连商户编号, merchant: 商户端的间连商户编号",
			],
			"merchant_order_no" => "20161008001",
			"enable_pay_channels" => "pcredit,moneyFund,debitCardExpress",
			"disable_pay_channels" => "pcredit,moneyFund,debitCardExpress",
			"store_id" => "NJ_001",
			"goods_detail" => json_encode([
				[
					"goods_id" => "apple-01",
					"alipay_goods_id" => "20010001",
					"goods_name" => "ipad",
					"quantity" => 1,
					"price" => 2000,
					"goods_category" => "34543238",
					"categories_tree" => "124868003|126232002|126252004",
					"body" => "特价手机",
					"show_url" => "http://www.alipay.com/xxx.jpg",
				]
			]),
			"settle_info" => json_encode([
				"settle_detail_infos" => [
					[
						"trans_in_type" => "cardAliasNo"
						"trans_in" => "A0001"
						"summary_dimension" => "A0001"
						"settle_entity_id" => "2088xxxxx;ST_0001"
						"settle_entity_type" => "SecondMerchant、Store"
						"amount" => 0.1
					]
				],
				"settle_period_time" => "7d",
			]),
			"invoice_info" => [
				"key_info" => [
					"is_support_invoice" => true
					"invoice_merchant_name" => "ABC|003"
					"tax_num" => "1464888883494"
				],
				"details" => "[{"code":"100294400","name":"服饰","num":"2","sumPrice":"200.00","taxRate":"6%"}]",
			],
			"specified_channel" => "pcredit",
			"business_params" => "{"data":"123"}",
			"ext_user_info" => [
				"name" => "李明"
				"mobile" => "16587658765"
				"cert_type" => "IDENTITY_CARD"
				"cert_no" => "362334768769238881"
				"min_age" => "18"
				"fix_buyer" => "F"
				"need_check_info" => "F"
			]*/
		];
		$notifyUrl = $this->notifyUrl .'?order_sn='.$params['order_sn'].'&type=ali&trade_type=wap';
		$extend_params = $this->extendParams($params);
		if(!empty($extend_params))
		{	$notifyUrl = $this->notifyUrl .'?order_sn='.$params['order_sn'].'&type=huabei&trade_type=wap';
			$info['extend_params'] = $extend_params;
		}
		$returnUrl = env('FRONT_URL') . '/payquery?sn='. $params['order_sn'];
		$request = new AlipayTradeWapPayRequest();
		$request->setNotifyUrl($notifyUrl);

		$request->setReturnUrl($returnUrl);
		$request->setBizContent(json_encode($info));
		$result = $this->aop->pageExecute ( $request,'GET');
		return ['code'=> 1, 'data' => $result];
	}

	public function extendParams($params= [])
	{
		if($params['hb_fq_num'])
		{
			$extend_params = [
			///"sys_service_provider_id" => "",
			"hb_fq_num" => $params['hb_fq_num'],
			"hb_fq_seller_percent" => env('HUABEIPERCENT',100),
			//"industry_reflux_info" => "",
			//"card_type" => ""
			];
			return $extend_params;
		}
		return [];
	}

	/**
	 * [TradePcPay 统一收单下单并支付页面接口(alipay.trade.page.pay)]
	 * @Author   Peien
	 * @DateTime 2020-07-13T14:27:05+0800
	 * @param    array                    $params [description]
	 */
	public  function TradePcPay($params = [])
	{
		$info = [
			"body" => $params['desc'],
			"subject" => $params['title'],
			"out_trade_no" => $params['order_sn'],
			"time_express" => $params['endTime'],//该笔订单允许的最晚付款时间，逾期将关闭交易
			//"time_expire" => "2016-12-31 10:05",//绝对超时时间，格式为yyyy-MM-dd HH:mm
			"total_amount" => $params['total_amount'],
			"seller_id" => "",//收款支付宝用户ID
			"qr_pay_mode" =>4,
			"qrcode_width" =>150,
			//"auth_token" => "appopenBb64d181d0146481ab6a762c00714cC27",//针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
			//"goods_type" => "0",//商品主类型 :0-虚拟类商品,1-实物类商品
			//"quit_url" => "http://www.taobao.com/product/113714.html",//用户付款中途退出返回商户网站的地址
			//"passback_params" => "merchantBizType%3d3C%26merchantBizNo%3d2016010101111"
			"product_code" => "FAST_INSTANT_TRADE_PAY",//销售产品码，与支付宝签约的产品码名称。 注：目前仅支持FAST_INSTANT_TRADE_PAY
			/*"promo_params" => "{"storeIdType":"1"}"
			"royalty_info" => json_encode([
				"royalty_type" => "ROYALTY",
				"royalty_detail_infos" => [
					[
					"serial_no" => 1,
					"trans_in_type" => "userId",
					"batch_no" => "123",
					"out_relation_id" => "20131124001",
					"trans_out_type" => "userId",
					"trans_out" => "2088101126765726",
					"trans_in" => "2088101126708402",
					"amount" => 0.1,
					"desc" => "分账测试1",
					"amount_percentage" => "100",
					]
				]
			]),*/
			/*
			"sub_merchant" => [
				"merchant_id" => "2088000603999128",
				"merchant_type" => "alipay: 支付宝分配的间连商户编号, merchant: 商户端的间连商户编号",
			],
			"merchant_order_no" => "20161008001",
			"enable_pay_channels" => "pcredit,moneyFund,debitCardExpress",
			"disable_pay_channels" => "pcredit,moneyFund,debitCardExpress",
			"store_id" => "NJ_001",
			"goods_detail" => json_encode([
				[
					"goods_id" => "apple-01",
					"alipay_goods_id" => "20010001",
					"goods_name" => "ipad",
					"quantity" => 1,
					"price" => 2000,
					"goods_category" => "34543238",
					"categories_tree" => "124868003|126232002|126252004",
					"body" => "特价手机",
					"show_url" => "http://www.alipay.com/xxx.jpg",
				]
			]),
			"settle_info" => json_encode([
				"settle_detail_infos" => [
					[
						"trans_in_type" => "cardAliasNo"
						"trans_in" => "A0001"
						"summary_dimension" => "A0001"
						"settle_entity_id" => "2088xxxxx;ST_0001"
						"settle_entity_type" => "SecondMerchant、Store"
						"amount" => 0.1
					]
				],
				"settle_period_time" => "7d",
			]),
			"invoice_info" => [
				"key_info" => [
					"is_support_invoice" => true
					"invoice_merchant_name" => "ABC|003"
					"tax_num" => "1464888883494"
				],
				"details" => "[{"code":"100294400","name":"服饰","num":"2","sumPrice":"200.00","taxRate":"6%"}]",
			],
			"specified_channel" => "pcredit",
			"business_params" => "{"data":"123"}",
			"ext_user_info" => [
				"name" => "李明"
				"mobile" => "16587658765"
				"cert_type" => "IDENTITY_CARD"
				"cert_no" => "362334768769238881"
				"min_age" => "18"
				"fix_buyer" => "F"
				"need_check_info" => "F"
			]*/
		];
		$notifyUrl = $this->notifyUrl .'?order_sn='.$params['order_sn'].'&type=ali&trade_type=pc';
		$extend_params = $this->extendParams($params);
		if(!empty($extend_params))
		{	$notifyUrl = $this->notifyUrl .'?order_sn='.$params['order_sn'].'&type=huabei&trade_type=pc';
			$info['extend_params'] = $extend_params;
		}
		$returnUrl = env('FRONT_URL') . '/myaccount/orders?sn='. $params['order_sn'];
		$request = new AlipayTradePagePayRequest ();
		$request->setNotifyUrl($notifyUrl);
		$request->setReturnUrl($returnUrl);
		$request->setBizContent(json_encode($info));
		\Log::info('支付参数',[$request]);
		$result = $this->aop->pageExecute ( $request,'GET');
		\Log::info('ali PC网站支付=' .$result);
		//$qrCode = base64_encode(\QrCode::format('png')->size(150)->generate($result));
		return ['code'=> 1, 'data'=> $result];
	}

	/**
	 * [refund 退款]
	 * @Author   Peien
	 * @DateTime 2020-06-28T15:36:34+0800
	 * @param    [type]                   $params [description]
	 * @return   [type]                           [description]
	 */
	public function refund($params)
	{	
		/*$queryInfo = $this->orderQuery($params);
		if($queryInfo['code'] == 0)
		{
			//未付款或者失败
			return $queryInfo;
		}*/
		$info = json_encode([
			"out_trade_no" => $params['order_sn'],
			"trade_no" => "",
			"refund_amount" => $params['refund_fee'],
			"refund_currency" => "",
			"refund_reason" => "正常退款",
			"out_request_no" => $params['order_sn'] .date('Ymdhis'),
		]);
		$request = new AlipayTradeRefundRequest ();
		$request->setBizContent($info);
		$result = $this->aop->execute ( $request); 
		\Log::info('退款信息='.json_encode($result));
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return ['code' => 1,'data'=> '退款成功'];
		} else {
			return ['code' =>0,'data' => '退款失败'];
		}
	}

	/**
	 * [PayCancel 统一收单交易撤销接口(alipay.trade.cancel)]
	 * @Author   Peien
	 * @DateTime 2020-06-28T15:37:47+0800
	 * @param    array                    $params [description]
	 */
	public function PayCancel($params = [])
	{
		$info = json_encode([
			'out_trade_no' => $params['order_sn']
		]);
		$request = new AlipayTradeCancelRequest ();
		$request->setBizContent($info);
		$result = $this->aop->execute ( $request); 

		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return true;
		echo "成功";
		} else {
			return false;
		echo "失败";
		}
	}

	/**
	 * [fastpayRefundQuery alipay.trade.fastpay.refund.query(统一收单交易退款查询)]
	 * @Author   Peien
	 * @DateTime 2020-06-28T16:10:48+0800
	 * @param    array                    $params [description]
	 * @return   [type]                           [description]
	 */
	public function fastpayRefundQuery($params = [])
	{
		//dd($params);
		$info = json_encode([
			'trade_no' => $params['order_sn']
		]);
		$request = new AlipayTradeFastpayRefundQueryRequest ();
		$request->setBizContent($info);
		$result = $this->aop->execute ( $request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return $responseNode;
			//已经退款成功
			/*if($result->$responseNode->refund_status == '' || $result->$responseNode->refund_status == 'REFUND_SUCCESS')
			{
				return false;
			}*/
		return  true;
		} else {
			return false;
		echo "失败";
		}
	}



	/**
	 * [orderQuery description]
	 * @Author   Peien
	 * @DateTime 2020-06-28T16:41:55+0800
	 * @return   [type]                   [description]
	 */
	public function orderQuery($params = [])
	{
		$info = json_encode([
			'out_trade_no' => $params['order_sn']
		]);
		$request = new AlipayTradeQueryRequest ();
		$request->setBizContent($info);
		$result = $this->aop->execute( $request);
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			if($result->$responseNode->trade_status == 'TRADE_SUCCESS')
			{
				$queryInfo=[	
					'payTime' => $result->$responseNode->send_pay_date?? date('Y-m-d H:i:s'),
					'trade_no' => $result->$responseNode->trade_no?? ''
				];

				return ['code'=> 1, 'msg'=> 'ok', 'data'=> $queryInfo];
			}else
			{
				return ['code'=> 0, 'msg'=> $result->$responseNode->trade_status];
			}
		} else {
			return ['code' => 0, 'msg' => $result->$responseNode->msg];
		}

	}


	public function TradeQrPay($params = [])
	{

		$qrcode = $this->TradePrecreate($params);

	}

	/**
	 * [TradePrecreate 统一收单线下交易预创建(alipay.trade.precreate)]
	 * @Author   Peien
	 * @DateTime 2020-07-20T12:48:19+0800
	 * @param    array                    $params [description]
	 */
	public function TradePrecreate($params =[])
	{
		$info = json_encode([
			"out_trade_no" => $params['order_sn'],
			"total_amount" => $params['total_amount'],
			"subject" => $params['title'],
			"body" => $params['desc'],
			
			"product_code" =>  "",
			"timeout_express" => "90m",//该笔订单允许的最晚付款时间，逾期将关闭交易
			//"time_expire" => "2016-12-31 10:05",//绝对超时时间，格式为yyyy-MM-dd HH:mm
			
			"seller_id" => "",//收款支付宝用户ID
			//"auth_token" => "appopenBb64d181d0146481ab6a762c00714cC27",//针对用户授权接口，获取用户相关数据时，用于标识用户授权关系
			//"goods_type" => "0",//商品主类型 :0-虚拟类商品,1-实物类商品
			//"quit_url" => "http://www.taobao.com/product/113714.html",//用户付款中途退出返回商户网站的地址
			//"passback_params" => "merchantBizType%3d3C%26merchantBizNo%3d2016010101111"
			"product_code" => "OFFLINE_PAYMENT",//销售产品码，与支付宝签约的产品码名称。 注：目前仅支持FAST_INSTANT_TRADE_PAY
			"qr_code_timeout_express"=>'3m'
		]);
		$request = new AlipayTradePrecreateRequest ();
		$request->setBizContent($info);
		$result = $this->aop->execute ( $request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
		echo "成功";
		} else {
		echo "失败";
		}


	} 

	/**
	 * [getStagesInfo 获取花呗分期每期金额]
	 * @Author   Peien
	 * @DateTime 2020-08-03T10:26:10+0800
	 * @param    [type]                   $money [description]
	 * @return   [type]                          [description]
	 */
	public static function   getStagesInfo($money,$hb_fq_seller_percent)
	{

		$fq_array= explode(',',env('FQ_LIST','3,6,12'));
		$money = bcmul($money, 100,0);
		foreach ($fq_array as $value) {
			$params =
			[
				'money' =>  $money,
				'hb_fq_num' => $value,
			];
		$result[] = self::gethbMoneyInfo($params);
		}

		//3   2.30%
		//6    4.50%
		//12  7.50%
		//3
		$data['list'] = $result;
		$data['fq_string'] = env('FQ_LIST','3,6,12');
		
		return ['code' => 1, 'data' => $data];
	}

	public static function gethbMoneyInfo($info)
	{
			//商家承担手续费传入 100，用户承担手续费传入 0
			$hb_fq_seller_percent  = env('HUABEIPERCENT',0);
			if($hb_fq_seller_percent == 100)
			{
				if($info['hb_fq_num'] == 3)
				{
					$result = ['interest_rate'=>0, 'rate' => '0%'];
				}elseif($info['hb_fq_num'] == 6)
				{
					$result = ['interest_rate'=>0, 'rate' => '0%'];
				}elseif($info['hb_fq_num'] == 12)
				{
					$result = ['interest_rate'=>0, 'rate' => '0%'];
				}
			}
			else
			{
				if($info['hb_fq_num'] == 3)
				{
					$result = ['interest_rate'=>0.023, 'rate' => '2.3%'];
				}elseif($info['hb_fq_num'] == 6)
				{
					$result = ['interest_rate'=>0.045, 'rate' => '4.5%'];
				}elseif($info['hb_fq_num'] == 12)
				{
					$result = ['interest_rate'=>0.075, 'rate' => '7.5%'];
				}



			}


			

		//本金
		$result['principal'] = bcdiv(bcdiv($info['money'],$info['hb_fq_num'],0),100,2);
		//利息
		$result['interest'] = bcdiv(bcdiv(bcmul( $info['money'], $result['interest_rate'],2),$info['hb_fq_num'],0),100,2);
		//总和
		$result['total_amount'] = bcdiv($info['money'],100,0);//bcmul(bcadd($result['principal'], $result['interest'],2),$info['hb_fq_num'],2);
		$result['all_interest'] = 
		bcdiv(
			round(
				bcdiv(
					bcmul( 
						$info['money'], 
						$result['interest_rate'],
						1
					),
					$info['hb_fq_num'],
					1
				)
				,0
			),
			$info['hb_fq_num'],
			0
		);
		//利率
		$result['interest_rate'] = $result['rate'];
		$result['hb_fq_num'] = $info['hb_fq_num'];
		$result['hb_fq_seller_percent '] = $hb_fq_seller_percent;
		return $result;
	}

	/**
	 * [closeOrder 关闭订单]
	 * @Author   Peien
	 * @DateTime 2020-08-09T14:50:58+0800
	 * @param    [type]                   $params [description]
	 * @return   [type]                           [description]
	 */
	public function closeOrder($params)
	{
		$info = json_encode([
			"out_trade_no" => $params['order_sn']
		]);
		$request = new AlipayTradeCloseRequest();
		$request->setBizContent($info);
		$result = $this->aop->execute( $request); 
		$responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
		$resultCode = $result->$responseNode->code;
		if(!empty($resultCode)&&$resultCode == 10000){
			return ['code' => 1, 'data' => '1'];
		} else {
			return ['code' => 0, 'msg' => $result->$responseNode->sub_msg];
		}
	}

}