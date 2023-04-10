<?php
namespace App\Model;

class WechatCoupons extends Model
{
    protected $table = 'wechat_coupons';

    // 优惠券类型
    CONST TYPE = [
        'NEWER_COUPONS' => 1,
        'GENERAL_CONPONS' => 2
    ];

    /**
     * 模型的「启动」方法
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }
}