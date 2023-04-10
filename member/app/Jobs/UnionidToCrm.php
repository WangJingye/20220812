<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Model\WechatUsers;

class UnionidToCrm implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;

    protected $aHeader;

    protected $appId;

    protected $customerId;

    protected $wechatInfo;

    protected $gender;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customerId, WechatUsers $wechatUserData, $gender, $aHeader)
    {
        //
        $this->url = env('CRM_CUSTOMER_DOMAIN') . 'wechat-binds';

        $this->aHeader = $aHeader;

        $this->appId = env('APPID');

        $this->customerId = $customerId;

        $this->wechatInfo = $wechatUserData;

        $this->gender = $gender;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $wechat = [];
        $wechat['customerId'] = $this->customerId;
        $wechat['openId'] = $this->wechatInfo->openid;
        $wechat['appId'] = $this->appId;
        $wechat['gender'] = $this->gender;
        $wechat['unionId'] = $this->wechatInfo->unionid;
        $wechat['nickName']  = $this->wechatInfo->nickName;
        $wechat['headImageUrl']  = $this->wechatInfo->avatarUrl;
        $wechat['country']  = $this->wechatInfo->country;
        $wechat['province']  = $this->wechatInfo->province;
        $wechat['city']  = $this->wechatInfo->city;
        $wechat['stateCode'] = 'B';

        http_request($this->url, $wechat, $this->aHeader, 'POST', 'unionid同步CRM：');

        return true;
    }
}
