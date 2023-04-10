<?php namespace App\Services\UPay;

use Curl\Curl;

class Invoice
{
    private $curl;
    private $version = '1.0.0';
    private $appId = '28dp02175996';
    private $gateway = 'https://vip-apigateway.iwosai.com';
    private $private_key = 'MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCEkKPJG30CizqX/89exejlUs60gA6Cq8pjSdKANwNFn0IhVL2+jNeB5cT+1OlXzhcnvWyFB4pBLMO/7I8Dy7DUN9qSTDmnupruyzUd7fxr5nBgd3Q23cLDxDmuAYsE8m2pRyCloDGy8zp51n3gEPRd8TJPvRTv4uSeVsF6eoGmiRFp/E6jxwSaVs/zdfM199Z9EbVcDW3COxicIS76i8cke78ascPnuhr1O9hgAzQ4I99/NUnT8s+NjMUIvv1LM/Dfay9KYY2tDi7NIrfN/UJbd8BiNpYIAvSlQyjcv8zqnttjm2OwAynKtg9PtY9duvr4/CoxW5COxUECShh6VOFfAgMBAAECggEAZxboF9vFknXdghjQFd6IZ9XGo322Sw33XNEq3sRpSfo4fh0dVZLHgN/DG31NXRgKz7+yJZ2geWCrdZQr/4Kmp9IrqnuWloY6OBtU2kqZcvuIRqx+drBr5ruxM01F4/n3J54FmGeEXqphvh/8UYQ91NReEL51CrwCl9PVsD6Wln9/0jTkg1kuKESKWRexQ5JlisPAQxcOCvOxl5ThwbnepsZBZw2mfUBzv9vZ3S8aUi5OGobsOhfn4QX7qsPJphLtKkJJer8TcnRzuh5eJb7UfygBHlnEZeKfELwRgGgE8d9HNDyGq6Yiyenaz+d1X+GYDdm9pIRwvC3nxeBCn/TnEQKBgQDHwr7ZjCTnVlFeiSVARrRD2kpOGvgjNqMz09jT9oCRevbCqgE+k5/xkoHw82L45mdYwfh4iS4N/m47PfbT4AqmfvfLPy0eB5XrTxjbMFrhrpxU86vI6isLa+1MFMpW8GTkiCHe2KUzcU09kjOJ8lXEffwXkKykRIyNhHbY13YaOQKBgQCp4u4zNCVJ8tSCpreenCoLiPLq/VsQ1yQzL5MJX37SyP6ReQHoGjTt9NGYGFRD+Psd/9KzpBFaUMdigMeq3lJtm4eW/hEWgSstzhC7Yz8Upr8eTlhQ1oA4AkB89sQc0uPHQFGi5frmguWLCr79VzpbPztjNhQdv7PXcU685SG4VwKBgEuXhmEMh2qDX4dGnrIUD2Md18B2tC+fHWMfZ43OWhizT22ap53mf0ALEOD9ORa3GaScwknan3LsNQp2CFFlFqKqqVpgLdKPTEwfQmivg7SjPsVm8Dq3YlKQJNwFggwkLAnO+gI6OUmeNnx2NsqcyZfxlNPWC36d8hIbbl3gKvEBAoGBAJWje2jCn625JJJIQyiEKUyrvjxaGWKF/i7P0tXIta74t7JvQcyteL24jP1JQL/2iUptUaxF8br5uAX9pOHOnhBJlG7dLzQBZoUcIwTLcH7COUl6fLQHnDy5TxBDU46H+3ZAIyg3Jn7wATwtpPkFYjOJSMgklCd3+fLkrRQZR1BVAoGAFAHLclVhvE1ywwZxJJEAHIBONov4UYCqXD7t6YcdJnE5uOHFHCLSxxbV2jySPKGZ+lUTOVFNc6MxySfrdTqpQZo/6V3IOSsWEPfM67MnDe6xfMdKi8gv9nKSY4qluoJrPj21BeYbZkhje+cT5MibDf8vbUauwmPVirJM8H9WyCw=';
    private $public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnk9KFBWMN8jHPkovzVaPApo6cgziPDiX27ugbdgwH8n95oQEwGv67RFx3robKM+Nqy8qD54Xb6ZJyy+ELZRLAlbMiB9+LBIFT5eDsj8haI8UwzDRq1R87pPdC6YmffgqVBsEqYi3PeB7U955EyENvHxEe8K4DMj9ZZPq6H7p4/Nwav48NkUOeAOH/seo57XL85ETUUZyZ7WhmkzL99zSFA53E7eKt96HagSgcQUr822MSjHQzaKcml5z4gITls+/fBCm2KWgKFIyl4N4rxLpxpfZpXNikpmKVeRISO3mGSIZ7MaSX2VixE0NbVwGiNvSWomKjUDaEGCpJEmvmT5CnwIDAQAB';
    //销货方信息税号
//    private $payee_register_no = '91310000717863452K';
    private $payee_register_no = '91440300661005378A';
    //复核人
    private $checker = '谢亚佳';
    //收款人
    private $receiver = '万友';
    //开票人
    private $drawer = '崔俊';
    //税率
    private $tax_rate = 0.13;
    //税目编码
    private $tax_code = [
        'FW'=>'1040204990000000000',//运动鞋
        'AP'=>'1040201230000000000',//运动服
        'AC'=>'1040113050000000000',//运动配件
        'EQ'=>'1040113050000000000',//包
    ];

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setHeaders([
            'Format'=>'json',
            'Content-Type'=>'application/json',
        ]);
    }

    /**
     * 开蓝票
     * @param $invoice
     * @param $order
     * @return bool
     */
    public function makeInvoice($invoice,$order){
        $url = $this->gateway.'/invoice/blue';
        $params =[
            "request"=> [
                "head"=>[
                    "version"=> "{$this->version}",
                    "appid"=> "{$this->appId}",
                    "request_time"=> date('c',time()),
                    "reserve"=>"{}"
                ],
                "body"=>[
                    "request_id"=>"{$this->generateRequestId()}", //业务方每次请求网关是生成的唯一标识。sting(不超过32)，自定义
                    "order_id"=> "{$order->increment_id}",   //业务方订单号，自定义唯一。
                    "invoice_type_code"=>"3", //开票类型。1:增值税专用发票，2:增值税普通发票，3:电子发票
                    "payee_info"=>[
                        "payee_register_no"=> "{$this->payee_register_no}",//销货方信息税 string(20)
                        "checker"=> "{$this->checker}",   //复核人
                        "receiver"=> "{$this->receiver}",  //收款人
                        "drawer"=> "{$this->drawer}"     //开票人
                    ],
                    "payer_info"=>[
                        "payer_name"=> "{$invoice->name}",
                        "payer_phone"=> "",
                        "payer_bank_account"=> "",
                        "payer_bank_name"=>"",
                        "payer_address"=>"",
                        "payer_register_no"=> "{$invoice->code}"
                    ],
                    "sum_price_tax"=> sprintf("%.2f",$order->grand_total),
                    "sum_tax"=> sprintf("%.2f",$order->grand_total*$this->tax_rate),
                    "sum_price"=> sprintf("%.2f",$order->grand_total-$order->grand_total*$this->tax_rate),
                    "items"=>array_reduce($order->items,function($result,$item){
                        $row_total = $item->row_total;
                        $qty = intval($item->qty_ordered);
                        $item_name = cut_str($item->name,50);
                        $tax_code = array_get($this->tax_code,$item->product_type?:'',array_first($this->tax_code));
                        $result[] = [
                            "item_tax_code"=> "$tax_code",
                            "item_name"=> "{$item_name}",
                            "item_sum_price"=> sprintf("%.2f",$row_total-$row_total*$this->tax_rate),
                            "item_sum_tax"=> sprintf("%.2f",$row_total*$this->tax_rate),
                            "item_sum_price_tax"=> sprintf("%.2f",$row_total),
                            "item_tax_rate"=>"{$this->tax_rate}",
                            "item_quantity"=> "{$qty}",
                            "item_unit"=>"",
                            "item_unit_price"=> sprintf("%.2f",$item->price),
                            "item_specification_type"=> "",
                            "zero_rate_flag"=> ""
                        ];
                        return $result;
                    }),
                    "business_type"=> "{$invoice->title}",
                    "invoice_memo"=> "",
                    "request_time"=>date('c',time()),
                    "callback_url"=> env_url('invoice/callback'),
                ]
            ],
            "signature"=> ""
        ];
        $params['signature'] = $this->getSignature($params['request']);

        call_user_func([$this->curl,'POST'],$url,$params);
        $response = $this->curl->response;
        $this->log([
            'RequestData'=>$params,
            'RespondData'=>$response,
            'Url'=>$url,
            'Error'=>$this->curl->error,
            'ErrorMessage'=>$this->curl->errorMessage,
        ]);
        if($response){
            //验签
            if(!empty($response->response)
                && !empty($response->signature)
                && $this->verify($response->response,$response->signature)){
                if(object_get($response,'response.body.biz_response.result_code') == 'SUCCESS'){
                    return true;
                }
            }
        }return false;
    }

    /**
     * 查询发票
     * @param $task_sn
     * @return null
     */
    public function queryInvoice($task_sn){
        $url = $this->gateway.'/invoice/query';
        $params =[
            "request"=> [
                "head"=>[
                    "version"=> "{$this->version}",
                    "appid"=> "{$this->appId}",
                    "request_time"=> date('c',time()),
                    "reserve"=>"{}"
                ],
                "body"=>[
                    "task_sn"=>"{$task_sn}",
                ]
            ],
            "signature"=> ""
        ];
        $params['signature'] = $this->getSignature($params['request']);

        call_user_func([$this->curl,'POST'],$url,$params);
        $response = $this->curl->response;
        $this->log([
            'RequestData'=>$params,
            'RespondData'=>$response,
            'Url'=>$url,
            'Error'=>$this->curl->error,
            'ErrorMessage'=>$this->curl->errorMessage,
        ]);
        return $response;
    }

    /**
     * 红冲发票
     * @param $task_sn
     * @return null
     */
    public function redInvoice($task_sn){
        $url = $this->gateway.'/invoice/red';
        $params =[
            "request"=> [
                "head"=>[
                    "version"=> "{$this->version}",
                    "appid"=> "{$this->appId}",
                    "request_time"=> date('c',time()),
                    "reserve"=>"{}"
                ],
                "body"=>[
                    "request_id"=>"{$this->generateRequestId()}", //业务方每次请求网关是生成的唯一标识。sting(不超过32)，自定义
                    "blue_request_id" => "", //蓝票的request_id
                    "payee_register_no"=> "",
                    "blue_task_sn"=> "{$task_sn}",
                    "request_time"=>date('c',time()),
                    "callback_url"=> env_url('invoice-red/callback'),
                ]
            ],
            "signature"=> ""
        ];
        $params['signature'] = $this->getSignature($params['request']);

        call_user_func([$this->curl,'POST'],$url,$params);
        $response = $this->curl->response;
        $this->log([
            'RequestData'=>$params,
            'RespondData'=>$response,
            'Url'=>$url,
            'Error'=>$this->curl->error,
            'ErrorMessage'=>$this->curl->errorMessage,
        ]);
        if($response){
            //验签
            if(!empty($response->response)
                && !empty($response->signature)
                && $this->verify($response->response,$response->signature)){
                if(object_get($response,'response.body.biz_response.result_code') == 'SUCCESS'){
                    return true;
                }
            }
        }return false;
    }

    /**
     * 组装回调响应格式
     * @param $result_code
     * @return array
     */
    public function response($result_code){
        return ['result_code'=>"{$result_code}"];
    }

    /**
     * 获取签名
     * @param $request
     * @return string
     */
    private function getSignature($request){
        $sign_body=stripslashes(json_encode($request,JSON_UNESCAPED_UNICODE));
        //对请求体进行RSA加密  获取签名
        $privKeyId = openssl_pkey_get_private($this->formatPriKey($this->private_key));
        $signature='';
        openssl_sign($sign_body, $signature, $privKeyId);
        openssl_free_key($privKeyId);
        return base64_encode($signature);
    }

    /**
     * 验证签名
     * @param $response
     * @param $signature
     * @return bool
     */
    private function verify($response,$signature){
        $req_response = json_encode($response,JSON_UNESCAPED_UNICODE);
        $req_signature = $signature;
        $publickey = openssl_pkey_get_public($this->formatPubKey($this->public_key));
        $verify = openssl_verify($req_response, base64_decode($req_signature),$publickey,OPENSSL_ALGO_SHA1);
        openssl_free_key($publickey);
        return ($verify===1)?true:false;
    }

    private function formatPriKey($priKey) {
        $fKey = "-----BEGIN PRIVATE KEY-----\n";
        $len = strlen($priKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($priKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END PRIVATE KEY-----";
        return $fKey;
    }

    private function formatPubKey($pubKey) {
        $fKey = "-----BEGIN PUBLIC KEY-----\n";
        $len = strlen($pubKey);
        for($i = 0; $i < $len; ) {
            $fKey = $fKey . substr($pubKey, $i, 64) . "\n";
            $i += 64;
        }
        $fKey .= "-----END PUBLIC KEY-----";
        return $fKey;
    }

    /**
     * 生成请求的唯一标识
     * @param int $codeLenth
     * @return string
     */
    private function generateRequestId($codeLenth = 16){
        $str_sn = '';
        for ($i = 0; $i < $codeLenth; $i++)
        {
            if ($i == 0)
                $str_sn .= rand(1, 9);
            else
                $str_sn .= rand(0, 9);
        }
        return $str_sn;
    }

    public function log($data){
        log_array('invoice',$data);
    }
}
