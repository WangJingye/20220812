<?php namespace App\Service\DLCCrm;

class Request
{
    private $instance;
    public function __construct()
    {
        $wsdl = config('dlc.dlc_crm_url');
        $username = config('dlc.dlc_crm_username');
        $password = config('dlc.dlc_crm_password');
        $this->connect($wsdl,$username,$password);
    }

    private function connect(...$args){
        list($wsdl,$username,$password) = $args;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);
        $options = ['cache_wsdl'=>WSDL_CACHE_NONE,'stream_context' => $context];
        $this->instance = new \SoapClient($wsdl,$options);
    }

    public function exec($api,$params)
    {
        $params['channel'] = config('dlc.dlc_crm_channel');
        $resp = $this->instance->__call($api,[$params]);
        $this->log([
            'Api'=>$api,
            'RequestData'=>$params,
            'RespondData'=>$resp??[],
        ]);
        $result = $this->out($api,$resp);
        //输出自定义
        if(class_exists(Response::class)){
            $response = new Response;
            $name = array_search($api,Config::API_MAP);
            if(method_exists($response,$name)){
                return call_user_func([$response,$name],$result);
            }
        }return $result;
    }

    /**
     * 输出格式化
     * @param $api
     * @param $resp
     * @return \SimpleXMLElement
     */
    protected function out($api,$resp){
        $result = $resp->{$api.'Result'};
        if(isset($result->any)){
            $resp = simplexml_load_string($result->any);
        }else{
            $resp = $result;
        }
        return $resp;
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

    private function log($data){
        log_array('dlc_crm',$data);
    }




}







