<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\XmlToArray;
use App\Model\CrmCustomers;
use App\Model\CrmAuthToken;
use App\Model\WechatUsers;
use App\Jobs\CustomerInfoToDb;
use App\Jobs\UnionidToCrm;
use Validator;
use Exception;

class LoginController extends Controller
{
	protected $wechatUserId;

	protected $openId;

	public function __construct(Request $request)
	{ 
		$encryptString = array_get($request->all(), 'openid');

		$decrypted = decrypt($encryptString);
		
		$this->wechatUserId = $decrypted['wechatUserId'];

		$this->openId = $decrypted['openId'];
	}

    // 登录(手机号/邮箱 + 密码)
	public function login(Request $request)
	{
		try {
			$fields = [
				'openid' => 'required', 
				'phoneMail' => 'required|string',
	            'password'  => 'required|string|min:6'
	        ];
	        $validator = Validator::make($request->all(), $fields, [
	                'required' 	=> '请输入:attribute', // :attribute 字段占位符表示字段名称
	                'string'   	=> ':attribute 为字符串',
	                'min'		=> ':attribute至少:min位字符长度'
	            ],[
	            	'phoneMail' => '邮箱或手机号',
	            	'password' => '密码',
	            ]
	        );
	        if($validator->fails()){
	        	throw new Exception($validator->errors()->first(), 0);
	    	}

			$paramData = array_only($request->all(), array_keys($fields));
			$openId = $this->openId;
			$wechatUserId = $this->wechatUserId;
			$phoneMail = array_get($paramData, 'phoneMail');
			$password = array_get($paramData, 'password');
			$phoneCode = $request->input('phoneCode', '');
			$username = trim($phoneCode . $phoneMail);
			// 从Redis获取Token（$userid, $wechatUserId, $crmid）
			$query = [];
			$query['username'] = $username;
			$query['password'] = $password;
			$quertStr = http_build_query($query);
			
			$url = getenv("CAS_DOMAIN") . 'cas/v1/tickets';

			$response = curl_post_service($url, $quertStr, 'Login1 service:');
			if($response['httpCode'] == 201){
				$service = getenv("CAS_DOMAIN");
				preg_match('/action[\s]*?=[\s]*?([\'\"])(.*?)\1/', $response['data'], $matchBody);
				$action = $matchBody[2];

				$ticketInfo = curl_post_ticket($action, $service, 'Login2 Ticket:');
				
				if($ticketInfo['httpCode'] == 200){
					$casUrlParams = [];
					$casUrlParams['service'] = $service;
					$casUrlParams['format'] = 'xml';
					$casUrlParams['ticket'] = $ticketInfo['data'];

					$param_str = http_build_query($casUrlParams);
					
					$casUrl = getenv("CAS_DOMAIN") . "cas/p3/serviceValidate?".$param_str;

					$xmlResponse = curl_get_customer($casUrl, 'Login3 CustomerXML:');

					$xml = $xmlResponse['data'];

					$respData   = XmlToArray::convert($xml, true);
						
					$crmId = $respData['cas:authenticationSuccess']['cas:user'];
				       
			        $loginToken = CrmCustomers::getLoginToken($wechatUserId, $openId, $crmId);

 					$authToken = new CrmAuthToken;

			        // 异步处理 - 同步周友信息至DB
					dispatch((new CustomerInfoToDb($crmId, $authToken->aHeader, $wechatUserId))->onQueue('default'));

					$wechatUserData = WechatUsers::where('id', $this->wechatUserId)->first();

					$gender = 'M'; // 默认为男
					if($wechatUserData && $wechatUserData->gender) {
						if($wechatUserData->gender == '1'){
							$gender = 'M'; 
						} else if($wechatUserData->gender == '2'){
							$gender = 'F';
						}
					}

					// 异步处理 - 绑定unionId
					dispatch((new UnionidToCrm($crmId, $wechatUserData, $gender, $authToken->aHeader))->onQueue('default'));

			        $message = "登录成功";
			        $data = [];
			        $data['token'] = $loginToken;

					return response()->ajax($message, $data);
				} else {
					throw new Exception('请输入正确的用户名或密码', 0);
				}
			} else {
				throw new Exception('请输入正确的用户名或密码', 0);
			}
		
		} catch (Exception $e) {
        	return response()->errorAjax($e);
		}

	}
}
