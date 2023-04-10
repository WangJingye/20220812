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
            $oss_path = $oss->url ? ($oss->url . 'images/') : '';
        }

        return $oss_path;
    }
}

/*
 * 获取OSS域名
 */
if (!function_exists('get_oss_url')) {
    function get_oss_url()
    {
        static $oss_url;
        if (is_null($oss_url)) {
            $oss_url = App\Model\ConfigOss::find(1)->url;
        }

        return $oss_url;
    }
}

if (!function_exists('nick_encode')) {
    function nick_encode($nick)
    {
        return (mb_substr($nick, 0, 1) . '*****') ?: '';
    }
}

/*
 * 记录日志
 */
if (!function_exists('log_json')) {
    function log_json($dir, $method, $msg)
    {
        $path = public_path('logs/' . $dir . '/' . $method . '/');
        $filename = date('Ymd') . '.log';
        $msg = date('[Y-m-d H:i:s]') . $msg;
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        file_put_contents($path . '/' . $filename, $msg . PHP_EOL, FILE_APPEND);
    }
}

/*
 * 二维数组交集
 */
if (!function_exists('two_dimen_array_intersect')) {
    function two_dimen_array_intersect(array $array1, array $array2)
    {
        $inter = array_filter($array1, function ($v) use ($array2) {
            return in_array($v, $array2);
        });

        return $inter;
    }
}
/*
 * 二维数组差集
 */
if (!function_exists('two_dimen_array_diff')) {
    function two_dimen_array_diff(array $array1, array $array2)
    {
        $diff = array_filter($array1, function ($v) use ($array2) {
            return !in_array($v, $array2);
        });

        return $diff;
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
        $stime = (int)($usec * 1000 * 1000);
        $stime = str_pad($stime, 6, '0', STR_PAD_LEFT);

        return $mtime . $stime;
    }
}

/*
 * 生成uuid
 */
if (!function_exists('object2Array')) {
    function object2Array($obj)
    {
        return json_decode(json_encode($obj),true);
    }
}


if (!function_exists('http_curl')){
    function http_curl($aiBeautyUrl, $data){

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $aiBeautyUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        return $response;
    }
}
