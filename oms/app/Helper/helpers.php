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
        $_msg = null;
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

if (!function_exists('get_client_ip')) {
    function get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }

    if (!function_exists('object2array')) {
        function object2array(&$object)
        {
            $object = json_decode(json_encode($object), true);
            return $object;
        }
    }


    /**
    @par $val 字符串参数，可能包含恶意的脚本代码如<script language="javascript">alert("hello world");</script>
     * @return  处理后的字符串
     **/
    if (!function_exists('removeXSS')) {
        function removeXSS($val)
        {
            $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
            $search = 'abcdefghijklmnopqrstuvwxyz';
            $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $search .= '1234567890!@#$%^&*()';
            $search .= '~`";:?+/={}[]-_|\'\\';
            for ($i = 0; $i < strlen($search); $i++) {
                // ;? matches the ;, which is optional
                // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

                // @ @ search for the hex values
                $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
                // @ @ 0{0,7} matches '0' zero to seven times
                $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
            }

            // now the only remaining whitespace attacks are \t, \n, and \r
            $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base');
            $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
            $ra = array_merge($ra1, $ra2);

            $found = true; // keep replacing as long as the previous round replaced something
            while ($found == true) {
                $val_before = $val;
                for ($i = 0; $i < sizeof($ra); $i++) {
                    $pattern = '/';
                    for ($j = 0; $j < strlen($ra[$i]); $j++) {
                        if ($j > 0) {
                            $pattern .= '(';
                            $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                            $pattern .= '|';
                            $pattern .= '|(&#0{0,8}([9|10|13]);)';
                            $pattern .= ')*';
                        }
                        $pattern .= $ra[$i][$j];
                    }
                    $pattern .= '/i';
                    $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
                    $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags

                    if ($val_before == $val) {
                        // no replacements were made, so exit the loop
                        $found = false;
                    }
                }
            }
            return $val;
        }
    }

    if (!function_exists('cutSubstr')) {
        function cutSubstr($str,$len=30,$suffix='...'){
            if (strlen($str)>$len) {
                $str=cut_str($str,$len,0,'UTF-8');
            }
            return $str;
        }
    }
}
