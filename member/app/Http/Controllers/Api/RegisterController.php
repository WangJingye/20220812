<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Model\WechatUsers;
use App\Model\CrmCustomers;
use App\Model\CrmAuthToken;
use App\Jobs\UnionidToCrm;
use App\Jobs\CustomerInfoToDb;
use Validator;
use Exception;

class RegisterController extends Controller
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

    // 创建会员
	public function create(Request $request)
	{
		try {
			$fields = [
	            'familyName'  => [
	            	'required', 
	            	'string',
	            	'max:255',
	            	'regex:/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u'
	            ],
	            'giftName'  => [
	            	'required', 
	            	'string',
	            	'max:255',
	            	'regex:/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u'
	            ],
	            'password' => 'required|string|min:6',
	            'isStore' => 'required'
	        ];
	        $validator = Validator::make($request->all(), $fields, [
	                'required' => '请输入:attribute', // :attribute 字段占位符表示字段名称
	                'string'   => ':attribute为字符串',
	                'max'      => ':attribute大于了:max位',
	                'regex'    => ':attribute只支持中文和字符'
	            ],[
	            	'familyName' => '姓氏',
	            	'giftName' => '名字',
	            	'password' => '密码',
	            ]
	        );
	        if($validator->fails()){
	        	throw new Exception($validator->errors()->first(), 0);
	    	}

			$params = array_only($request->all(), array_keys($fields));
			$wechatUserData = WechatUsers::where('id', $this->wechatUserId)->first();

			$salute = '01';
			$gender = 'M'; // 默认为男
			if($wechatUserData && $wechatUserData->gender) {
				if($wechatUserData->gender == '1'){
					$salute = '01';
					$gender = 'M'; 
				} else if($wechatUserData->gender == '2'){
					$salute = '02';
					$gender = 'F';
				}
			}

			$phoneCode = $request->input('phoneCode') ? $request->input('phoneCode') : '86';
			$phoneCode = str_replace('+','',$phoneCode);
			$phone = $request->input('phone') ? $request->input('phone') : $wechatUserData['phone'];

			$authToken = new CrmAuthToken;

			$url = $authToken->domain . 'customers';
			$data = $db = [];
			$data['salute'] = $salute; // 微信称谓 [01(男), 02（女）, 03, 04]
			$data['familyName'] = $db['family_name'] = $params['familyName']; // 姓
			$data['firstName'] = $db['first_name'] = $params['giftName']; // 名

			$data['residenceCountry'] = $db['residence_country'] = 'CN'; // 居住地
			$data['mobileCountryCode'] = $db['mobile_country_code'] = $phoneCode; // 国家代码
			$data['mobileNumber'] = $db['mobile_number'] = $phone; // 手机号
			$data['stateCode'] = $db['stateCode'] = 'V'; // 周友可登录状态 ['P', 'V']
			$data['email'] = $db['email'] = "NA"; // Email
			// $data['emailState'] = 'P'; // Email
			$data['password'] = $params['password']; // Password
			$data['optOutEDM'] = 'Y'; // 同意收取推廣電郵 [ Y, N ]
			$data['optOutPhone'] = 'Y'; // 	同意收取推廣電話/短訊 [ Y, N ]
			$data['memberClass'] = $db['member_class'] = 'FS'; // 會員等級 [ A1, A8, AA, 01, 02, 06 ]
			$data['memberMovement'] = 'R'; // 會籍變化 [ R, T, U, D, A, E ]
			$data['fromChannel'] = '09'; // 開戶渠道 [ 01, 02, 03, 04, 05, 06, 07, 08 ]
		
			$authToken->aHeader[] = 'Content-Type: application/json';
		
			$response = http_request($url, $data, $authToken->aHeader, 'POST', '周友注册：');
			$result = json_decode($response['data'], true);
			
			if($response['httpCode'] != 200 && array_key_exists('errors', $result)) {
				$errCode = $result['errors'][0]['errorCode'];
				$errMsgConfig = config('errorCode');
				$errMsg  = array_key_exists($errCode, $errMsgConfig) ? $errMsgConfig[$errCode] : $errCode;
				throw new Exception($errMsg, 0);
				
			}
			$db['date_of_birth'] = '01-01';
			$db['wechat_user_id'] = $this->wechatUserId;
			$db['customer_id'] = $result['id'];
			$db['gender'] = $gender;
			$db['salute'] = $salute;
			$db['fromchannel'] = is_string($params['isStore']) && $params['isStore'] == 'true' ? 3 : 1; // 3：分店导购入会 / 1：小程序注册的周友
			$db['available'] = 1; // 周友状态，为0代表不可在商城下单
			$db['created_at'] = $db['updated_at'] = date('Y-m-d H:i:s', time());
	 		// 插入数据库
			CrmCustomers::insert($db); // 插入失败， 记录日志

			// 异步处理 - 绑定unionId
			dispatch((new UnionidToCrm($result['id'], $wechatUserData, $gender, $authToken->aHeader))->onQueue('default'));

			$loginToken = CrmCustomers::getLoginToken($this->wechatUserId, $this->openId, $result['id']);
			$message = '新增成功';
			$data = [];
			$data['token'] = $loginToken;
			return response()->ajax($message);

		} catch (Exception $e) {
        	return response()->errorAjax($e);
		}
	}

	// 忘记密码，通过FamilyName、giftName验证合法性
	public function customerByInfo(Request $request)
	{
		try {
			$fields = [
				'phoneCode' => [
	                'required','numeric',
	                Rule::in([86,852,853,886])
	            ],
	            'phone'  => 'required',
	            'familyName'  => [
	            	'required', 
	            	'string',
	            	'max:255',
	            	'regex:/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u'
	            ],
	            'giftName'  => [
	            	'required', 
	            	'string',
	            	'max:255',
	            	'regex:/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u'
	            ]
	        ];
	        $validator = Validator::make($request->all(), $fields, [
	                'required' 	=> '请输入:attribute', // :attribute 字段占位符表示字段名称
	                'numeric'  	=> ':attribute格式不正确',
	                'in'       	=> '请选择正确的:attribute',
	            	'mobile'	=> '请填写正确的:attribute',
	            	'size'		=> '请填写正确的:attribute',
	            	'regex'    	=> ':attribute只支持中文和字符'
	            ],[
	            	'familyName' => '姓氏',
	            	'giftName' 	=> '名字',
	            	'phoneCode' => '地区代码',
	            	'phone' 	=> '手机号码'
	            ]
	        );
	        $validator->sometimes('phone', 'required|mobile', function ($input) {
			    return $input->phoneCode == 86;
			});
			$validator->sometimes('phone', 'required|size:8', function ($input) {
			    return in_array($input->phoneCode, [852,853,886]);
			});
	        if($validator->fails()){
	        	throw new Exception($validator->errors()->first(), 0);
	    	}

	    	$data 		= array_only($request->all(), array_keys($fields));
			$familyName = array_get($data, 'familyName');
	    	$firstName 	= array_get($data, 'giftName');
			$countryCode = array_get($data, 'phoneCode');
			$phone = array_get($data, 'phone');

	    	$query = [];
			$query['countryCode'] = $countryCode;
			$query['mobile'] = $phone;
			$quertStr = http_build_query($query);
			$uri = 'customers/search/criteria?' . $quertStr;
			$authToken = new CrmAuthToken();
			$url = $authToken->domain . $uri;
			$response = http_request($url, [], $authToken->aHeader, 'GET', '手机号判断是否周友：');
			$result = json_decode($response['data'], true);

			if($response['httpCode'] !== 200 && array_key_exists('errors', $result)) {
				$errCode = $result['errors'][0]['errorCode'];
				$errMsgConfig = config('errorCode');
				$errMsg  = array_key_exists($errCode, $errMsgConfig) ? $errMsgConfig[$errCode] : $errCode;
				throw new Exception($errMsg, 0);
			}
			
			$message = '该账号暂时还不是周友';

			if(count($result)) {
				$validCustomer = collect($result)->where('stateCode', 'V')->where('firstName', $firstName)->where('familyName', $familyName)->all();
				if(!$validCustomer) {
	        		throw new Exception('该账号是未认证周友或填写资料不正确', 0);

				}
				$validCustomer = array_values($validCustomer);
				$message = '该账号已是周友';
				$customer_id = $validCustomer[0]['id'];

				// 发送验证码
		    	$authToken->aHeader[] = 'Content-Type: application/json';

		    	$url = $authToken->domain . 'notifications/sms/pincodes';

		    	$smsData = [];
		    	$smsData['mobileCountryCode'] = $countryCode; //国家（区）代码
		    	$smsData['mobileNumber'] 	= $phone;
		    	$smsData['familyName'] 		= $familyName;
		    	$smsData['firstName'] 		= $firstName;
		    	$smsData['messageType'] 	= 'R'; // R:發送重設密碼一次性驗證碼 SMS, W:發送成功開戶 SMS

		    	$response = http_request($url, $smsData, $authToken->aHeader, 'POST', '发送短信验证码（忘记密码）：');

		    	$result = json_decode($response['data'], true);

		    	if($response['httpCode'] !== 201){
		    		if($result && array_key_exists('errors', $result)) {
		    			$errCode = $result['errors'][0]['errorCode'];
						$errMsgConfig = config('errorCode');
						$errMsg  = array_key_exists($errCode, $errMsgConfig) ? $errMsgConfig[$errCode] : $errCode;
						throw new Exception($errMsg, 0);
					}
		  			throw new Exception('验证码发送异常', 0);
		    	}

		    	$customId = $result['customerId'];			
				return response()->ajax($message, compact('customer_id', 'customId'));
			}
			throw new Exception($message, 0);
		} catch (Exception $e) {
        	return response()->errorAjax($e);
		}
	}

	// 已是周友，忘记密码
	public function forgetPassword(Request $request)
	{	
		try {
			$fields = [
			 	'customer_id' 	=> 'required|string',
			 	'customId' 		=> 'required|string',
			 	'phoneCode' 	=>	[
	                'required','numeric',
	                Rule::in([86,852,853,886])
	            ],
				'phone' 	=> 'required',
			 	'verify' 	=> 'required|numeric',
	            'password' 	=> 'required|string|min:6',
	        ];
	    	$validator = Validator::make($request->all(), $fields, [
	            'required' 	=> ':attribute 为必填项',//:attribute 字段占位符表示字段名称
	            'string'   	=> ':attribute 为字符串',
	            'min'      	=> ':attribute 不得少于 :min 位',
	            'in'       	=> 	'请选择正确的:attribute',
            	'mobile'	=>  '请填写正确的:attribute',
            	'size'		=>  '请填写正确的:attribute'
	            
	        ],[
	            'password' 	=> '密码',
	            'phoneCode' => '地区代码',
	        ]);

	        $validator->sometimes('phone', 'required|mobile', function ($input) {
			    return $input->phoneCode == 86;
			});
			$validator->sometimes('phone', 'required|size:8', function ($input) {
			    return in_array($input->phoneCode, [852,853,886]);
			});
	        if($validator->fails()) {
	        	throw new Exception($validator->errors()->first(), 0);
	        	
	        }
	        $data = array_only($request->all(), array_keys($fields));
	        $customerId = array_get($data, 'customer_id');
	        $smsCustomerId = array_get($data, 'customId');
	        $mobCtryCde = array_get($data, 'phoneCode');
	        $mobile = array_get($data, 'phone');
	        $smsPin = array_get($data, 'verify');

	        $authToken = new CrmAuthToken();
	      	$result = $authToken->forgetPasswordValidVerify($smsCustomerId, $mobCtryCde, $mobile, $smsPin);
	    	
	    	if(!$result){
	    		throw new Exception("验证码错误", 0);
	    	}

			// 校验成功			
	        $url = $authToken->domain . 'customers/' . $customerId;
	        $password = array_get($data, 'password');
	        $query = ['password' => $password];

	        $response = http_request($url, $query, $authToken->aHeader, 'PATCH', '更新周友密码：');
	        $result = json_decode($response['data'], true);

	        if(array_key_exists('errors', $result)) {
				throw new Exception($result['errors'][0]['message'], 0);
			}
	       	// 密码更新成功
	        return response()->ajax('Password updated successfully！');
        } catch (Exception $e) {
        	return response()->errorAjax($e);
		}
	}

    public function customerByUnionId(Request $request)
    {
    	try {
	    	$fields = [
				'encryptedData' => 'required|string|max:1000',
				'iv'  => 'required|string|max:255',
	        ];
	    	$validator = Validator::make($request->all(), $fields, [
	            'required' => ':attribute 为必填项',//:attribute 字段占位符表示字段名称
	            'string'   => ':attribute 为字符串'
	        ]);
	        if($validator->fails()) {
	        	throw new Exception($validator->errors()->first(), 0);
	        }
	        $data = array_only($request->all(), array_keys($fields));
	        $encryptedData = array_get($data, 'encryptedData');
	        $iv   = array_get($data, 'iv');

	        $res = WechatUsers::getUnionid($encryptedData, $iv, $this->openId);
	        if(is_string($res)) { 
	        	throw new Exception($res, 2);
	        }
	        // 
	        if(!$res['unionid']){
				return response()->ajax("该unionId还不是周友", ['isBind' => false]);
	        }

	        $uri = 'wechat-binds/search/criteria?unionId=' . $res['unionid'];
	      	$authToken = new CrmAuthToken();
			$url = $authToken->domain . $uri;
			$response = http_request($url, [], $authToken->aHeader, 'GET', '通过unionId查询是否为周友：');
			$result = json_decode($response['data'], true);

			if($response['httpCode'] == 400 && array_key_exists('errors', $result)) {
				$errCode = $result['errors'][0]['errorCode'];
				$errMsgConfig = config('errorCode');
				$errMsg  = array_key_exists($errCode, $errMsgConfig) ? $errMsgConfig[$errCode] : $errCode;
				throw new Exception($errMsg, 0);
			} else if($response['httpCode'] == 404){
				return response()->ajax("该unionId还不是周友", ['isBind' => false]);

			} else if($response['httpCode'] == 200){
				$crmId = $result[0]['customerId'];

 				// 异步处理 - 同步周友信息至DB
				dispatch((new CustomerInfoToDb($crmId, $authToken->aHeader, $this->wechatUserId))->onQueue('default'));

		        $loginToken = CrmCustomers::getLoginToken($this->wechatUserId, $this->openId, $crmId);
				$message = '该unionId已是周友';
				return response()->ajax($message, ['isBind' => true, 'openid' => $loginToken]);
			}
	       
	    } catch (Exception $e) {
    		return response()->errorAjax($e);
    	}
    }

    public function checkLogin(Request $request)
    {	
    	return response()->ajax('该账号处于已登录状态');
    }

}
