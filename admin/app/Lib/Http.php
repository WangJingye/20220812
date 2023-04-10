<?php
namespace App\Lib;
use App\Http\Helpers\Api\AdminCurlLog;

class Http
{
    public function curl($apiName,$data=[]){
        $url = config('api.map')[$apiName];
        $data=json_encode($data);
        $header = array(
            'accept: application/json',
            'content-type: application/json',
            'content-length: ' . strlen($data));
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($ch);
            $resultArray=\json_decode($result,true);
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $logger = new AdminCurlLog();
            $logger->curlLog(json_decode($data),"GET" , $url,
                "", $return_code, $result );
            loger([
                'url'=>$url,
                'request'=>$data,
                'response'=>$resultArray
            ],'admin_api');
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function send($url, $data,$method)
    {
        $method = strtoupper($method);
        if($method=='GET'){
            $url .= "?".http_build_query($data);
            $data="";
        }else{
            $data = json_encode($data);
        }
        try {
            $ch = curl_init($url);
            if($method=='POST'){
                curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'accept: application/json',
                    'content-type: application/json',
                    'content-length: ' . strlen($data))
            );
            $result = curl_exec($ch);
            if ($result === false) {
                error_log(
                    print_r([
                    'time' => date('Y-m-d H:i:s'),
                    'request' => $data,
                    'message' => curl_error($ch)
                ],true),3,base_path('storage/logs/curl_error.log'));
            } else {
                error_log(print_r([
                    'time' => date('Y-m-d H:i:s'),
                    'request' => $data,
                    'message' => curl_error($ch)
                ],true),3,base_path('storage/logs/curl_success'.date('Y-m-d').'.log'));
            }
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    public function get($url,$data=[]){
        return $this->send($url,$data,'get');
    }

    public function post($url,$data=[]){
        return $this->send($url,$data,'post');
    }
}
