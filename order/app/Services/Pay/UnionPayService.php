<?php
namespace App\Services\Pay;

use App\Lib\Pay\UnionPay\Lib\AcpService;
use App\Lib\Pay\UnionPay\Lib\SDKConfig;
use Illuminate\Support\Facades\Log;

class UnionPayService
{

	public function __construct()
	{

		$this->merId = config('pay.UNION.merId');

	}

	/**
	 * [mulyiCertPay description]
	 * @Author   Peien
	 * @DateTime 2020-06-22T10:50:13+0800
	 * @return   [type]                   [description]
	 */
	public function Pay($params =[])
	{
		header ( 'Content-type:text/html;charset=utf-8' );
		
		//include_once base_path(). '\app\Lib\Pay\UnionPay\sdk\acp_service.php';

		/**
		 * 重要：联调测试时请仔细阅读注释！
		 * 
		 * 产品：跳转网关支付产品<br>
		 * 交易：消费：前台跳转，有前台通知应答和后台通知应答<br>
		 * 日期： 2015-09<br>

		 * 版权： 中国银联<br>
		 * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考，不提供编码性能规范性等方面的保障<br>
		 * 提示：该接口参考文档位置：open.unionpay.com帮助中心 下载  产品接口规范  《网关支付产品接口规范》，<br>
		 *              《平台接入接口规范-第5部分-附录》（内包含应答码接口规范，全渠道平台银行名称-简码对照表)<br>
		 *              《全渠道平台接入接口规范 第3部分 文件接口》（对账文件格式说明）<br>
		 * 测试过程中的如果遇到疑问或问题您可以：1）优先在open平台中查找答案：
		 * 							        调试过程中的问题或其他问题请在 https://open.unionpay.com/ajweb/help/faq/list 帮助中心 FAQ 搜索解决方案
		 *                             测试过程中产生的7位应答码问题疑问请在https://open.unionpay.com/ajweb/help/respCode/respCodeList 输入应答码搜索解决方案
		 *                          2） 咨询在线人工支持： open.unionpay.com注册一个用户并登陆在右上角点击“在线客服”，咨询人工QQ测试支持。
		 * 交易说明:1）以后台通知或交易状态查询交易确定交易成功,前台通知不能作为判断成功的标准.
		 *       2）交易状态查询交易（Form_6_5_Query）建议调用机制：前台类交易建议间隔（5分、10分、30分、60分、120分）发起交易查询，如果查询到结果成功，则不用再查询。（失败，处理中，查询不到订单均可能为中间状态）。也可以建议商户使用payTimeout（支付超时时间），过了这个时间点查询，得到的结果为最终结果。
		 */

		/*$customerInfo = array(
				'certifTp' => '01',
				'certifId' => '6216261000000000018',
				'customerNm' => '全渠道',
		);*/

		$data = array(
				
				//以下信息非特殊情况不需要改动
				'version' => SDKConfig::getSDKConfig()->version,                 //版本号
				'encoding' => 'utf-8',				  //编码方式
				'txnType' => '01',				      //交易类型
				'txnSubType' => '01',				  //交易子类
				'bizType' => '000201',				  //业务类型
				'frontUrl' =>  SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
				'backUrl' => SDKConfig::getSDKConfig()->backUrl,	  //后台通知地址
				'signMethod' => SDKConfig::getSDKConfig()->signMethod,	              //签名方法
				'channelType' => '08',	              //渠道类型，07-PC，08-手机
				'accessType' => '0',		          //接入类型
				'currencyCode' => '156',	          //交易币种，境内商户固定156
				
				//TODO 以下信息需要填写
				'merId' => $this->merId,		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
				'orderId' => $params["order_sn"],	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
				'txnTime' => date('YmdHis'),	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
				'txnAmt' => $params['total_amount'] * 100,	//交易金额，单位分，此处默认取demo演示页面传递的参数
				
				// 订单超时时间。
				// 超过此时间后，除网银交易外，其他交易银联系统会拒绝受理，提示超时。 跳转银行网银交易如果超时后交易成功，会自动退款，大约5个工作日金额返还到持卡人账户。
				// 此时间建议取支付时的北京时间加15分钟。
				// 超过超时时间调查询接口应答origRespCode不是A6或者00的就可以判断为失败。
				'payTimeout' => date('YmdHis', strtotime('+15 minutes')), 

				'riskRateInfo' =>'{commodityName=测试商品名称}',

				// 请求方保留域，
				// 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
				// 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
				// 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
				//    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
				// 2. 内容可能出现&={}[]"'符号时：
				// 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
				// 2) 如果对账文件没有显示要求，可做一下base64（如下）。
				//    注意控制数据长度，实际传输的数据长度不能超过1024位。
				//    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
				//    'reqReserved' => base64_encode('任意格式的信息都可以'),
				
		//		//如果需要支持用户用网银支付时必送bizScene
		//			'bizScene' => "120005", //业务场景，取值参考接口规范
		//		//需要使用网银交易且业务场景bizScene=120005投资理财时，子域证件类型、证件号、姓名必送
		//		//测试环境网银测试借记卡的身份信息：姓名：网银；证件类型：01；证件号：110101199003070257
		//		//测试环境网银测试贷记卡的身份信息：姓名：支付；证件类型：01；证件号：220102199007075743
					//'customerInfo' => AcpService::getCustomerInfo($customerInfo), 
				
				//TODO 其他特殊用法请查看 special_use_purchase.php
			);
		AcpService::sign ( $data );
		$uri = SDKConfig::getSDKConfig()->frontTransUrl;
		$html_form = AcpService::createAutoFormHtml( $data, $uri );
		return $html_form;
	}


	/**
	 * [orderQuery 支付查询]
	 * @Author   Peien
	 * @DateTime 2020-06-23T11:37:52+0800
	 * @param    array                    $params [description]
	 * @return   [type]                           [description]
	 */
	public function orderQuery($params = [])
	{
		$data = [
			//以下信息非特殊情况不需要改动
			'version' => SDKConfig::getSDKConfig()->version,		  //版本号
			'encoding' => 'utf-8',		  //编码方式
			'signMethod' => SDKConfig::getSDKConfig()->signMethod,		  //签名方法
			'txnType' => '00',		      //交易类型
			'txnSubType' => '00',		  //交易子类
			'bizType' => '000000',		  //业务类型
			'accessType' => '0',		  //接入类型
			'channelType' => '07',		  //渠道类型

			'orderId' => $params["order_sn"],	//请修改被查询的交易的订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数
			'merId' => $this->merId,	    //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
			'txnTime' => $params["pay_time"] ?? date('YmdHis'),	//请修改被查询的交易的订单发送时间，格式为YYYYMMDDhhmmss，此处默认取demo演示页面传递的参数
		];
		AcpService::sign ( $data ); // 签名
		$url = SDKConfig::getSDKConfig()->singleQueryUrl;
		$result_arr = AcpService::post ( $data, $url);
		if(count($result_arr)<=0) { //没收到200应答的情况
			//printResult ( $url, $data, "" );
			return ['code' => 0, 'message' => '无响应'];
		}

		//printResult ($url, $data, $result_arr ); //页面打印请求应答数据

		if (!AcpService::validate ($result_arr) ){
			return ['code' => 0, 'message' => '应答报文验签失败'];
		}

		if ($result_arr["respCode"] == "00"){
			if ($result_arr["origRespCode"] == "00"){
				return ['code' => 1, 'message' => '成功','data' => $result_arr];
				//交易成功
			} else if ($result_arr["origRespCode"] == "03"
					|| $result_arr["origRespCode"] == "04"
					|| $result_arr["origRespCode"] == "05"){
				//后续需发起交易状态查询交易确定交易状态
				return ['code' => 0, 'message' => '交易处理中，请稍微查询'];
				//return "交易处理中，请稍微查询。<br>\n";
			} else {
				//其他应答码做以失败处理
				return ['code' => 0, 'message' => "交易失败：" . $result_arr["origRespMsg"]];
			}
		} else if ($result_arr["respCode"] == "03"
				|| $result_arr["respCode"] == "04"
				|| $result_arr["respCode"] == "05" ){
			//后续需发起交易状态查询交易确定交易状态
			return ['code' => 0, 'message' => '处理超时，请稍微查询。'];

		} else {
			//其他应答码做以失败处理
			//TODO
			return ['code' => 0, 'message' => "失败：" . $result_arr["respMsg"]];
		}
	}


	public function refund($params = [])
	{
		$data = [
			//以下信息非特殊情况不需要改动
			'version' => SDKConfig::getSDKConfig()->version,		      //版本号
			'encoding' => 'utf-8',		      //编码方式
			'signMethod' => SDKConfig::getSDKConfig()->signMethod,		      //签名方法
			'txnType' => '04',		          //交易类型
			'txnSubType' => '00',		      //交易子类
			'bizType' => '000201',		      //业务类型
			'accessType' => '0',		      //接入类型
			'channelType' => '07',		      //渠道类型
			'backUrl' => SDKConfig::getSDKConfig()->backUrl, //后台通知地址
			
			//TODO 以下信息需要填写
			'orderId' => $params["order_sn"],	    //商户订单号，8-32位数字字母，不能含“-”或“_”，可以自行定制规则，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
			'merId' => $this->merId,	        //商户代码，请改成自己的测试商户号，此处默认取demo演示页面传递的参数
			'origQryId' => $params["origQryId"], //原消费的queryId，可以从查询接口或者通知接口中获取，此处默认取demo演示页面传递的参数
			'txnTime' => date('YmdHis'),
			   //订单发送时间，格式为YYYYMMDDhhmmss，重新产生，不同于原消费，此处默认取demo演示页面传递的参数
			'txnAmt' => $params["refund_fee"],       //交易金额，退货总金额需要小于等于原消费

			// 请求方保留域，
			// 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
			// 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
			// 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
			//    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
			// 2. 内容可能出现&={}[]"'符号时：
			// 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
			// 2) 如果对账文件没有显示要求，可做一下base64（如下）。
			//    注意控制数据长度，实际传输的数据长度不能超过1024位。
			//    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
			//    'reqReserved' => base64_encode('任意格式的信息都可以'),
		];
		AcpService::sign ( $data ); // 签名
		$url = SDKConfig::getSDKConfig()->backTransUrl;

		$result_arr = AcpService::post ( $data, $url);
		if(count($result_arr)<=0) { //没收到200应答的情况
			//printResult ( $url, $data, "" );
			return ['code' => 0, 'message' => '无响应'];
		}

		//printResult ($url, $params, $result_arr ); //页面打印请求应答数据

		if (!AcpService::validate ($result_arr) ){
			//echo "应答报文验签失败<br>\n";
			return ['code' => 0, 'message' => '应答报文验签失败'];
			//return;
		}

		if ($result_arr["respCode"] == "00"){
			return ['code' => 1, 'message' => '成功','data' => $result_arr];
			//return $result_arr;
		    //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
		    //TODO
		    //echo "受理成功。<br>\n";
		} else if ($result_arr["respCode"] == "03"
		 	    || $result_arr["respCode"] == "04"
		 	    || $result_arr["respCode"] == "05" ){
		    //后续需发起交易状态查询交易确定交易状态
		    //TODO
		    return ['code' => 0, 'message' => '交易处理中，请稍微查询'];
		    //return "处理超时，请稍微查询。<br>\n";
		} else {
		    //其他应答码做以失败处理
		     //TODO
		     return ['code' => 0, 'message' => "失败：" . $result_arr["respMsg"]];
		}

	}


	public function notify($params = [])
	{

		$info = AcpService::validate ( $params ) ? '验签成功' : '验签失败';

		$orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
		$respCode = $_POST ['respCode'];
		$queryResult = $this->orderQuery($params);
		
        //判断respCode=00、A6后，对涉及资金类的交易，请再发起查询接口查询，确定交易成功后更新数据库。
		//TODO 更新订单状态
		return true;
	}





}