<?php  

namespace App\Model;

use App\Model\CrmAuthToken;
use Illuminate\Support\Facades\Redis;

trait CrmTokenTrait
{
	
    public $domain;

    public $aHeader;

    public function __construct()
    {
        $this->domain = env('CRM_CUSTOMER_DOMAIN');
        $this->aHeader[] = 'accept: application/json';
        $this->aHeader[] = 'Authorization: ' . $this->getToken();
       
    }

    // 获取接口token
    public function getToken()
    {

        $now = time();
        $tokenData = Redis::get('crm-token');
        if(empty($tokenData)){
            $tokenData = $this->authCrmToken();
        }
       
        logger('crm接口token', [$tokenData]);
        return $tokenData;
    }

    // CRM生成Token
    public function authCrmToken() 
    {
        $url = $this->domain . 'authentication/token';
        $response = http_request($url, false, $this->aHeader, 'GET', '获取crm Token：');

        $result = json_decode($response['data'], true);
        $expiration = strtotime($result['expiration']);
        $tokenExpired = $expiration - time() - 10;

        Redis::setex('crm-token', $tokenExpired, $result['token']);
       
        return $result['token'];
    }

    // 登录-校验验证码
    public function validVerify($mobCtryCde, $mobile, $smsPin, $message)
    {
        $query = [];
        $query['mobCtryCde'] = $mobCtryCde; //国家（区）代码
        $query['mobNbr'] = $mobile;
        $query['smsPin'] = $smsPin;
        $quertStr = http_build_query($query);
        // 登录时校验验证码合法性
        $url = $this->domain . 'sms-verifications/wechat?'. $quertStr;

        $response = http_request($url, false, $this->aHeader, 'GET', $message);
        
        return $response['httpCode'] == 200 ? true : false;
    } 

     // 忘记密码-校验验证码
    public function forgetPasswordValidVerify($customerId, $mobCtryCde, $mobile, $smsPin)
    {
        $query = [];
        $query['customerId'] = $customerId;
        $query['mobileCountryCode'] = $mobCtryCde; //国家（区）代码
        $query['mobileNumber'] = $mobile;
        $query['pinCode'] = $smsPin;
        // 登录时校验验证码合法性
        $url = $this->domain . 'sms-verifications';

        $this->aHeader[] = 'Content-Type: application/json';

        $response = http_request($url, $query, $this->aHeader, 'POST', '忘记密码, 短信验证码校验：');
        
        return $response['httpCode'] == 201 ? true : false;
    } 

}