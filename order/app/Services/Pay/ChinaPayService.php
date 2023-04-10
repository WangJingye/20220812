<?php
namespace App\Services\Pay;
define("transResvered", "trans_");
define("cardResvered", "card_");
define("transResveredKey", "TranReserved");
define("signatureField", "Signature");
use App\Lib\Pay\ChinaPay\Lib\Sign;
use App\Lib\Pay\ChinaPay\Lib\Common;
use App\Lib\Pay\ChinaPay\util\Settings_INI;
use App\Lib\Pay\ChinaPay\util\SecssUtil;
use App\Lib\Http;


class ChinaPayService
{
	
	public function __construct()
	{
		header('Content-Type:text/html;charset=utf-8 ');
		$settings = new Settings_INI();
		$settings->load(base_path() . "/app/Lib/Pay/ChinaPay/config/path.properties");
		$this->pay_url = $settings->get("pay_url");
		$this->query_url = $settings->get("query_url");
		$this->refund_url = $settings->get("refund_url");
		$this->merId = config('pay.CHINA.merId');
		$this->common = new Common();
	}
	/**
	 * [Pay ]
	 * @Author   Peien
	 * @DateTime 2020-07-14T19:37:10+0800
	 * @param    array                    $params [description]
	 */
	public function  Pay($params = [])
	{
        $params['total_amount'] = bcmul($params['total_amount'], 100,0);
        $returnUrl = env('FRONT_URL') . '/payquery?sn='. $params['order_sn'];
		$info = [
			'MerId' => $this->merId,//15位数字，由chinapay分配 商户号
			'MerOrderNo' => $params['order_sn'],//订单号
			'OrderAmt' => $params['total_amount'],//订单金额
			'TranDate' => date('Ymd'),//交易日期  8位数字，为订单提交日期 (Ymd)
			'TranTime' => date('His'),//交易时间 6位数字，为订单提交时间(his)
			'TranType' => '0001',//0001   (4位数字，网银支付交易为0001，如果商户不填写，ChinaPay会在持卡人页面显示商户已开通的交易类型供持卡人选择，完成支付)
			'BusiType' => '0001',//业务类型 0001
			'Version' => "20140728",//支付接口版本号
            'PayTimeOut'  => bcdiv(config('pay.TIME_EXPIRE'),60,0), //分钟，以 ChinaPay 接收交易的时间为准，超过此时间段后用户支付成功的交易，不通知商户，系统自动退款。 
			//'AcqCode' => "000000000000014",
			//'CurryNo' => "CNY",
			//'AccessType' => "0",
			///'MerResv' => "MerResv",
			//'CardTranData' => json_encode(['CardNo'=>$params['card_id']]),//商户页面收集卡号的交易必填，JSON格式填写，如：{"CardNo":"123","ProtocolNo":"123"}，根据不同交易类型填写不同的卡信息，填写后进行BASE64编码，编码后采用RSA加密，加密时调用方法encryptData进行加密。CardNo：卡号
			//'TranReserved' => json_encode(['BizType'=> '', 'TranSubType'=> '', 'TerminalType' => 'pc' ]),
			'MerPageUrl'     => $returnUrl,//商户页面接收ChinaPay应答的地址，用于引导使用者交易后返回商户网站页面 
			'MerBgUrl'     => env('NOTIFY_DOMAIN').'/order/api/pay/ChinaNotify',//商户后台接收ChinaPay应答的地址
		];
        \Log::info('ChinaPay支付参数=' .json_encode($info));
        $result = $this->ChinaPayResponse($info,$this->pay_url);
		return ['code'=>1,'data'=> $result];
	}
	/**
     * [orderQuery description]
     * @Author   Peien
     * @DateTime 2020-07-14T19:39:42+0800
     * @return   [type]                   [description]
     */
    public function  orderQuery($params = [])
    {
    	$info = [
			'MerId' => $this->merId,//15位数字，由chinapay分配 商户号
			'MerOrderNo' => $params['order_sn'],//订单号
			//'OrderAmt' => $params['total_amount'],//订单金额
			'TranDate' => $params['OriTranDate'] ?? date('Ymd'),//交易日期  8位数字，为订单提交日期 (Ymd)
			'TranTime' => date('His'),//交易时间 6位数字，为订单提交时间(his)
			'TranType' => '0502',//0001   (4位数字，网银支付交易为0001，如果商户不填写，ChinaPay会在持卡人页面显示商户已开通的交易类型供持卡人选择，完成支付)
			'BusiType' => '0001',//业务类型 0001
			'Version' => 20140728,//支付接口版本号
		];
		$result = $this->ChinaPayResponse($info,$this->query_url);
        parse_str($result, $resultArray);
         \Log::info('chinaPay 订单查询',[$resultArray]);
        if($resultArray['respCode'] == '0000' or $resultArray['respCode'] == '1003')
        {
            $resultArray['payTime'] = date('Y-m-d H:i:s',strtotime($resultArray['CompleteDate'].$resultArray['CompleteTime']));
            $resultArray['trade_no'] =$resultArray['AcqSeqId'];
            return ['code' => 1, 'data' => $resultArray]; 
        }
        return ['code'=> 0, 'msg' => $resultArray['respMsg']];
    }

    /**
     * [refund 	0401退款、0402退款撤销的交易接收地址如下：]
     * @Author   Peien
     * @DateTime 2020-07-14T19:40:09+0800
     * @param    array                    $params [description]
     * @return   [type]                           [description]
     */
    public function refund($params = [])
    {
        $params['refund_fee'] = bcmul($params['refund_fee'], 100,0);
    	$info = [
			'MerId' => $this->merId,//15位数字，由chinapay分配 商户号
			'MerOrderNo' => $params['order_sn'].'0',//订单号
			'RefundAmt' => $params['refund_fee'],//订单金额
			'TranDate' => date('Ymd'),//交易日期  8位数字，为订单提交日期 (Ymd)
			'TranTime' => date('His'),//交易时间 6位数字，为订单提交时间(his)
			'OriOrderNo' => $params['OriOrderNo'],//原始交易订单号
			'OriTranDate' => $params['OriTranDate'],
			'TranType' => '0401',//0001   (4位数字，网银支付交易为0001，如果商户不填写，ChinaPay会在持卡人页面显示商户已开通的交易类型供持卡人选择，完成支付)
			'BusiType' => '0001',//业务类型 0001
			'Version' => 20140728,//支付接口版本号
            'MerBgUrl' => env('NOTIFY_DOMAIN'). '/oms/pay/refundNotify',
		];
        \Log::info('refund SERVICE  退款参数',[$info]);
		$result = $this->ChinaPayResponse($info, $this->refund_url);
        \Log::info('ChinaPay退款返回结果=',[ $result]);
		parse_str($result, $resultArray);
        \Log::info('resultArray=',[$resultArray]);
		if($resultArray['respCode'] == '0000' or $resultArray['respCode'] == '1003')
		{
            return ['code' => 1, 'data' => $result]; 
		}
        return ['code'=> 0, 'msg' => $resultArray['respMsg']];
    }
    public function notify($params = [])
    {
       	include_once base_path('app/Lib/Pay/ChinaPay/util/SecssUtil.class.php');
        $secssUtil = new \SecssUtil();
        $securityPropFile = base_path('app/Lib/Pay/ChinaPay/config/security.properties');
        $secssUtil->init($securityPropFile);
        if ($secssUtil->verify($params)) {
        	return true;
        } else {
           return false;
        }
    }
	public function ChinaPayResponse($data, $query_url = '')
	{
        $transResvedJson = array();
        $cardInfoJson = array();
        $sendMap = array();
        foreach ($data as $key => $value) {
            if ($this->common->isEmpty($value)) {
                continue;
            }
            if ($this->common->startWith($key, transResvered)) {
                // 组装交易扩展域
                $key = substr($key, strlen(transResvered));
                $transResvedJson[$key] = $value;
            } else 
                if ($this->common->startWith($key, cardResvered)) {
                    // 组装有卡交易信息域
                    $key = substr($key, strlen(cardResvered));
                    $cardInfoJson[$key] = $value;
                } else {
                    $sendMap[$key] = $value;
                }
        }
        $transResvedStr = null;
        $cardResvedStr = null;
        if (count($transResvedJson) > 0) {
            $transResvedStr = json_encode($transResvedJson);
        }
        if (count($cardInfoJson) > 0) {
            $cardResvedStr = json_encode($cardInfoJson);
        }

        include_once base_path('app/Lib/Pay/ChinaPay/util/SecssUtil.class.php');
        $secssUtil = new \SecssUtil();
        if (! $this->common->isEmpty($transResvedStr)) {
            $transResvedStr = $secssUtil->decryptData($transResvedStr);
            $sendMap[transResveredKey] = $transResvedStr;
        }
        if (! $this->common->isEmpty($cardResvedStr)) {
            $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
            $sendMap[cardResveredKey] = $cardResvedStr;
        }
        $securityPropFile = base_path('app/Lib/Pay/ChinaPay/config/security.properties');
        $secssUtil->init($securityPropFile);
        $secssUtil->sign($sendMap);
        $sendMap[signatureField] = $secssUtil->getSign();
        $http = new Http();
    	$result = $this->cutstr_html($http->formCurl($query_url,$sendMap));
    	return $result;

	}
    public function cutstr_html($string){ 
        return $string;
        $string = trim($string); 
        $string = str_replace(["\r\n","\t","\r"],"",$string); 
        return trim($string);
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
        return ['code'=>1,'data' =>1];
    }
}