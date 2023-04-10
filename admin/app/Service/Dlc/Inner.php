<?php namespace App\Service\Dlc;

use Curl\Curl;
use Illuminate\Support\Facades\Log;

/**
 * 内部接口调用公共函数
 * 调用样例：
 * $api = app('ApiRequestInner');
 * $resp = $api->request('promotion/cart/applyNew','POST',$data);
 * --------------------------------------------------------------
 * config下的apimap_inner为接口地址的对应关系
 * Class Inner
 * @package App\Services\ApiRequest
 */
class Inner
{
    protected $curl;

    /**
     * Inner constructor.
     * @throws \Exception
     */
    public function __construct(){
        $this->curl = new Curl();
        $this->curl->setHeader('Content-Type','application/json');
        $this->curl->setConnectTimeout(10);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER,0);
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
        $url = array_get(config('api.map'),$interface);
        if(!$url){
            throw new \Exception('接口不存在');
        }
        call_user_func([$this->curl,$method],$url,$data);
        $resp = $this->curl->getRawResponse();
        $resp = json_decode($resp,true);
        Log::info($interface,[
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

    protected function get_millisecond(){
        $time = explode(" ", microtime());
        return intval(($time[1]+$time[0])*1000);
    }
}







