<?php

namespace App\Lib;
use App\Http\Helpers\Api\GoodsCurlLog;

class Http
{
    public function curl($apiName, $data = [])
    {
        $url = config('api.map')[$apiName];
        $token = request()->header('token')??'';
        $refresh_token = request()->header('refresh_token')??'';

        $data = json_encode($data);
        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'accept: application/json',
                'content-type: application/json',
                'token: '.$token,
                'refresh-token: '.$refresh_token,
                'content-length: '.strlen($data))
                );

            $result = curl_exec($ch);
            $resultArray = \json_decode($result, true);
            $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $logger = new GoodsCurlLog();
            $logger->curlLog(json_decode($data),"Get" , $url,
                "", $return_code, $result );

            return $resultArray;
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }
}
