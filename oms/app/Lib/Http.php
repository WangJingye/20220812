<?php
namespace App\Lib;
use App\Http\Helpers\Api\PromotionCurlLog;


class Http
{
    public function curl($apiName,$data=[]){
        $url = config('api.map')[$apiName];
        
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
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            /*$logger = new PromotionCurlLog();
            $logger->curlLog(json_decode($data),"Get" , $url,
                "", $return_code, $result );*/
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function post($url,$data=[]){

        $data=json_encode($data);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            /*
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'accept: application/json',
                    'content-type: application/json',
                    'content-length: ' . strlen($data))
            );
            */
            $result = curl_exec($ch);
            $resultArray=\json_decode($result,true);
           /* error_log(print_r([
                'time'=>date('Y-m-d H:i:s'),
                'url'=>$url,
                'params'=>$data,
                'response'=>$result
            ],true),3,'../storage/logs/subscribe.log');*/
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
