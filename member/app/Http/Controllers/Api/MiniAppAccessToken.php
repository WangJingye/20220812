<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/27
 * Time: 10:55
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class MiniAppAccessToken extends Controller
{
    protected $accessToken;
    protected $tokenKey = 'MiniAppAccessToken';
    protected $tokenExpired = 7000;

    public function __construct()
    {
        $this->resetAccessToken();
    }

    public function resetAccessToken($force = false)
    {
        if (true === $force) {
            $tokenInfo = $this->getAccessToken();
            $token = $tokenInfo['access_token'];
            $this->setAccessTokenToRedis($this->tokenKey, $token, $this->tokenExpired);
        } else {
            $accessToken = $this->getAccessTokenFromRedis();
            if (-1 === $accessToken) {
                $tokenInfo = $this->getAccessToken();
                $token = $tokenInfo['access_token'];
                $this->setAccessTokenToRedis($this->tokenKey, $token, $this->tokenExpired);
            } else {
                $token = $accessToken;
            }
        }
        $this->accessToken = $token;
    }


    /**
     * 获取Token并拼接请求url，发送请求给微信接口
     * @param $postUrl
     * @param $param
     * @return mixed
     */
    public function wxApi($postUrl, $param)
    {
        $fullPostUrl    =   $postUrl . $this->accessToken;
        //请求的URL中，token要用最新的
        $result = $this->curlWx($fullPostUrl, $param);
        if ($this->isTokenInvaild($result)) {
            $this->resetAccessToken(true);
            $fullPostUrl    =   $postUrl . $this->accessToken;
            //Token刷新，请求的URL中Token也要变了
            $result = $this->curlWx($fullPostUrl, $param);
        }
        return $result;
    }

    /**
     * 校验Token是否失效
     * @param $data
     * @return bool
     */
    private function isTokenInvaild($data)
    {
        $tmparr = json_decode($data, true);
        if ('0' === $tmparr['errcode']) {
            return false;
        }
        if (preg_match('/access_token expired/i', $tmparr['errmsg']) || preg_match('/access_token is invalid/i', $tmparr['errmsg']) || preg_match('/invalid access_token/i', $tmparr['errmsg'])) {
            return true;
        }

        return false;
    }

    public function curlWx($postUrl, $param)
    {
        $curlPost = $param;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
        curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // output to variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        if (!empty($curlPost)) {
            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        }
        $result = curl_exec($ch);

        curl_close($ch);
        $this->rpcLog($postUrl, $curlPost, $result, date('Y-m-d H:i:s'));

        return $result;
    }

    private function getAccessTokenFromRedis()
    {
        $redis = app('redis.connection');
        $accessToken    =   $redis->get($this->tokenKey);
        if (empty($accessToken)){
            return -1;
        }
        return $accessToken;
    }

    private function setAccessTokenToRedis($tokenKey, $token, $tokenExpired)
    {
        $redis = app('redis.connection');
        $redis->set($tokenKey, $token, $tokenExpired);
    }


    public function getAccessToken()
    {
        $appId = config('app.appid');
        $appSecret = config('app.secret');
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

    private function rpcLog($method, $request, $response, $time)
    {
        if (!is_array(json_decode($response,true))){
            $response = "二维码生成成功";
        }
        $data = ['method' => $method, 'request' => $request, 'response' => $response, 'time' => $time];
        $this->validLog($data);
    }

    private function validLog($content)
    {
        $txt = 'Log Time:'.date('Y-m-d H:i:s')."\n";
        $txt .= 'Method:'.$content['method']."\n";
        $txt .= 'Request:'.$content['request']."\n";
        $txt .= 'Response:'.trim($content['response'])."\n";
        $txt .= 'Runtime:'.$content['time']."\r\n------------------------------\r\n";
        file_put_contents('Debug/'.date('Ymd').'.log', $txt, FILE_APPEND);
    }

}