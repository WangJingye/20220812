<?php

namespace App\Model\Common;

class YouShu
{
    /**
     * 获取签名
     */
    public static function getReqSign()
    {
        $app_id = config('youshu.app_id');
        $app_secret = config('youshu.app_secret');
        $date = strtotime(date('YmdHis'));
        $nonce = rand(1000000, 100000000);
        $str = "app_id=" . $app_id . "&nonce=" . $nonce . "&sign=sha256&timestamp=" . $date;
        $sign = hash_hmac('sha256', $str, $app_secret, false);
        return $str . '&signature='.$sign;
    }

    /**
     * curl post
     */
    public static function curl_post($url, $param)
    {
        $header = array('Content-Type: application/json');
        $ch = curl_init();    // 启动一个CURL会话
        curl_setopt($ch, CURLOPT_URL, $url);     // 要访问的地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // 对认证证书来源的检查   // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        // curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($ch, CURLOPT_POST, true); // 发送一个常规的Post请求
        curl_setopt($ch, CURLOPT_POSTFIELDS, $param);     // Post提交的数据包
        // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);     // 设置超时限制防止死循环
        // curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        //curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);     // 获取的信息以文件流的形式返回 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //模拟的header头
        $data = curl_exec($ch);

        // 显示错误信息
        if (curl_error($ch)) {
            print "Error: " . curl_error($ch);
        } else {
            // 打印返回的内容
            curl_close($ch);
            return $data;
        }
    }
}
