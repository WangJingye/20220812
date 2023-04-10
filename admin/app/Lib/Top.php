<?php

namespace App\Lib;

require_once 'vendor/topsdk/TopSdk.php';

class Top
{
    private $appkey;
    private $appsecret;
    private $sessionkey;
    private $callbackurl;
    private $topClient;

    public function __construct()
    {
        list($this->appkey,$this->appsecret,$this->sessionkey,$this->callbackurl)=option_get_with([
            'config_page_tb_appkey',
            'config_page_tb_appsecret',
            'config_page_tb_sessionkey',
            'config_page_tb_callbackurl',
        ]);
        $this->__init();
    }

    protected function __init(){
        $this->topClient = new \TopClient;
        $this->topClient->appkey = $this->appkey;
        $this->topClient->secretKey = $this->appsecret;
        $this->topClient->format = 'json';
    }

    /**
     * 判断该会员是否入会
     * @param $mix_nick
     * @return int
     */
    public function isMember($mix_nick){
        $req = new \CrmMemberIdentityGetRequest;
        $req->setMixNick($mix_nick);
        $resp = $this->topClient->execute($req, $this->sessionkey);
        return isset($resp->result->member_info->grade)?1:0;
    }

    /**
     * 获取入会地址
     * @return string
     */
    public function getMemberJoinUrl(){
        $req = new \CrmMemberJoinurlGetRequest;
        $extraInfo = '{"source":"paiyangji","deviceId":"1","itemId":1}';
        $req->setExtraInfo($extraInfo);
        $req->setCallbackUrl($this->callbackurl);
        $resp = $this->topClient->execute($req, $this->sessionkey);
        return isset($resp->result->result)?('http:'.$resp->result->result):'';
    }
}



