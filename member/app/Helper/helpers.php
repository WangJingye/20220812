<?php
/**
 * 保存配置项
 */
if (!function_exists('http_request'))
{
    function http_request($url, $data=FALSE, $aHeader=FALSE, $method='GET', $message="")
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else if($method == 'PATCH'){
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "PATCH"); //设置请求方式
        }
        if($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if($aHeader) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $logger = new \App\Http\Helpers\Api\MemberCurlLog();
        $logger->curlLog($data, $method, $url, $aHeader, $httpCode, json_encode($res));
        curl_close($ch);
        return ['httpCode' => $httpCode, 'data' => $res];
    }

}


if (!function_exists('curl_post_service'))
{
    function curl_post_service($url, $data, $message)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HEADER => true,

            CURLOPT_POSTFIELDS =>  $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));
        $res = curl_exec($ch);

        // 获得响应结果里的：头大小
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        // 根据头大小去获取头信息内容
        $header = substr($res, 0, $headerSize);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $body = substr($res, $headerSize);
        $logger = new \App\Http\Helpers\Api\MemberCurlLog();
        $logger->curlLog($data, 'POST', $url, $header, $httpCode, json_encode($res));
        curl_close($ch);
        return ['httpCode' => $httpCode, 'header' => $header, 'data' => $body];
    }
}


if (!function_exists('curl_post_ticket'))
{
    function curl_post_ticket($url, $data, $message="")
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "service=".$data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
            ),
            CURLOPT_SSL_VERIFYPEER => 0, // 对认证证书来源的检查
            CURLOPT_SSL_VERIFYHOST => 0, // 从证书中检查SSL加密算法是否存在 
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        logger($message, [
            'time' => date('Y-m-d H:i:s'),
            'url' => $url,
            'params' => "service=".$data,
            'httpCode' => $httpCode,
            'response' => $response
        ]);
        curl_close($curl);

        return ['httpCode' => $httpCode, 'data' => $response];

    }

}

if (!function_exists('curl_get_customer'))
{
    function curl_get_customer($url, $message="")
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);

        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        logger($message, [
            'time' => date('Y-m-d H:i:s'),
            'url' => $url,
            'httpCode' => $httpCode,
            'response' => $response
        ]);
        curl_close($curl);
        return ['httpCode' => $httpCode, 'data' => $response];

    }
}

if (!function_exists('loger')) {
    function loger($data, $path)
    {
        $path = 'logs/' . $path;
        if (!is_dir(storage_path($path))) {
            mkdir(storage_path($path), 0777, true);
        }
        error_log(PHP_EOL.'['.date('H:i:s')."] ".json_encode($data).PHP_EOL, 3, storage_path($path . '/' . date('Ymd') . '.log'));
    }
}
if (!function_exists('rand_code')) {
    function rand_code()
    {
        $key = '';
        $pattern = '1234567890'; // 无 l o
        for ($i = 0; $i < 6; $i++) {
            $key .= $pattern[rand(0, 9)];
        }
        return $key;
    }
}

/**
 * 记录日志
 */
if (! function_exists('log_json')) {
    function log_json($dir,$msg){
        $msg = is_array($msg)?json_encode($msg,320):$msg;
        $msg = stripcslashes($msg);
        $path = storage_path('logs/'.$dir.'/');
        $filename = date('Ymd').'.log';
        $msg = date('[Y-m-d H:i:s]').$msg;
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        file_put_contents($path.'/'.$filename,$msg.PHP_EOL,FILE_APPEND);
    }
}

if (!function_exists('log_array')) {
    function log_array($dir,$data){
        $data = is_string($data)?[$data]:$data;
        //保存到文件
        $path = storage_path('logs/'.$dir.'/');
        $filename = date('Ymd').'.log';
        $_msg = "####################################################\n\r";
        $_msg .= date('[Y-m-d H:i:s]').print_r($data,true);
        $_msg .= "####################################################\n\r";
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        file_put_contents($path.'/'.$filename,$_msg.PHP_EOL,FILE_APPEND);
        $_msg = null;
    }
}

if (!function_exists('getFilesize')) {
    function getFilesize($num)
    {
        $p = 0;
        $format = 'bytes';
        if ($num > 0 && $num < 1024) {
            $p = 0;
            return number_format($num) . ' ' . $format;
        }
        if ($num >= 1024 && $num < pow(1024, 2)) {
            $p = 1;
            $format = 'KB';
        }
        if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
            $p = 2;
            $format = 'MB';
        }
        if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
            $p = 3;
            $format = 'GB';
        }
        if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
            $p = 3;
            $format = 'TB';
        }
        $num /= pow(1024, $p);
        return number_format($num, 3) . ' ' . $format;
    }
}
