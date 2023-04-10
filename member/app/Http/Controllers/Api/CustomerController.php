<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CrmAuthToken;
use App\Model\CrmCustomers;
use Validator;
use Exception;

class CustomerController extends Controller
{
	protected $wechatUserId;

	protected $crmId;

	public function __construct(Request $request)
	{ 
		$encryptString = array_get($request->all(), 'openid');

		$decrypted = decrypt($encryptString);

		$this->wechatUserId = $decrypted['wechatUserId'];

		$this->crmId = $decrypted['crmId'];
	}

	// 我的会员信息
	public function account(Request $request)
	{
		try {
			$authToken = new CrmAuthToken;

			// 1.悦享钱余额
			$balance_url = $authToken->domain . 'customers/' . $this->crmId . '/stardollar-balances';
	        $balance_response = http_request($balance_url, [], $authToken->aHeader, 'GET', '获取周友悦享钱余额：');
	        if($balance_response['httpCode'] != 200) {
	        	$point = '-';
	        	
	        } else {
	        	$balance_result = json_decode($balance_response['data'], true);
	        	$point = $balance_result['usableStarDollar'];
	        }
	        

			// 2.通过crmId 获取会员等级和称谓
			$customer_url = $authToken->domain . 'customers/' . $this->crmId;
	        $customer_response = http_request($customer_url, [], $authToken->aHeader, 'GET', '通过crmId 获取会员等级和称谓：');
	        $name = '';
	        $level = '';
	        $sexy = '';
	        if($customer_response['httpCode'] == 200) {
	      
	        	$customer_result = json_decode($customer_response['data'], true);
	        	$name = $customer_result['familyName'] . $customer_result['firstName'];
	        	switch ($customer_result['salute']) {
		        	case '01':
		        		$sexy = '先生';
		        		break;
		        	case '02':
		        		$sexy = '小姐';
		        		break;
		        	case '03':
		        		$sexy = '女士';
		        		break;
		        	case '04':
		        		$sexy = '太太';
		        		break;
		        	default:
		        		$sexy = '先生';
		        		break;
		        }

		        switch ($customer_result['memberClass']) {
		        	case 'FS':
		        		$level = '基本会员';
		        		break;
		        	case 'A1':
		        		$level = '尊尚会员';
		        		break;
		        	case 'A8':
		        		$level = '尊尚会员';
		        		break;
		        	case 'AA':
		        		$level = '高级会员';
		        		break;
		        	case '01':
		        		$level = '尊尚会员';
		        		break;
		        	case '02':
		        		$level = '员工会员';
		        		break;
		        	case '06':
		        		$level = '过渡员工会员';
		        		break;
		        	default:
		        		$level = '基本会员';
		        		break;
		        }

		        $crmCustomer = CrmCustomers::where(['customer_id' => $this->crmId, 'wechat_user_id' => $this->wechatUserId])->first();
		       	if(!$crmCustomer){
		       		$crmCustomer = new CrmCustomers();
		       		$crmCustomer->wechat_user_id = $this->wechatUserId;
		       		$crmCustomer->customer_id = $this->crmId;
		       		$crmCustomer->fromchannel = 2; //老周友
		       	}
	      
		       	$crmCustomer->family_name = $customer_result['familyName'];
		       	$crmCustomer->first_name = $customer_result['firstName'];
		       	$crmCustomer->gender = $customer_result['gender'];
		       	$crmCustomer->salute = $customer_result['salute'];
		       	$crmCustomer->mobile_country_code = $customer_result['mobileCountryCode'];
		       	$crmCustomer->mobile_number = $customer_result['mobileNumber'];
		       	$crmCustomer->member_class = in_array($customer_result['memberClass'], ['01', 'A8', 'A1']) ? 'A1' : $customer_result['memberClass'];
		       	$crmCustomer->date_of_birth = $customer_result['dateOfBirth'];
		       	$crmCustomer->email = $customer_result['email'];
		       	$crmCustomer->residence_country = $customer_result['residenceCountry'];
		       	$crmCustomer->stateCode = $customer_result['stateCode'];
				$crmCustomer->save();
	        }

	        $data = [];
	        $data['level'] = $level;
	        $data['point'] = $point;
	       	$data['number'] = $this->crmId;
	       	$data['name'] = $name;
	       	$data['sexy'] = $sexy;

			// 返回会员码
	        return response()->ajax('获取周友个人信息', $data);
	    } catch (Exception $e) {
        	return response()->errorAjax($e);

		}
	}

	// 我的会员码
	public function myQrcode(Request $request)
	{
		try {
			$authToken = new CrmAuthToken;

			// 1.通过crmId 获取会员码
			$customer_url = $authToken->domain . 'customers/' . $this->crmId;
	        $customer_response = http_request($customer_url, [], $authToken->aHeader, 'GET', '通过crmId 获取会员码：');

	        $qrcode = '';
	        if($customer_response['httpCode'] == 200) {
	  
	        	$customer_result = json_decode($customer_response['data'], true);
		        $encryptedId = $customer_result['encryptedId'];
		        include '../app/Common/Qrcode/phpqrcode.class.php';
				$p=new \QRcode();
				$file_name='../storage/'.date("Y-m-d");
				ob_start();
				$p->png($encryptedId,false);
				$qrcode = base64_encode(ob_get_contents());
				ob_end_clean();
	        }
	        
	        $data = [];
	        $data['qrcode'] = $qrcode;
			// 返回会员码
	        return response()->ajax('获取周友会员码', $data);
	    } catch (Exception $e) {
        	return response()->errorAjax($e);

		}
	}

	// 获取会员信息
	public function getCustomerInfo(Request $request) 
	{
		try {
			$this->customerInfoToDB($this->wechatUserId, $this->crmId);
			$customerInfo = CrmCustomers::where('wechat_user_id', $this->wechatUserId)->where('customer_id', $this->crmId)->first();
			if(!$customerInfo){
				throw new Exception("您查找的周友不存在！", 0);
			}

			return response()->ajax('获取会员信息', $customerInfo);

		} catch (Exception $e) {

        	return response()->errorAjax($e);
		}
	}

	private function customerInfoToDB($wechatUserId, $customerId) 
	{
		$url = env('CRM_CUSTOMER_DOMAIN') . 'customers/' . $customerId;
		$authToken = new CrmAuthToken;
		$response = http_request($url, [], $authToken->aHeader, 'GET', '周友信息同步：');
        if($response['httpCode'] == 200) {
            $result = json_decode($response['data'], true);
            $customer = CrmCustomers::where('wechat_user_id', $wechatUserId)->where('customer_id', $customerId)->first();
            if(!$customer){
                logger('老周友新增至DB：', $result);
                $crmCustomer = new CrmCustomers();
                $crmCustomer->wechat_user_id = $wechatUserId;
                $crmCustomer->customer_id = $customerId;
                $crmCustomer->family_name = $result['familyName'];
                $crmCustomer->first_name = $result['firstName'];
                $crmCustomer->gender = $result['gender'];
                $crmCustomer->salute = $result['salute'];
                $crmCustomer->mobile_country_code = $result['mobileCountryCode'];
                $crmCustomer->mobile_number = $result['mobileNumber'];
                $crmCustomer->member_class = in_array($result['memberClass'], ['01', 'A8', 'A1']) ? 'A1' : $result['memberClass'];
                $crmCustomer->date_of_birth = $result['dateOfBirth'];
                $crmCustomer->email = $result['email'];
                $crmCustomer->residence_country = $result['residenceCountry'];
               
                $crmCustomer->stateCode = $result['stateCode'];
                $crmCustomer->available = 1; // 激活状态
                $familyAccount = CrmCustomers::where('customer_id', $customerId)->first();
                $crmCustomer->fromchannel = $familyAccount ? array_search($familyAccount->fromchannel, CrmCustomers::CHANNEL) : 2; //老周友
                $crmCustomer->save();
            } else {

                logger('更新周友信息：', $result);
                $customer->gender = $result['gender'];
                $customer->salute = $result['salute'];
                $customer->date_of_birth = $result['dateOfBirth'];
                $customer->residence_country = $result['residenceCountry'];
                $customer->member_class = in_array($result['memberClass'], ['01', 'A8', 'A1']) ? 'A1' : $result['memberClass'];
                $customer->stateCode = $result['stateCode'];
                $customer->save();
            }
            logger('同步周友信息至DB：', ['customerId:' . $customerId]);
            return true;
        }
        logger('同步周友信息至DB异常：', ['customerId:'.$customerId]);
        return false;
	}
}