<?php
namespace App\Lib;
use App\Http\Helpers\Api\OrderCurlLog;

class Http
{
    public function curl($apiName,$data=[]){

        $url = config('api.map')[$apiName] ?? $apiName;
        $data=json_encode($data);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'accept: application/json',
                'content-type: application/json',
                'content-length: ' . strlen($data))
                );
            $result = curl_exec($ch);
            $resultArray=\json_decode($result,true);
            loger([
                'time'=>date('Y-m-d H:i:s'),
                'url'=>$url,
                'params'=>json_encode($data),
                'response'=>$result
            ],strtr($url,['http://'=>'','//'=>'.','/'=>"."]));
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
