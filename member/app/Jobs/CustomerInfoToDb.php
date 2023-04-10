<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\CrmCustomers;

class CustomerInfoToDb implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    protected $aHeader;

    protected $wechatUserId;

    protected $customerId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customerId, $aHeader, $wechatUserId)
    {
        //
        $this->url = env('CRM_CUSTOMER_DOMAIN') . 'customers/' . $customerId;

        $this->aHeader = $aHeader;

        $this->wechatUserId = $wechatUserId;

        $this->customerId = $customerId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $response = http_request($this->url, [], $this->aHeader, 'GET', '周友信息同步：');
        if($response['httpCode'] == 200) {
            $result = json_decode($response['data'], true);
            $customer = CrmCustomers::where('wechat_user_id', $this->wechatUserId)->where('customer_id', $this->customerId)->first();
            if(!$customer){
                logger('老周友新增至DB：', $result);
                $crmCustomer = new CrmCustomers();
                $crmCustomer->wechat_user_id = $this->wechatUserId;
                $crmCustomer->customer_id = $this->customerId;
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
                $familyAccount = CrmCustomers::where('customer_id', $this->customerId)->first();
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
            logger('同步周友信息至DB：', ['customerId:' . $this->customerId]);
            return true;
        }
        logger('同步周友信息至DB异常：', ['customerId:'.$this->customerId]);
        return false;
    }
}
