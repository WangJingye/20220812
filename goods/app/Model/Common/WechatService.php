<?php

/**
 * User: JIWI001
 * Date: 2019/11/27
 * Time: 22:00.
 */

namespace App\Model\Common;

class WechatService
{
    protected $accesstoken;
    protected $brand = 'css_mini_site';
    protected $tokenTable = 'wechat_access_token';
    protected $tokenExpired = 7000;

    public function __construct()
    {
        $this->resetAccessToken();
    }

    public function resetAccessToken($force = false)
    {
        if (true === $force) {
            $tokenInfo = $this->getAccessToken($force);
            $this->storeAccessTokentoDB($tokenInfo);
            $token = $tokenInfo['access_token'];
        } else {
            $accesstoken = $this->getAccessTokenFromDB();
            if (-1 === $accesstoken || 2 === $accesstoken) {
                $tokenInfo = $this->getAccessToken();
                $this->storeAccessTokentoDB($tokenInfo);
                $token = $tokenInfo['access_token'];
            } else {
                $token = $accesstoken;
            }
        }
        $this->accesstoken = $token;
    }

    private function getAccessToken()
    {
        $appID = config('wechat.app_id');
        $appSecret = config('wechat.app_secret');
        $postUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appID.'&secret='.$appSecret;
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

    public function wxApi($method, $param, $gets = [], $hasfile = false)
    {
        $result = $this->curlWx($method, $param, $gets, $hasfile);
        if ($this->isTokenInvaild($result)) {
            $this->resetAccessToken(true);
            $result = $this->curlWx($method, $param, $gets, $hasfile);
        }

        return $result;
    }

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

    public function curlWx($method, $param, $gets = [], $hasfile = false)
    {
        $str = '';
        foreach ($gets as $key => $get) {
            $str = "$str&$key=".$get;
        }
        $postUrl = "https://api.weixin.qq.com/$method?access_token=".$this->accesstoken.$str;
        $curlPost = $param;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
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

        curl_close($ch);

        return $result;
    }

    private function getAccessTokenFromDB()
    {
        $re = Wechat::where('name', $this->brand)->get()->toArray();
        if (empty($re)) {
            return -1;
        }
        if (empty($re[0]['token']) || (null !== $re[0]['expired_time'] && time() >= strtotime($re[0]['expired_time']))) {
            return 2;
        } else {
            return $re[0]['token'];
        }
    }

    private function storeAccessTokentoDB($data)
    {
        $bindInfo = [];
        $bindInfo['token'] = $data['access_token'];
        $bindInfo['expired_time'] = isset($data['expires_in']) ? date('Y-m-d H:i:s', time() + $this->tokenExpired) : null;

        Wechat::updateOrCreate(
            ['name' => $this->brand],
            $bindInfo
        );
    }
}
