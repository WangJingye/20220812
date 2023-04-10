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

    /**
     * 微信 getVisitPage
     */
    public static function wxVisit()
    {
        $result = self::getAccessToken();
        $token = '';
        if (!empty($result['access_token'])) $token = $result['access_token'];
        $url = 'https://api.weixin.qq.com/datacube/getweanalysisappidvisitpage?access_token='.$token;
        $lastDate = date("Ymd", strtotime("-1 day"));
        $param = json_encode([
            'begin_date'    => $lastDate,
            'end_date'      => $lastDate
        ], true);
        $result = self::curl_post($url, $param);
        $wxVisit = json_decode($result, true);
        $ref_date = !empty($wxVisit['ref_date']) ? $wxVisit['ref_date'] : '';
        $list = !empty($wxVisit['list']) ? $wxVisit['list'] : [];
        return ['ref_date'  => $ref_date, 'list'    => $list];
    }

    public static function getAccessToken()
    {
        $appId = config('wechat.mini_program.app_id');;
        $appSecret = config('wechat.mini_program.secret');
        $postUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
        curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // output to variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 0); //post提交方式
        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }
}
