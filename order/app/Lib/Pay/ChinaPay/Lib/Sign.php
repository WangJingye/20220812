<?php
namespace App\Lib\Pay\ChinaPay\Lib;
#use App\Lib\Pay\ChinaPay\Lib\Common;
#use App\Lib\Pay\ChinaPay\Lib\SecssUtil;
define(transResvered, "trans_");
	define(cardResvered, "card_");
	define(transResveredKey, "TranReserved");
	define(signatureField, "Signature");
include './util/common.php';
include './util/SecssUtil.class.php';

class Sign{
	

	public function __construct($params = [])
	{
		dd($params);
		session_start();

	if ($_POST) {
	    $dispatchMap = array(
	        // 配置个人网银交易转发地址
	        "0001" => "/chinapay_demo/page/pay/b2cPaySend.php",
	        "0004" => "/chinapay_demo/page/pay/b2cPaySend.php",
	        // 配置退款交易转发地址
	        "0401" => "/chinapay_demo/page/refund/b2cRefundSend.php",
	        // 配置查询交易转发地址
	        "0502" => "/chinapay_demo/page/query/b2cQuerySend.php"
	    );
	    if (count($_POST) > 0) {
	        if ($_POST['TranType'] && trim($_POST['TranType']) != "") {
	            $dispatchUrl = $dispatchMap[$_POST['TranType']];
	        } else {
	            $dispatchUrl = $dispatchMap['0001'];
	        }
	        $transResvedJson = array();
	        $cardInfoJson = array();
	        $sendMap = array();
	        foreach ($_POST as $key => $value) {
	            if (isEmpty($value)) {
	                continue;
	            }
	            if (startWith($key, transResvered)) {
	                // 组装交易扩展域
	                $key = substr($key, strlen(transResvered));
	                $transResvedJson[$key] = $value;
	            } else 
	                if (startWith($key, cardResvered)) {
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
	        
	        $secssUtil = new SecssUtil();
	        
	        if (! isEmpty($transResvedStr)) {
	            $transResvedStr = $secssUtil->decryptData($transResvedStr);
	            $sendMap[transResveredKey] = $transResvedStr;
	        }
	        if (! isEmpty($cardResvedStr)) {
	            $cardResvedStr = $secssUtil->decryptData($cardResvedStr);
	            $sendMap[cardResveredKey] = $cardResvedStr;
	        }
	        
	        $securityPropFile = $_SERVER['DOCUMENT_ROOT'] . "/chinapay_demo/config/security.properties";
	        $secssUtil->init($securityPropFile);
	        $secssUtil->sign($sendMap);
	        
	        $sendMap[signatureField] = $secssUtil->getSign();
	        $_SESSION = $sendMap;
	        
	        header("Location:" . $dispatchUrl);
	    }
	}

	}
	

	
}



?>