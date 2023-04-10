<?php

/**
 * User: JIWI001
 * Date: 2018/09/11
 * Time: 16:06.
 */

namespace App\Lib;
use App\Http\Helpers\Api\AdminCurlLog;

class Curl
{
    public function __construct()
    {
    }

    /**
     * 封装CURL.
     *
     * @param string $postUrl
     * @param array  $param
     * @param array  $gets
     * @param string $url
     *
     * @return array
     */
    public static function curl(string $postUrl, array $param = [], array $gets = [], boolen $hasfile = null): array
    {
        try {
            $requestTime = date('Y-m-d H:i:s');
            if ($gets) {
                $str = '';
                foreach ($gets as $key => $value) {
                    $str = $str.'&'.$key.'='.$value;
                }
                $postUrl = "$postUrl?".substr($str, 1);
            }
            $curlPost = $param;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $postUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // output to variable
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            if (!empty($curlPost)) {
                if (true === $hasfile) {
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                }
                curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
                curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            }
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $logger = new AdminCurlLog();
            $logger->curlLog($curlPost,"POST" , $postUrl,
                "", $httpCode, $result );
            self::rpcLog($postUrl, $requestTime, $curlPost, date('Y-m-d H:i:s'), json_encode($result));

            return ['code' => $httpCode, 'data' => $result];
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * RPC请求Log.
     *
     * @param string $method
     * @param string $requestTime
     * @param array  $request
     * @param string $time
     * @param string $response
     *
     * @return void
     */
    private static function rpcLog(string $method, string $requestTime, array $request, string $time, string $response): void
    {
        $txt = 'Log Time:'.date('Y-m-d H:i:s')."\n";
        $txt .= 'Method:'.$method."\n";
        $txt .= 'Request Time:'.$requestTime."\n";
        $txt .= 'Request:'.json_encode($request)."\n";
        $txt .= 'Response:'.trim($response)."\n";
        $txt .= 'Runtime:'.$time."\r\n------------------------------\r\n";
        if(!is_dir(base_path('storage/oss'))){
            mkdir(base_path('storage/oss'), 0777, true);
        }
        file_put_contents(base_path('storage/oss/').date('YmdH').'.log', $txt, FILE_APPEND);
    }
}
