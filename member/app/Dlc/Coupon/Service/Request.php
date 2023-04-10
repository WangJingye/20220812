<?php

namespace App\Dlc\Coupon\Service;

class Request extends BaseService
{
    private $client;
    private $timeout = 5;
    private $header = [
        'content-type'=>'application/json; charset=utf8'
    ];
    public function init(){
        $this->client = new \GuzzleHttp\Client([
            'timeout'=>$this->timeout,
            'header'=>$this->header,
        ]);
    }

    public function request($uri,$method='GET',$params=[]){
        $response = $this->client->request($method,$uri,['query'=>$params]);
        $code = $response->getStatusCode();
        $result = json_decode( $response->getbody()->getContents(), true);
        $this->log([
            'requestUri'=>$uri,
            'requestMethod'=>$method,
            'requestData'=>$params,
            'responseCode'=>$code,
            'responseData'=>$result,
        ]);
        return $result;
    }

    protected function log($data){
        $helper = HelperService::getInstance();
        $helper->log('coupon-request',$data);
    }





}
