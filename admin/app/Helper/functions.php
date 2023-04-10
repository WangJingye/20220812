<?php
/**
 * 保存配置项.
 */
if (!function_exists('option_set')) {
    function option_set($name, $value)
    {
        return \App\Model\Options::query()->where('option_name', $name)->updateOrInsert(['option_name' => $name], ['option_value' => serialize($value)]);
    }
}

/*
 * 获取配置项
 */
if (!function_exists('option_get')) {
    function option_get($name)
    {
        static $options;
        if (!isset($options[$name])) {
            $options[$name] = \App\Model\Options::query()->where('option_name', $name)->value('option_value');
        }

        return $options[$name];
    }
}

/*
 * 通过数组获取配置项
 */
if (!function_exists('option_get_with')) {
    function option_get_with($array)
    {
        $model = \App\Model\Options::query()->whereIn('option_name', $array);

        return $model->count() ? $model->pluck('option_value')->toArray() : [];
    }
}

/*
 * OSS是否启用
 */
if (!function_exists('oss_status')) {
    function oss_status()
    {
        static $oss_status;
        if (is_null($oss_status)) {
            $oss_status = \App\Model\ConfigOss::checkWork();
        }

        return $oss_status;
    }
}

/*
 * 获取OSS地址
 */
if (!function_exists('get_oss_path')) {
    function get_oss_path()
    {
        static $oss_path;
        if (is_null($oss_path)) {
            $oss = \App\Model\ConfigOss::query()->first();
            $oss_path = $oss->url ? ($oss->url.'images/') : '';
        }

        return $oss_path;
    }
}

/*
 * 获取OSS域名
 */
if (!function_exists('getImageUrl()')) {
    function getImageUrl($file)
    {
        if(env('OSS_ENABLE')){
            return env('OSS_DOMAIN').$file;
        }else{
            return $file;
        }

    }
}

if (!function_exists('nick_encode')) {
    function nick_encode($nick)
    {
        return (mb_substr($nick, 0, 1).'*****') ?: '';
    }
}

/*
 * 记录日志
 */
if (!function_exists('log_json')) {
    function log_json($dir, $method, $msg)
    {
        $path = public_path('logs/'.$dir.'/'.$method.'/');
        $filename = date('Ymd').'.log';
        $msg = date('[Y-m-d H:i:s]').$msg;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path.'/'.$filename, $msg.PHP_EOL, FILE_APPEND);
    }
}
/*
 * 记录日志
 */
if (!function_exists('array_to_object')) {
    function array_to_object($array)
    {
        return json_decode(json_encode($array));
    }
}

/*
 * 生成uuid
 */
if (!function_exists('create_uuid')) {
    function create_uuid($prefix = '')
    {    //可以指定前缀
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str, 0, 8).'-';
        $uuid .= substr($str, 8, 4).'-';
        $uuid .= substr($str, 12, 4).'-';
        $uuid .= substr($str, 16, 4).'-';
        $uuid .= substr($str, 20, 12);

        return $prefix.$uuid;
    }
}
/*
 * 生成uuid
 */
if (!function_exists('createUuidByTime')) {
    function createUuidByTime($prefix = '')
    {
        list($usec, $sec) = explode(' ', microtime());

        $mtime = date('ymdHis', $sec);
        $stime = (int) ($usec * 1000 * 1000);
        $stime = str_pad($stime, 6, '0', STR_PAD_LEFT);

        return $mtime.$stime;
    }
}

function toQuery($arr, $falg = true)
{
    if (true == $falg) {
        return '?'.http_build_query($arr);
    } else {
        return http_build_query($arr);
    }
}

/**
 * 专为后台获取商品详情所写的接口
 */
if (!function_exists('http_pdt_post_data')) {
    function http_pdt_post_data($url, $data_string)
    {
        $header = array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($data_string));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $logger = new \App\Http\Middleware\AdminApiLog();
        $logger->curlLog(json_decode($data_string, true), "POST", $url,
            $header, $return_code, json_encode($return_content));
        return array($return_code, $return_content);
    }
}



/**
 * 通过post方式CURL调用内部接口
 * @param $url
 * @param $data_string
 * @return array
 */
if (!function_exists('http_Post_Data')) {
    function http_Post_Data($url, $data_string)
    {
        $header = array(
            'User:  MiniAppRetain',
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        ob_start();
        curl_exec($ch);
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $logger = new \App\Http\Helpers\Api\AdminCurlLog();
        $logger->curlLog($data_string, "POST", $url,
            $header, $return_code, json_encode($return_content));
        return array($return_code, $return_content);
    }
}

if (!function_exists('new_http_request')) {
    function new_http_request($url, $data=FALSE, $aHeader=FALSE, $method='GET', $message=""){
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
        $logger = new \App\Http\Helpers\Api\AdminCurlLog();
        $logger->curlLog($data,$method , $url,
            $aHeader, $httpCode, json_encode($res) );
        curl_close($ch);
        return ['httpCode' => $httpCode, 'data' => $res];
    }


    if (!function_exists('getProductById')) {
        function getProductById($arr)
        {
            $url = 'http://goods.css.com.cn/goods/inner/getSearchProdsByIds';
            //生产环境走内网，测试环境走UAT外网
            if (env("APP_ENV") == "local") {
                $url = "https://wecapiuat.chowsangsang.com.cn/pdt/goods/inner/getSearchProdsByIds";
            }
            $param['prodIdStr'] = json_encode($arr);
            $response = http_pdt_post_data($url, json_encode($param));
            $product_info = json_decode($response[1], true)['data'];
            return $product_info;
        }
    }

    if (!function_exists('getProductDetailList')) {
        function getProductDetailList($view_ptdIdList)
        {
            $pdtid_chunk_result = array_chunk($view_ptdIdList, 10, true);
            $product_detail_list = [];
            foreach ($pdtid_chunk_result as $pdtIdList){
                $product_detail_list = array_merge($product_detail_list ,getProductById($pdtIdList)['list']);
            }
            return $product_detail_list;
        }
    }
    /**
     * 获取上个月的年月
     */
    if (!function_exists('getLastMonth')) {
        function getLastMonth()
        {
            $today_detail = getdate();
            $this_month = $today_detail['mon'];
            $last_month = $this_month - 1;
            $last_month_year = $today_detail["year"];
            if ($last_month == 0) {
                $last_month = 12;
                $last_month_year = $last_month_year - 1;
            }
            return "$last_month_year-$last_month";
        }
    }

    if (!function_exists('object2array_pre')) {
        function object2array_pre(&$object) {
            if (is_object($object)) {
                $arr = (array)($object);
            } else {
                $arr = &$object;
            }
            if (is_array($arr)) {
                foreach($arr as $varName => $varValue){
                    $arr[$varName] = object2array($varValue);
                }
            }
            return $arr;
        }
    }

    if (!function_exists('object2array')) {
        function object2array(&$object)
        {
            $object = json_decode(json_encode($object), true);
            return $object;
        }
    }

    if (!function_exists('processProductDetail')) {
        function processProductDetail($prod_detail_list, $pdtIdList)
        {
            $prod_detail_new = [];
            foreach ($prod_detail_list as $key => $prod_detail) {
                $prod_detail_new[$key]['pdtId'] = $prod_detail['pdtId'];
                $prod_detail_new[$key]['name'] = $prod_detail['name'];
                $prod_detail_new[$key]['series'] = $prod_detail['series'];
                $prod_detail_new[$key]['picUrl'] = $prod_detail['picUrl'];
                $prod_detail_new[$key]['scores'] = $pdtIdList[$key];
            }
            return $prod_detail_new;
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

}