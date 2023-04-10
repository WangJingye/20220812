<?php

namespace App\Http\Middleware;

use Closure;

class MemberApiLog
{
    protected $log_frag;

    public function __construct()
    {
        //初始化一个Log标记，模块名+服务器名+年月日
        $this->log_frag = "_" .  env("MODULE_NAME") ."_" . gethostname() . "_" . date("Y-m-d") ;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->server('HTTP_ORIGIN');
        $allowOrigin = [
            'https://apiuat.dlc.com.cn',
            'https://www.dlc.com.cn',
            'http://localhost:8080',
            'https://uat.dlc.com.cn'
        ];
        $headers = [
            'Access-Control-Allow-Origin' => in_array($origin,$allowOrigin)?$origin:'',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With,access-token,version,Token,token,refresh-token,from'
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);

        }

        $response = $next($request);
        $content = $response->getContent();

        $decodeContent = json_decode($content);

        if(is_array($decodeContent) ){
            $this->apiLog($request->all(), $request->method(), $request->url(), "200", $decodeContent);
        }
        else{
            $this->apiLog($request->all(), $request->method(), $request->url(), "200", $content);
        }

        return $response;
    }


    /**
     * 作为API供小程序调用时的Log记录
     * @param $request
     * @param $method
     * @param $url
     * @param $statusCode
     * @param $response
     */
    public function apiLog($request, $method, $url, $statusCode, $response)
    {
        //如果正常响应了200，表示服务通讯正常，正常记录即可。
        if($statusCode == 200){
            $this->logApiSuccess($request, $method, $url, $statusCode, $response);
        }
        else{
            $this->logApiError($request, $method, $url, $statusCode, $response);
        }
    }

    /**
     * 通过Curl方式调用内部或外部接口Log记录
     * @param $request
     * @param $method
     * @param $url
     * @param $headers
     * @param $statusCode
     * @param $response
     */
    public function curlLog($request, $method, $url, $headers, $statusCode, $response)
    {
        //如果正常响应了200，表示服务通讯正常，正常记录即可。
        if($statusCode == 200){
            $this->logCurlSuccess($request, $method ,$url, $headers, $statusCode,
                json_decode($response, true));
        }
        else{
            //服务出现异常非200响应，通过UUID追踪错误信息明细
            $error_log_uuid = $this->create_uuid();
            $this->logCurlFail($request, $method, $url, $headers, $statusCode, $error_log_uuid);
            $this->logCurlErrorDetail($error_log_uuid, $statusCode, $response);
        }
    }


    /**
     * 记录成功的Curl请求记录
     * @param $request
     * @param $method
     * @param $fullUrl
     * @param $headers
     * @param $statusCode
     * @param $response
     */
    protected function logCurlSuccess( $request, $method, $fullUrl,  $headers, $statusCode,  $response)
    {
        $this->curl_info(
            array(
                'Request_body'   => array($request),
                'Request_Time' => date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]),
                'Request_IP' =>  getenv("HTTP_X_FORWARDED_FOR"),
                'Method'    => $method,
                'URL'       => $fullUrl,
                'HostName'   => gethostname(),
                'Headers'   => $headers,
                'HTTP code' => $statusCode,
                'Response' => $response,
                'Response_Time' => date("Y-m-d H:i:s"),
            )
        );
    }

    /**
     * 记录失败的Curl请求记录
     * @param $request
     * @param $fullURI
     * @param $method
     * @param $headers
     * @param $statusCode
     * @param $error_log_uuid
     */
    protected function logCurlFail($request,  $fullURI,$method,  $headers, $statusCode, $error_log_uuid )
    {
        $this->curl_warning(
            array(
                'Request_body'   => array($request),
                'Request_Time' => date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]),
                'Request_IP' =>  getenv("HTTP_X_FORWARDED_FOR"),
                'Method'    => $method,
                'URL'       => $fullURI,
                'HostName'   => gethostname(),
                'Headers'   => $headers,
                'HTTP code' => $statusCode,
                'Response_Time' => date("Y-m-d H:i:s"),
                'Response_UUID'  => $error_log_uuid
            )
        );

    }


    /**
     * 记录错误请求的错误明细
     * @param $error_log_uuid
     * @param $statusCode
     * @param $response
     */
    protected function logCurlErrorDetail($error_log_uuid, $statusCode, $response )
    {
        $this->curl_error(
            array(
                'Response_UUID' => $error_log_uuid,
                'Request_Time' => date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]),
                'Request_IP' =>  getenv("HTTP_X_FORWARDED_FOR"),
                'HTTP code' => $statusCode,
                'Response_Time' => date("Y-m-d H:i:s"),
                'Response_Body'  => $response
            )
        );

    }


    /**
     * 记录成功的Api被调用记录
     * @param $request
     * @param $method
     * @param $url
     * @param $statusCode
     * @param $response
     */
    protected function logApiSuccess($request, $method, $url, $statusCode, $response)
    {
        $this->api_info(
            array(
                'Request_Body'   => $request,
                'HTTP code' => $statusCode,
                'Method' =>         $method,
                'Url'   => $url,
                'Request_Time' => date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]),
                'Request_IP' =>  getenv("HTTP_X_FORWARDED_FOR"),
                'Response_Time' => date("Y-m-d H:i:s"),
                'Response_Body'  => array($response)
            )
        );
    }


    /**
     * 记录失败的Api被调用记录
     * @param $request
     * @param $method
     * @param $url
     * @param $statusCode
     * @param $response
     */
    protected function logApiError($request, $method, $url, $statusCode, $response)
    {
        $this->api_error(
            array(
                'Request_Body'   => array($request),
                'Request_Time' => date("Y-m-d H:i:s", $_SERVER["REQUEST_TIME"]),
                'Request_IP' =>  getenv("HTTP_X_FORWARDED_FOR"),
                'Method' =>         $method,
                'Url'   => $url,
                'HTTP code' => $statusCode,
                'Response_Time' => date("Y-m-d H:i:s"),
                'Response_Body'  => array($response)
            )
        );
    }


    protected function curl_info(array $request_info){
        //非开发模式下，日志存储到 这个目录下
        $path = storage_path('logs');
        if(env("APP_ENV") !== "local"){
            if(!file_exists($path)){
                mkdir($path, 0777);
            };
            file_put_contents($path.'/Curl_Success' . $this->log_frag .  ".Log",
                json_encode($request_info)."\r\n", FILE_APPEND);
        }else {
            if (!file_exists("Log")) {
                mkdir("Log");
            };
            file_put_contents('Log/Curl_Success' . $this->log_frag . ".Log",
                json_encode($request_info) . "\r\n", FILE_APPEND);
        }
    }


    protected function curl_warning(array $request_info)
    {
        //非开发模式下，日志存储到 /var/www/html/css/Log 这个目录下
        $path = storage_path('logs');
        if(env("APP_ENV") !== "local"){
            if(!file_exists($path)){
                mkdir($path, 0777);
            };
            file_put_contents($path.'/Curl_Fail' . $this->log_frag .  ".Log",
                json_encode($request_info)."\r\n", FILE_APPEND);
        }else {
            if (!file_exists("Log")) {
                mkdir("Log");
            };
            file_put_contents('Log/Curl_Fail' . $this->log_frag . ".Log",
                json_encode($request_info) . "\r\n", FILE_APPEND);
        }
    }


    protected function curl_error(array $response_info)
    {
        //非开发模式下，日志存储到 /var/www/html/css/Log 这个目录下
        $path = storage_path('logs');
        if(env("APP_ENV") !== "local"){
            if(!file_exists($path)){
                mkdir($path, 0777);
            };
            file_put_contents($path.'/Curl_Error' . $this->log_frag .  ".Log",
                json_encode($response_info)."\r\n", FILE_APPEND);
        }else {
            if (!file_exists("Log")) {
                mkdir("Log");
            };
            file_put_contents('Log/Curl_Error' . $this->log_frag . ".Log",
                json_encode($response_info) . "\r\n", FILE_APPEND);
        }
    }


    protected function api_info(array $request_info){
        //非开发模式下，日志存储到 /var/www/html/dlc-api/Log 这个目录下
        $path = storage_path('logs');
        if(env("APP_ENV") !== "local"){
            if(!file_exists($path)){
                mkdir($path, 0777);
            };
            file_put_contents($path.'/Api_Success' . $this->log_frag .  ".Log",
                json_encode($request_info)."\r\n", FILE_APPEND);
        }else {
            if (!file_exists("Log")) {
                mkdir("Log");
            };
            file_put_contents('Log/Api_Success' . $this->log_frag . ".Log",
                json_encode($request_info) . "\r\n", FILE_APPEND);
        }
    }


    protected function api_warning(array $request_info)
    {
        if(!file_exists("Log")){
            mkdir("Log");
        };
        file_put_contents('Log/Api_Warning' . $this->log_frag .  ".Log",
            json_encode($request_info)."\r\n", FILE_APPEND);
    }


    protected function api_error(array $response_info)
    {
        //非开发模式下，日志存储到 /var/www/html/dlc-api/Log 这个目录下
        $path = storage_path('logs');
        if(env("APP_ENV") !== "local"){
            if(!file_exists($path)){
                mkdir($path, 0777);
            };
            file_put_contents($path.'/Api_Error' . $this->log_frag .  ".Log",
                json_encode($response_info)."\r\n", FILE_APPEND);
        }else {
            if (!file_exists("Log")) {
                mkdir("Log");
            };
            file_put_contents('Log/Api_Error' . $this->log_frag . ".Log",
                json_encode($response_info) . "\r\n", FILE_APPEND);
        }
    }


    /**
     * 生成UUID
     * @param string $prefix
     * @return string
     */
    protected static function create_uuid($prefix = ""){
        $str = md5(uniqid(mt_rand(), true));
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;
    }
}
