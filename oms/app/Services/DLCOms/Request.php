<?php namespace App\Services\DLCOms;

use Curl\Curl;

class Request
{
    protected $curl;
    protected $app_key;
    protected $app_secret;
    protected $url;

    public function __construct(){
        $this->curl = new Curl();
        $headers = [
            'Content-type'=>'application/x-www-form-urlencoded',
            'charset'=>'utf-8',
        ];
        $this->curl->setHeaders($headers);
        $this->curl->setOpts([
            CURLOPT_SSL_VERIFYPEER=>0,
            CURLOPT_SSL_VERIFYHOST=>0,
        ]);
        $this->curl->setConnectTimeout(10);

        $this->app_key = config('dlc.dlc_oms_app_key');
        $this->app_secret = config('dlc.dlc_oms_app_secret');
        $this->url = config('dlc.dlc_oms_url');
    }

    /**
     * @param $api
     * @param array $params
     * @return null
     * @throws \Exception
     */
    protected function exec($api,array $params){
        $datas = [
            'app_key'=>$this->app_key,
            'timestamp'=>time(),
            'version'=>'1.0',
            'random'=>$this->getRandom(32),
            'format'=>'json',
            'method'=>$api,
            'params'=>json_encode($params),
        ];
        $datas['sign'] = $this->getSignature($datas);
        call_user_func([$this->curl,'POST'],$this->url,$datas);
        $resp = $this->curl->response;
        $this->log([
            'RequestData'=>$params,
            'SysRequestData'=>$systemParams??[],
            'RespondData'=>$resp??[],
            'Api'=>$api,
            'ErrorMessage'=>$this->curl->errorMessage,
        ]);
        if(empty($resp)){
            throw new \Exception($this->curl->errorMessage);
        }
        $result = $this->out($resp);
        //输出自定义
        if(class_exists(Response::class)){
            $response = new Response;
            $name = array_search($api,Config::API_MAP);
            if(method_exists($response,$name)){
                return call_user_func([$response,$name],$result);
            }
        }return $result;
    }

    public function __call($name,$arguments){
        $api = array_get(Config::API_MAP,$name);
        if($api){
            list($params) = $arguments;
            return call_user_func([$this,'exec'],$api,$params);
        }else{
            throw new \Exception('方法不存在');
        }
    }

    protected function out($resp){
        return json_decode($resp);
    }

    private function getSignature($params){
        ksort($params);
        $sign='';
        foreach($params as $k=>$v){
            $sign.=$k.'='.$v.'&';
        }
        return strtoupper(md5($sign.$this->app_secret));
    }

    private function getRandom($len = 16){
        $returnStr='';
        $pattern = '1234567890ABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i = 0; $i < $len; $i ++) {
            $returnStr .= $pattern {mt_rand ( 0, 35 )};
        }
        return $returnStr;
    }

    private function log($data){
        log_array('dlc_oms',$data);
    }




}







