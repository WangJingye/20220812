<?php


namespace App\Service;


use Exception;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\RequestOptions;

class CrmUsersService
{
    public $client;
    public $domain;//crm接口域名
    private static $options = [
        RequestOptions::VERIFY => false,
        RequestOptions::TIMEOUT => 5
    ];

    public function __construct()
    {

        $this->client = new Client(self::$options);
        $this->domain = config('crm.crm_domain');

    }

    /**
     * xml转数组
     * @param $contents
     * @return mixed
     */
    public function xmlToArray($contents)
    {
        $object_str = simplexml_load_string($contents);
        return json_decode($object_str, true);
    }


    /**
     * 通过手机号判断用户是否存在
     * @param $mobile
     * @return int
     */
    public function userExist_noUse($mobile)
    {
        $url = $this->domain . '/CustomerSIDbyMobile_query';
        $form_params = [
            'mobile' => $mobile
        ];

        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();
        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            $data = $this->xmlToArray($contents);
            if ($data && !empty($data['Customersid'])) {
                return $data['Customersid'];
            }
        }
        return false;
    }

    /**
     * 通过手机号判断用户是否存在
     * @param $mobile
     * @return int
     */
    public function userExist($mobile, $pos_id = '')
    {
        $url = $this->domain . '/SisleyCustomer';
        $form_params = [
            'Customersid' => $pos_id,
            'Mobile' => $mobile
        ];

        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();
        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            $data = $this->xmlToArray($contents);
            if (isset($data['ErrCode']) && $data['ErrCode'] == '0' && isset($data['ResultData'][0])) {
                return $data['ResultData'][0]['CustomerSID'];
            }

        }
        return false;
    }

    /**
     * 创建用户 获取用户customerids
     * @param $params
     * @return bool
     */
    public function createUSer($params)
    {
        $birth_time = strtotime($params['birth']);
        $url = $this->domain . '/CustomerAddByPos';
        $code = config('crm.BA');

        $form_params = [
            'CustomerID' => $params['pos_id'],
            'CustomerName' => $params['name'],
            'CustomerSID' => $params['pos_id'],
            'Province' => '',
            'City' => '',
            'County' => '',
            'Store' => $code,
            'BACode' => $code,
            'Birthday' => $params['birth'],
            'Gender' => $params['sex'] == 'F' ? 'female' : 'male',
            'Mobile' => $params['phone'],
            'Telephone' => '',
            'Email' => $params['email'],
            'Address' => '',
            'Zipcode' => '',
            'CustomerType' => '潜客',
            'BirthdayYear' => date('Y', $birth_time),
            'BirthdayMonth' => date('m', $birth_time),
            'BirthdayDay' => date('d', $birth_time),
            'AgeGroup' => '1',
            'IdentifyingCode' => '3'
        ];
        Log::info('user_start' . time(), []);
        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();
        Log::info('user_end' . time(), []);
        $contents = '';
        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            loger(['request' => $form_params, 'response' => $contents], 'crmRegisterSuccess');
            $data = $this->xmlToArray($contents);
            if (isset($data['Code']) && $data['Code'] == 0 && $data['Msg'] == 'Success') {
                return true;
            }

        }
        loger(['request' => $form_params, 'code' => $code, 'response' => $contents], 'crmRegisterError');
        return false;
    }

    /**
     * 查询用户信息
     * @param $params
     * @return bool
     */
    public function userInfo($params)
    {
        try {
            $url = $this->domain . '/SisleyCustomer';
            if (empty($params['pos_id']) && empty($params['phone'])) {
                return false;
            }

            $form_params = [
                'Customersid' => $params['pos_id'] ?? '',
                'Mobile' => $params['phone'] ?? ''
            ];
            if ($form_params['Mobile'] && $form_params['Customersid']) {
                $form_params['Customersid'] = '';
            }

            $response = $this->client->request('post', $url, ['form_params' => $form_params]);
            $code = $response->getStatusCode();
            if ($code == 200) {
                $contents = $response->getBody()->getContents();
                loger(['request' => $form_params, 'response' => $contents], 'getSisleyCustomerSuccess');
                $data = $this->xmlToArray($contents);
                if (isset($data['ErrCode']) && $data['ErrCode'] == '0' && isset($data['ResultData'][0])) {
                    $original = [];
                    $new = [];
                    foreach ($data['ResultData'] as $k => $val) {
                        if ($val['Status'] == 'N') {
                            $new = $val;
                        }
                        if ($form_params['Customersid'] == $val['CustomerSID']) {
                            $original = $val;
                        }
                    }
                    if (!empty($new)) {
                        return $new;
                    }
                    if (!empty($original)) {
                        return $original;
                    }
                    return $data['ResultData'][0];
                }

            }
            loger(['request' => $form_params, 'response' => $response], 'getSisleyCustomerFail');
            return false;
        } catch (Exception $e) {
            loger(['request' => $form_params, 'response' => $e], 'getSisleyCustomerFail');
            return false;
        }
    }


    /**
     * 更新用户积分
     * @param $params
     * @return bool
     */
    public function changePoins($params)
    {

        //pc, mobile,miniapp
        $url = $this->domain . '/Sisley_ChangePoints';
        $form_params = [
            'CustomerSID' => $params['pos_id'],
            'ChangePoints' => $params['point'],
            'source' => $params['source'],
            'remarks' => $params['remarks'],
        ];

        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();

        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            $data = $this->xmlToArray($contents);
            if (isset($data['Code']) && $data['Code'] == 0) {
                $date = date('Y-m-d H:i:s');
                $params['created_at'] = $date;
                $params['updated_at'] = $date;
                DB::table('tb_points_log')->insert($params);
                return true;
            }

        }

        Log::error('pos_crm_changePoins_return_http_code:' . $code . 'content:' . $contents . 'params:', $params);
        return false;
    }


    public function RedemptionInterface($params)
    {

        //pc, mobile,miniapp
        $url = $this->domain . '/RedemptionInterface';
        $form_params = [
            'ID' => 'dlc_pointmall_' . $params['ID'],
            'CustomerSID' => $params['CustomerSID'],
            'StoreNo' => '',
            'SKU' => $params['SKU'],
            'CreateTime' => date("Y-m-d H:i:s"),
            'Quantity' => 1,
            'OrderStatus' => 'Waiting',
            'CreateSource' => '网站',
            'TransactionCode' => '',
            'CustomerType' => '',
            'Remark' => '',
            'SendState' => 'B',
            'ExpressAddress' => '积分兑换，暂时无地址',
        ];

        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();

        $data = [];
        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            loger(['request' => $params, 'response' => $contents], 'RedemptionInterface');
            $xml = simplexml_load_string($contents);
            $jsonStr = json_encode($xml);
            $data = json_decode($jsonStr, true);
        }

        return $data;
    }

    /**
     * 查询用户信息
     * @param $params
     * @return bool
     */
    public function updateInfo($pos_id, $params)
    {

        if (count($params) == 1 && isset($params['password'])) {
            return true;
        }
        $url = $this->domain . '/Sisley_CustomerUpdate';
        if (isset($params['sex'])) {
            if ($params['sex'] == 1) {
                $params['sex'] = 'M';
            } else {
                $params['sex'] = 'F';
            }
        }
        $form_params = [
            'CustomerSid' => $pos_id,
            'Mobile' => isset($params['phone']) ? $params['phone'] : '',
            'Name' => isset($params['name']) ? $params['name'] : '',
            'Birthday' => isset($params['birth']) ? $params['birth'] : '',
            'Gender' => isset($params['sex']) ? $params['sex'] : '',
        ];

        $response = $this->client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();

        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            loger(['request' => $params, 'response' => $contents], 'crmUpdateSuccess');

            $data = $this->xmlToArray($contents);

            if (isset($data['Code']) && $data['Code'] == '0' && isset($data['Msg']) && $data['Msg'] == 'Success') {
                return true;
            }

        }
        loger(['request' => $params, 'code' => $code, 'response' => $contents], 'crmUpdateError');
        return false;
    }


}