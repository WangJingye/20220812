<?php namespace App\Service\YKSms;

use Curl\Curl;

class Request
{
    protected $curl;
    protected $username;
    protected $password;
    protected $url;

    public function __construct(){
        $this->curl = new Curl();
        $headers = [
            'Content-type'=>'application/json;charset=utf-8',
        ];
        $this->curl->setHeaders($headers);
        $this->curl->setOpts([
            CURLOPT_SSL_VERIFYPEER=>0,
            CURLOPT_SSL_VERIFYHOST=>0,
        ]);
        $this->curl->setConnectTimeout(10);

        $this->username = config('dlc.dlc_yk_sms_username');
        $this->password = config('dlc.dlc_yk_sms_password');
        $this->url = config('dlc.dlc_yk_sms_url');
    }

    /**
     * @param $api
     * @param array $params
     * @return null
     * @throws \Exception
     */
    protected function exec($api,array $params){
        $datas = [
            'name'=>$this->username,
            'password'=>strtolower(md5($this->password)),
        ];
        $url = "{$this->url}/{$api}";
        $datas = array_merge($datas,$params);
        call_user_func([$this->curl,'POST'],$url,json_encode($datas));
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
        return $resp;
    }

    public function __call($name,$arguments=[]){
        $api = array_get(Config::API_MAP,$name);
        if($api){
            if($arguments){
                list($params) = $arguments;
            }
            return call_user_func([$this,'exec'],$api,$params??[]);
        }else{
            throw new \Exception('方法不存在');
        }
    }

    private function log($data){
        log_array('dlc_sms',$data);
    }




}







