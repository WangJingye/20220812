<?php namespace App\Services\ApiRequest;

use Curl\Curl;

/**
 * 内部接口调用公共函数
 * 调用样例：
 * $api = app('ApiRequestInner',['module'=>'promotion']);
 * $resp = $api->request('promotion/cart/applyNew','POST',$data);
 * --------------------------------------------------------------
 * config下的apimap_inner为接口地址的对应关系
 * Class Inner
 * @package App\Services\ApiRequest
 */
class Inner
{
    protected $domain;
    //使用的模块
    protected $module;
    protected $curl;

    /**
     * Inner constructor.
     * @param $module
     * @throws \Exception
     */
    public function __construct($module){
        $this->module = strtolower($module);
        $this->domain = $this->mapping();
        $this->curl = new Curl();
        $this->curl->setHeader('Content-Type','application/json');
        $this->curl->setConnectTimeout(10);
    }

    /**
     * 模块与域名的映射关系
     * @return mixed
     * @throws \Exception
     */
    protected function mapping(){
        $map = [
            'admin'=>'API_INNER_DOMAIN_ADMIN',
            'goods'=>'API_INNER_DOMAIN_GOODS',
            'member'=>'API_INNER_DOMAIN_MEMBER',
            'oms'=>'API_INNER_DOMAIN_OMS',
            'order'=>'API_INNER_DOMAIN_ORDER',
            'promotion'=>'API_INNER_DOMAIN_PROMOTION',
            'store'=>'API_INNER_DOMAIN_STORE',
            'db'=>'API_INNER_DOMAIN_DB',
        ];
        $domain = array_get($map,$this->module);
        if(empty($domain)){
            throw new \Exception('Module not exists!');
        }
        return env($domain);
    }

    /**
     * @param string $interface(接口地址)
     * @param string $method(方法)
     * @param array $data(参数)
     * @return null
     * @throws \Exception
     */
    public function request($interface, $method, array $data = []){
        $start_time = $this->get_millisecond();
        $interface = $this->apiMapping($interface);
        $url = "{$this->domain}/{$interface}";
//        $this->curl->setHeader('authorization','Bearer '.$token);
        if(request('from')){
            $this->curl->setHeader('from',1);
            $this->curl->setHeader('dlc-inner-invoke-from','order');
        }
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
        call_user_func([$this->curl,$method],$url,$data);
        $resp = $this->curl->getRawResponse();
        $resp = json_decode($resp,true);
        $this->log([
            'RequestInterface'=>$interface,
            'RequestMethod'=>$method,
            'RequestData'=>$data,
            'RespondData'=>$resp,
            'Url'=>$url,
            'Error'=>$this->curl->error,
            'ErrorMessage'=>$this->curl->errorMessage,
            'Time'=>($this->get_millisecond()-$start_time).'ms',
        ]);
        if(empty($resp)){
            throw new \Exception($this->curl->errorMessage);
        }
        return $resp;
    }

    /**
     * Inner Api中接口和请求的外部接口的对应关系 没找到对应关系就用请求的api
     * @param $api
     * @return mixed
     */
    protected function apiMapping($api){
        return config("apimap_inner.{$api}")?:$api;
    }

    private function log($data){
        log_json("inner-{$this->module}",$data);
    }

    protected function get_millisecond(){
        $time = explode(" ", microtime());
        return intval(($time[1]+$time[0])*1000);
    }
}







