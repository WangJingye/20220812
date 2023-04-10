<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Model\FormCodeLog;
use App\Model\CrmAuthToken;
use Validator;
use Exception;


class SmsController extends Controller
{
	// 拒绝手机号授权，地区代码 + 手机号，发送验证码
    public function sendByPhone(Request $request)
    {
    	try {
    		$fields = [
	            'phoneCode'  =>	[
	                'required','numeric',
	                Rule::in([86,852,853,886])
	            ],
                'phone' 	=>	'required',
	        ];
	        $validator = Validator::make($request->all(), $fields, [
	                'required' 	=> 	':attribute为必填项', // :attribute 字段占位符表示字段名称
	            	'numeric' 	=> 	':attribute为数字类型',
	            	'in'      	=> 	'请选择正确的:attribute',
	            	'mobile'	=>  '请填写正确的:attribute',
	            	'size'		=>  '请填写正确的:attribute'

	            ],[
	                'phoneCode'	=>	'区号',
	                'phone'		=>	'手机号码',
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

        	$params = array_only($request->all(), array_keys($fields));

	    	$authToken = new CrmAuthToken();
	    	$authToken->aHeader[] = 'Content-Type: application/json';

	    	$url = $authToken->domain . 'notifications/wechat/pincodes';

	    	$mobileCountryCode = array_get($params, 'phoneCode'); //国家（区）代码
	    	$mobileNumber = array_get($params, 'phone');

	    	$response = http_request($url, compact('mobileCountryCode', 'mobileNumber'), $authToken->aHeader, 'POST', '发送短信验证码：');

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
				
	    	$message = '发送成功';
	        return response()->ajax($message);
        } catch (Exception $e) {
        	return response()->errorAjax($e);
        }	

    }

    // 注册，拒绝手机号授权，校验手机验证码
	public function smsForm(Request $request)
	{
		try {
    		$fields = [
	            'phoneCode' =>	[
	                'required','numeric',
	                Rule::in([86,852,853,886])
	            ],
                'phone' 	=>	'required',
                'verify' => 'required|numeric'
	        ];
	        $validator = Validator::make($request->all(), $fields, [
	                'required' 	=> 	':attribute 为必填项', // :attribute 字段占位符表示字段名称
	            	'numeric' 	=> 	':attribute 为数字类型',
	            	'in'      	=> 	'请选择正确的:attribute',
	            	'mobile'	=>  '请填写正确的:attribute',
	            	'size'		=>  '请填写正确的:attribute'
	            ],[
	            	'phoneCode' => '区码',
	                'phone'	=>	'手机号码',
	            	'verify' => '验证码'
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
        	$params = array_only($request->all(), array_keys($fields));

	    	$countryCode = array_get($params, 'phoneCode'); //地区代码
	    	$phone = array_get($params, 'phone');
	    	$smsPin = array_get($params, 'verify');

	        $authToken = new CrmAuthToken();
	    	
			$result = $authToken->validVerify($countryCode, $phone, $smsPin, '拒绝手机号授权，校验手机验证码：');
	    	
	    	if(!$result){
	    		throw new Exception("请输入正确的验证码", 0);
	    	}

	    	$query = [];
			$query['mobile'] = $phone;
			$query['countryCode'] = $countryCode;
			$quertStr = http_build_query($query);

			$uri = 'customers/search/criteria?' . $quertStr;
			$url = $authToken->domain . $uri;
			$response = http_request($url, [], $authToken->aHeader, 'GET', '手机号 判断是否周友：');
			
			$result = json_decode($response['data'], true);
			if($response['httpCode'] != 200 && array_key_exists('errors', $result)) {
				$errCode = $result['errors'][0]['errorCode'];
				$errMsgConfig = config('errorCode');
				$errMsg  = array_key_exists($errCode, $errMsgConfig) ? $errMsgConfig[$errCode] : $errCode;
				throw new Exception($errMsg, 0);
			}
			$message = '该账号暂时还不是周友';
			$isMember = 0;
			if(count($result)) {
				$validCustomer = collect($result)->where('stateCode', 'V')->all();
				if(!$validCustomer) {
					
					return response()->ajax('该账号是未认证周友', ['isMember' => $isMember]);
				}
				$isMember = 1;
				return response()->ajax('该账号已是周友', ['isMember' => $isMember]);
			}
			return response()->ajax($message, ['isMember' => $isMember]);

		} catch (Exception $e) {
        	return response()->errorAjax($e);
        }
	}
	
}