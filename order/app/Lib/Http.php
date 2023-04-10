<?php
namespace App\Lib;
use App\Http\Helpers\Api\OrderCurlLog;
use Illuminate\Support\Facades\Log;

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
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $logger = new OrderCurlLog();
            $logger->curlLog(json_decode($data),"Get" , $url,
                "", $return_code, $result );
            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * [formCurl 提交form 表单]
     * @Author   Peien
     * @DateTime 2020-07-27T18:13:40+0800
     * @param    [type]                   $apiName [description]
     * @param    array                    $data    [description]
     * @return   [type]                            [description]
     */
    public function formCurl($apiName,$data=[]){
        $url = config('api.map')[$apiName] ?? $apiName;
        $headers = array('Content-Type: application/x-www-form-urlencoded');
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            $result = curl_exec($ch);
            //$resultArray=\json_decode($result,true);
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            return $result;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }




    /*
     * 微信API调用方法
     * */
    public function api_request($url,$data=null){
        Log::info($url);
        //初始化cURL方法
        $ch = curl_init();
        //设置cURL参数（基本参数）
        $opts = array(
            //在局域网内访问https站点时需要设置以下两项，关闭ssl验证！
            //此两项正式上线时需要更改（不检查和验证认证）
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
        );
        curl_setopt_array($ch, $opts);
        //post请求参数
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //执行cURL操作
        $output = curl_exec($ch);
        if (curl_errno($ch)) {    //cURL操作发生错误处理。
            var_dump(curl_error($ch));
            die;
        }
        //关闭cURL
        curl_close($ch);
        $res = json_decode($output);
        Log::info($res);
        return ($res);   //返回json数据
    }



}
