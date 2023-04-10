<?php namespace App\Services\UPay;

use Curl\Curl;

class UPay
{
    //接口域名
    private $api_domain;
    //开发者编号
    private $vendor_sn;
    //开发者密钥
    private $vendor_key;
    //应用编号
    private $app_id;
    //激活码
    private $code;
    //设备号
    private $device_id;
    //终端信息存放路径
    private $filepath;
    //终端号
    private $terminal_sn;
    //终端密钥
    private $terminal_key;
    //本次交易的概述
    private $subject;
    //发起本次交易的操作员
    private $operator;
    //验签公钥
    private $public_key = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5+MNqcjgw4bsSWhJfw2M+gQB7P+pEiYOfvRmA6kt7Wisp0J3JbOtsLXGnErn5ZY2D8KkSAHtMYbeddphFZQJzUbiaDi75GUAG9XS3MfoKAhvNkK15VcCd8hFgNYCZdwEjZrvx6Zu1B7c29S64LQPHceS0nyXF8DwMIVRcIWKy02cexgX0UmUPE0A2sJFoV19ogAHaBIhx5FkTy+eeBJEbU03Do97q5G9IN1O3TssvbYBAzugz+yUPww2LadaKexhJGg+5+ufoDd0+V3oFL0/ebkJvD0uiBzdE3/ci/tANpInHAUDIHoWZCKxhn60f3/3KiR8xuj2vASgEqphxT5OfwIDAQAB';

    public function __construct(){
        $this->api_domain = env('UPAY_API_DOMAIN');
        $this->vendor_sn = env('UPAY_VENDOR_SN');
        $this->vendor_key = env('UPAY_VENDOR_KEY');
        $this->app_id = env('UPAY_APP_ID');
        $this->code = env('UPAY_CODE');
        $this->device_id = env('UPAY_DEVICE_ID');
        $this->filepath = __DIR__.'/terminal';
        $terminal = $this->getTerminal();
        $this->terminal_sn = $terminal['terminal_sn'];
        $this->terminal_key = $terminal['terminal_key'];
        $this->subject = env('UPAY_SUBJECT');
        $this->operator = env('UPAY_OPERATOR');
    }

    /**
     * 登记终端，获得终端号和密钥
     * @return mixed
     * @throws \Exception
     */
    public function activate(){
        $url = $this->api_domain .'/terminal/activate';

        $params['app_id'] = $this->app_id;
        $params['code'] = $this->code;
        $params['device_id'] = $this->device_id;
        $result = $this->pre_do_execute($params, $url, $this->vendor_sn, $this->vendor_key);
        $result = json_decode($result,true);
        if(array_get($result,'result_code')==200){
            $this->terminal_sn = array_get($result,'biz_response.terminal_sn');
            $this->terminal_key = array_get($result,'biz_response.terminal_key');
            //将终端号记录到文件中
            if($this->setTerminal($this->terminal_sn,$this->terminal_key)){
                return [
                    'terminal_sn'=>$this->terminal_sn,
                    'terminal_key'=>$this->terminal_key
                ];
            }
        }return false;
    }

    /**
     * 更换终端密钥
     * @return mixed
     * @throws \Exception
     */
    public function checkin(){
        $url = $this->api_domain .'/terminal/checkin';

        $params['terminal_sn'] = $this->terminal_sn;
        $params['device_id'] = $this->device_id;
        $result = $this->pre_do_execute($params, $url, $this->terminal_sn, $this->terminal_key);
        $result = json_decode($result,true);
        if(array_get($result,'result_code')==200){
            $this->terminal_sn = array_get($result,'biz_response.terminal_sn');
            $this->terminal_key = array_get($result,'biz_response.terminal_key');
            //将终端号记录到文件中
            if($this->setTerminal($this->terminal_sn,$this->terminal_key)){
                return [
                    'terminal_sn'=>$this->terminal_sn,
                    'terminal_key'=>$this->terminal_key
                ];
            }
        }return false;
    }

    /**
     * 预下单接口
     * @param array $data
     * @return bool|string
     * @throws \Exception
     */
    public function precreate($data){
        $url = $this->api_domain .'/upay/v2/precreate';

        $params['terminal_sn'] = $this->terminal_sn;      //收钱吧终端ID
        //$params['sn']='';         //收钱吧系统内部唯一订单号
        $params['client_sn'] = array_get($data,'client_sn');  //商户系统订单号,必须在商户系统内唯一；且长度不超过64字节
        $params['total_amount'] = array_get($data,'total_amount');             //交易总金额
        $params['payway'] = array_get($data,'payway');                   //内容为数字的字符串 支付方式
        $params['sub_payway'] = array_get($data,'sub_payway');
        $params['subject'] = $this->subject;              //本次交易的概述
        $params['operator'] = $this->operator;             //发起本次交易的操作员
//        $params['payer_uid'] = '';
        //$params['sub_payway']='';           //内容为数字的字符串，如果要使用WAP支付，则必须传 "3", 使用小程序支付请传"4"
        //$params['description']='';           //对商品或本次交易的描述
        //$params['longitude']='';             //经纬度必须同时出现
        //$params['latitude']='';              //经纬度必须同时出现
        //$params['goods_details']='';         //商品详情
        //$params['reflect']='';               //任何调用者希望原样返回的信息
        $params['notify_url'] = array_get($data,'notify_url');         //支付回调的地址
        return $this->pre_do_execute($params, $url, $this->terminal_sn, $this->terminal_key);
    }

    /**
     * 支付接口
     * @return mixed
     * @throws \Exception
     */
    public function  pay(){
        $url = $this->api_domain . "/upay/v2/pay";

        $params['terminal_sn'] = $this->terminal_sn;              //终端号
        $params['client_sn'] = ''; //商户系统订单号,必须在商户系统内唯一；且长度不超过64字节
        $params['total_amount'] = '';                      //交易总金额,以分为单位
        //$params['payway'] = '';                          //支付方式,1:支付宝 3:微信 4:百付宝 5:京东钱包
        $params['dynamic_id'] = '';       //条码内容（支付包或微信条码号）
        $params['subject'] = '';                        //交易简介
        $params['operator'] = '';                        //门店操作员
        //$params['description']='';                        //对商品或本次交易的描述
        //$params['longitude']='';                          //经度(经纬度必须同时出现)
        //$params['latitude']='';                           //纬度(经纬度必须同时出现)
        //$params['device_id']='';                          //设备指纹
        //$params['extended']='';                           //扩展参数集合  { "goods_tag": "beijing"，"goods_id":"1"}
        //$params['goods_details']='';                      //商品详情 goods_details": [{"goods_id": "wx001","goods_name": "苹果笔记本电脑","quantity": 1,"price": 2,"promotion_type": 0}]
        //$params['reflect']='';                            //反射参数
        //$params['notify_url']='';                         //支付回调地址(www.baidu.com 如果支付成功通知时间间隔为1s,5s,30s,600s)

        return $this->pre_do_execute($params, $url, $this->terminal_sn, $this->terminal_key);
    }

    /**
     * 退款接口
     * @param $creditMemo
     * @return mixed
     * @throws \Exception
     */
    public function refund($creditMemo){
        $url = $this->api_domain . '/upay/v2/refund';

        $params['terminal_sn'] = $this->terminal_sn;       //收钱吧终端ID
        $params['sn'] = "{$creditMemo['sn']}";        //收钱吧系统内部唯一订单号（N）
        //$params['client_sn']='';   //商户系统订单号,必须在商户系统内唯一；且长度不超过64字节
        //$params['client_tsn'] = '';  //商户退款流水号一笔订单多次退款，需要传入不同的退款流水号来区分退款，如果退款请求超时，需要发起查询，并根据查询结果的client_tsn判断本次退款请求是否成功
        $params['refund_amount'] = "{$creditMemo['refund_amount']}";              //退款金额
        $params['refund_request_no'] = "{$creditMemo['refund_request_no']}";        //商户退款所需序列号,表明是第几次退款(正常情况不可重复，意外状况爆出不变)
        $params['operator'] = $this->operator;;                 //门店操作员
        //$params['extended'] = '';                    //扩展参数集合
        //$params['goods_details'] = '';               //商品详情

        return $this->pre_do_execute($params, $url, $this->terminal_sn, $this->terminal_key);
    }

    /**
     * 查找接口
     * @param $client_sn
     * @return bool|string
     * @throws \Exception
     */
    public function query($client_sn){
        $url = $this->api_domain . '/upay/v2/query';
        $params['terminal_sn'] = $this->terminal_sn;      //收钱吧终端ID
//        $params['sn']='';         //收钱吧系统内部唯一订单号
        $params['client_sn'] = "$client_sn";    //商户系统订单号,必须在商户系统内唯一；且长度不超过64字节

        return $this->pre_do_execute($params, $url, $this->terminal_sn, $this->terminal_key);
    }

    /**
     * wap api pro 接口
     * @param $data
     * @return string
     */
    public function wap_api_pro($data){
        $params['terminal_sn'] = $this->terminal_sn;//收钱吧终端ID
        $params['client_sn'] = array_get($data,'client_sn');//商户系统订单号,必须在商户系统内唯一；且长度不超过64字节
        $params['total_amount'] = array_get($data,'total_amount');//以分为单位,不超过10位纯数字字符串,超过1亿元的收款请使用银行转账
        $params['subject'] = $this->subject;//本次交易的概述
        $params['operator'] = $this->operator;//发起本次交易的操作员
        $params['notify_url'] = array_get($data,'notify_url');
        $params['return_url'] = array_get($data,'return_url');  //处理完请求后，当前页面自动跳转到商户网站里指定页面的http路径
        ksort($params);  //进行升序排序
        $param_str = $param_str_encode = "";
        foreach ($params as $k => $v) {
            $param_str .= $k .'='.$v.'&';
            $param_str_encode .= $k .'='.urlencode($v).'&';
        }
        $sign = strtoupper(md5($param_str . 'key=' . $this->terminal_key));
        $paramsStr = $param_str_encode . "sign=" . $sign;
        return "https://qr.shouqianba.com/gateway?" . $paramsStr;
        //include './phpqrcode/phpqrcode.php';
        //QRcode::png($res);  //生成二维码
        //将这个url生成二维码扫码或在微信链接中打开可以完成测试
//        file_put_contents('logs/wap_api_pro_' . date('Y-m-d') . '.txt', $res, FILE_APPEND);
    }

    /**
     * 执行
     * @param $params
     * @param $url
     * @param $sn
     * @param $key
     * @param $method
     * @return bool|string
     * @throws \Exception
     */
    private function pre_do_execute($params, $url, $sn, $key, $method='POST'){
        $sign = $this->getSign(json_encode($params) . $key);
        $headers = [
            'Format'=>'json',
            'Content-Type'=>'application/json',
            'Authorization'=>$sn.' '.$sign,
        ];
        return $this->do_execute($url, $headers, $method, $params);
    }

    private function getSign($signStr){
        return Md5($signStr);
    }

    private function do_execute($url, $headers, $method, $params=[]){
        $curl = new Curl();
        $curl->setHeaders($headers);
        call_user_func([$curl,$method],$url,$params);
        $response = $curl->response;
        $this->log([
            'RequestData'=>$params,
            'RespondData'=>$response,
            'Url'=>$url,
            'Error'=>$curl->error,
            'ErrorMessage'=>$curl->errorMessage,
        ]);
        return $response;
    }

    public function log($data){
        log_array('upay',$data);
    }

    private function getTerminal():array{
        $data = [];
        $json = is_file($this->filepath)?file_get_contents($this->filepath):null;
        if($json){
            $data = json_decode($json,true);
        }
        return [
            'terminal_sn'=>array_get($data,'terminal_sn'),
            'terminal_key'=>array_get($data,'terminal_key'),
            'time'=>array_get($data,'time'),
        ];
    }

    private function setTerminal($terminal_sn,$terminal_key):bool{
        $time = time();
        return file_put_contents($this->filepath,json_encode(compact('terminal_sn','terminal_key','time')));
    }

    /**
     * 检查是否激活终端
     * @throws \Exception
     */
    private function checkTerminal():void{
        if(empty($this->terminal_sn) || empty($this->terminal_key)){
            throw new \Exception('未激活终端');
        }
    }

    /**
     * 回调验签
     * @return bool
     */
    public function validateSign(){
        /** @var \Laravel\Lumen\Http\Request $request */
        $request = app('request');
        $data = $request->getContent();
        $sign = getallheaders();
        $PUBLIC_KEY = $this->formatPubKey($this->public_key);
        $result = (openssl_verify($data,base64_decode($sign['Authorization']), $PUBLIC_KEY,OPENSSL_ALGO_SHA256)===1);
        if($result){
            return true;
        }return false;
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

}
