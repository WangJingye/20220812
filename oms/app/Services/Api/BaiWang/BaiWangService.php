<?php
/**
 *  ===========================================
 *  File Name   AuthApiBase.php
 *  Class Name  AuthApiBase
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Services\Api\BaiWang;

use App\Tools\Http;
use Illuminate\Support\Facades\Redis;

class BaiWangService
{
    /**
     * @var array 百旺请求地址
     */
    private static $apiUri = 'router/rest';
    /**
     * @var array token库
     */
    private static $redisTokenKey = 'HASH:SISLEY:ACCESS_TOKEN';
    /**
     * @var array 税率
     */
    private static $taxRate = '0.13';
    /**
     * @var array 商品分类编码
     */
    private static $revenueTypeMapping = [
        '1' => '107022302',
        '2' => '107022303',
        '3' => '106051202',
        '4' => '107022304',
    ];
    private static $instance;

    private static $appConfig = [
        'appKey' => '10001455',
        'appSecret' => '59882ace-3074-40ba-9371-aead1eb7ed40',
        'username' => 'admin_3000000404271',
        'password' => 'dlcdp200821!',
        'userSalt' => '56628960f3a449748f1a8579015c44f2',
    ];

    private function __construct()
    {
        self::$apiUri = env('BAIWANG_API_HOST') . self::$apiUri;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance) || !(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 发票开具
     * @param array $orderInfo
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoiceOpen(array $orderInfo)
    {
        //公共参数
        $common = self::getCommonArgs('baiwang.invoice.open');
        //业务参数
        $business = [];
        $business['serialNo'] = $orderInfo['order_sn'];
        $business['taxationMode'] = '0';
        $business['invoiceType'] = '0';
        $business['invoiceTypeCode'] = '026';
        $business['invoiceSpecialMark'] = '00';
        $business['sellerTaxNo'] = '91310000717862732B';
        $business['invoiceTerminalCode'] = 'Sisley2020';
        if ($orderInfo['type'] === 'company') {
            $business['buyerTaxNo'] = $orderInfo['number'];//购方单位税号
            $business['buyerName'] = $orderInfo['title'];//购方名称
        } else {
            $business['buyerName'] = $orderInfo['title'];//购方名称
        }
        $business['drawer'] = '徐小萌';
        $business['checker'] = '马倩';
//        $business['payee'] = 'dzzd';
//        $business['invoiceTotalPrice'] = (double)round($orderInfo['total_amount'] / (1 + self::$taxRate), 2);//合计金额，保留两位小数
//        $business['invoiceTotalTax'] = (double)($orderInfo['total_amount'] - $business['invoiceTotalPrice']);//合计税额，保留两位小数
//        $business['invoiceTotalPriceTax'] = (double)$orderInfo['total_amount'];//价税合计，保留两位小数
        $business['consolidatedTaxRate'] = self::$taxRate;
        $business['sellerName'] = '希思黎（上海）化妆品商贸有限公司';
        $business['sellerBankAccount'] = '中国银行上海市静安支行440359235500';
        $business['sellerAddressPhone'] = '上海市静安区南京西路1539号静安嘉里中心办公楼二座38、39层3801-3805、3903-3904室021-52031111';
        $business['invoiceDetailsList'] = self::setInvoiceDetailsList($orderInfo['goodsList']);
        $business['invoiceListMark'] = count($business['invoiceDetailsList']) >= 8 ? '1' : '0';
        //签名
        $common['sign'] = self::signature($common, $business);
        //合并请求参数
        $uri = http_build_query($common);
        $invoiceOpenResult = Http::httpRequest($business, self::$apiUri . "?{$uri}", 'POST', 'JSON');
        if (empty($invoiceOpenResult) || !empty($invoiceOpenResult['errorResponse'])) {
            throw new \Exception("Invoice Open fail.");
        }
    }

    /**
     * 发票上传（小程序开票）,成功返回开票url
     * @param array $orderInfo
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoiceUpload(array $orderInfo)
    {
        //公共参数
        $common = self::getCommonArgs('baiwang.invoice.upload');
        //业务参数
        $business = [];
        $business['invoiceUploadType'] = '1';
        $business['serialNo'] = $orderInfo['order_sn'];
        $business['taxationMode'] = '0';
        $business['invoiceType'] = '0';
        $business['invoiceTypeCode'] = '026';
        $business['invoiceSpecialMark'] = '00';
        $business['sellerTaxNo'] = '91310000717862732B';
        $business['invoiceTerminalCode'] = 'Sisley2020';
        $business['drawer'] = '徐小萌';
        $business['checker'] = '马倩';
//        $business['payee'] = 'dzzd';
//        $business['invoiceTotalPrice'] = (double)round($orderInfo['total_amount'] / (1 + self::$taxRate), 2);//合计金额，保留两位小数
//        $business['invoiceTotalTax'] = (double)($orderInfo['total_amount'] - $business['invoiceTotalPrice']);//合计税额，保留两位小数
//        $business['invoiceTotalPriceTax'] = (double)$orderInfo['total_amount'];//价税合计，保留两位小数
        $business['consolidatedTaxRate'] = self::$taxRate;
        $business['sellerName'] = '希思黎（上海）化妆品商贸有限公司';
        $business['sellerBankAccount'] = '中国银行上海市静安支行440359235500';
        $business['sellerAddressPhone'] = '上海市静安区南京西路1539号静安嘉里中心办公楼二座38、39层3801-3805、3903-3904室021-52031111';
        $business['invoiceDetailsList'] = self::setInvoiceDetailsList($orderInfo['goodsList']);
        $business['invoiceListMark'] = count($business['invoiceDetailsList']) >= 8 ? '1' : '0';
        //签名
        $common['sign'] = self::signature($common, $business);
        //合并请求参数
        $uri = http_build_query($common);
        $invoiceUploadResult = Http::httpRequest($business, self::$apiUri . "?{$uri}", 'POST', 'JSON');
        if (empty($invoiceUploadResult) || !empty($invoiceUploadResult['errorResponse'])) {
            throw new \Exception("Invoice Upload fail.");
        }

        $response = $invoiceUploadResult['response'];

        return ['url' => $response['url']];
    }

    /**
     * 发票查询
     * @param string $serialNo
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function invoiceQuery(string $serialNo)
    {
        //公共参数
        $common = self::getCommonArgs('baiwang.invoice.query');
        //业务参数
        $business = [];
        $business['sellerTaxNo'] = '91310000717862732B';
        $business['invoiceQueryType'] = '1';
        $business['serialNo'] = $serialNo;

        //签名
        $common['sign'] = self::signature($common, $business);
        //合并请求参数
        $uri = http_build_query($common);
        $invoiceQueryResult = Http::httpRequest($business, self::$apiUri . "?{$uri}", 'POST', 'JSON');
        if (empty($invoiceQueryResult) || !empty($invoiceQueryResult['errorResponse'])) {
            throw new \Exception("Invoice Query fail.");
        }

        $response = $invoiceQueryResult['response'];

        $invoiceDetail = $response['invoiceList'][0];

        return ['invoicePath' => $invoiceDetail['h5Url'], 'invoiceDownloadUrl' => $invoiceDetail['formatFileUrl'], 'title' => $invoiceDetail['buyerName'], 'phone' => $invoiceDetail['buyerPhone'], 'email' => $invoiceDetail['buyerEmail'], 'number' => $invoiceDetail['buyerTaxNo']];
    }

    /**
     * 版式文件生成
     * @param string $serialNo
     * @param string $email
     * @param string $phone
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function formatFileBuild(string $serialNo, string $email = '', string $phone = '')
    {
        //公共参数
        $common = self::getCommonArgs('baiwang.formatfile.build');
        //业务参数
        $business = [];
        $business['sellerTaxNo'] = '91310000717862732B';
        $business['serialNo'] = $serialNo;
        if ($email === '' && $phone === '') {
            $business['pushType'] = '0';
        } else {
            $business['pushType'] = '1';
            if (!empty($email)) {
                $business['buyerEmail'] = $email;
            }
            if (!empty($phone)) {
                $business['buyerPhone'] = $phone;
            }
        }

        //签名
        $common['sign'] = self::signature($common, $business);
        //合并请求参数
        $uri = http_build_query($common);
        $invoiceQueryResult = Http::httpRequest($business, self::$apiUri . "?{$uri}", 'POST', 'JSON');
        if (empty($invoiceQueryResult) || !empty($invoiceQueryResult['errorResponse'])) {
            throw new \Exception("Formatfile build fail.");
        }

        $response = $invoiceQueryResult['response'];
        $invoiceDownloadUrl = $response['data'];

        return ['invoiceDownloadUrl' => $invoiceDownloadUrl];
    }

    /**
     * 组装发票商品明细
     * @param array $goodsList
     * @return array
     */
    private static function setInvoiceDetailsList(array $goodsList)
    {
        $invoiceDetailsList = [];
        foreach ($goodsList as $line => $goodInfo) {
            $invoiceDetails = [];
            $invoiceDetails['goodsLineNo'] = $line + 1;
            $invoiceDetails['goodsLineNature'] = '0';
            $invoiceDetails['goodsCode'] = '1070223020000000000';//商品编码
            $invoiceDetails['goodsName'] = $goodInfo['name'];
//            $invoiceDetails['goodsTaxItem'] = self::$revenueTypeMapping[$goodInfo['revenue_type']];
            $invoiceDetails['goodsQuantity'] = $goodInfo['qty'];
            $invoiceDetails['goodsTotalPrice'] = round($goodInfo['order_amount_total'] * $goodInfo['qty'], 2);//合计税额，保留两位小数
            $invoiceDetails['goodsTaxRate'] = self::$taxRate;
            $invoiceDetails['priceTaxMark'] = '1';
            $invoiceDetails['freeTaxMark'] = '';
            $invoiceDetails['preferentialMark'] = '0';
            $invoiceDetailsList[] = $invoiceDetails;
        }
        return $invoiceDetailsList;
    }

    /**
     * 请求签名
     * @param $method
     * @return array
     */
    private function getCommonArgs($method)
    {
        $common = [];
        $common['method'] = $method;
        $common['appKey'] = self::$appConfig['appKey'];
        $common['token'] = self::getAuthToken();
        $common['timestamp'] = (string)time();
        $common['version'] = '3.0';

        return $common;
    }

    /**
     * 请求签名
     * @return string
     */
    private static function getAuthToken()
    {
        $token = Redis::hget(self::$redisTokenKey, self::$appConfig['appKey']);
        if ($token !== false) {
            return $token;
        } else {
            return '';
        }
    }

    /**
     * 请求签名
     * @param array $common
     * @param array $body
     * @return string
     */
    private static function signature(array $common, array $body)
    {
        ksort($common);
        $rawStr = '';
        foreach ($common as $pk => $pv) {
            $rawStr .= $pk . $pv;
        }
        $rawStr .= json_encode($body);

        $rawStr = self::$appConfig['appSecret'] . $rawStr . self::$appConfig['appSecret'];

        return strtoupper(md5($rawStr));
    }
}
