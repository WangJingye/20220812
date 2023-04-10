<?php

if (! function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  array|string  $key
     * @param  mixed   $default
     * @return \Illuminate\Http\Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        if (is_array($key)) {
            return app('request')->only($key);
        }

        $value = app('request')->__get($key);

        return is_null($value) ? value($default) : $value;
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
    }
}

if (!function_exists('array2Xml')) {
    function array2Xml(array $data) {
        return app('ArrayToXML')->generate($data);
    }
}

if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

if (!function_exists('config_path'))
{
    function config_path($path = '')
    {
        return app()->basePath().DIRECTORY_SEPARATOR.'config'.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('magento_media_path'))
{
    function magento_media_path($path = '')
    {
//        return env('MAGENTO_API_DOMAIN').'/media/catalog/product'.$path;
        // TODO 暂时写死
        return 'http://asics-connext.oss-cn-zhangjiakou.aliyuncs.com/pdt-url.png';
    }
}

if (!function_exists('env_url'))
{
    function env_url($path = '')
    {
        return env('APP_URL').($path?('/'.$path):'');
    }
}

if (!function_exists('web_url'))
{
    function web_url($path = '')
    {
        return env('WEB_URL').($path?('/'.$path):'');
    }
}

if (!function_exists('oss_url'))
{
    function oss_url($path = '')
    {
        return env('OSS_URL').($path?('/'.$path):'');
    }
}

if (!function_exists('cut_str'))
{
    /**
     * 超过指定长度用省略号代替
     * @param $string
     * @param $sublen
     * @param int $start
     * @param string $code
     * @return string
     */
    function cut_str($string, $sublen, $start = 0, $code = 'UTF-8')
    {
        if($code == 'UTF-8'){
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);

            if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
            return join('', array_slice($t_string[0], $start, $sublen));
        }else{
            $start = $start*2;
            $sublen = $sublen*2;
            $strlen = strlen($string);
            $tmpstr = '';

            for($i=0; $i< $strlen; $i++){
                if($i>=$start && $i< ($start+$sublen)){
                    if(ord(substr($string, $i, 1))>129){
                        $tmpstr.= substr($string, $i, 2);
                    }else{
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if(ord(substr($string, $i, 1))>129) $i++;
            }
            if(strlen($tmpstr)< $strlen ) $tmpstr.= "...";
            return $tmpstr;
        }
    }
}

if (!function_exists('filtersc'))
{
    //过滤特殊字符
    function filtersc($str)
    {
        $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\"|\'|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
        return preg_replace($regex,"",$str);
    }
}
