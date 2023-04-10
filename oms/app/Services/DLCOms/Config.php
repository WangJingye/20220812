<?php namespace App\Services\DLCOms;

class Config
{
    const RESPONSE_CODE = [
        200=>'成功',
        401=>'请求的时间与当前时间相差较大',
        402=>'签名错误',
        403=>'缺少必要参数',
        404=>'方法不存在',
        405=>'服务器繁忙',
        300=>'业务参数问题',
        500=>'服务器错误',
    ];

    const API_MAP = [
        //订单新增
        'orderAdd'=>'lvmh.site.order.add',
        //发票补开接口
        'invoiceAdd'=>'lvmh.site.invoice.add',
        //会员Code更新
        'memberUpdateCode'=>'lvmh.site.update.member.code',
        //物流轨迹同步
        'getLogistics'=>'lvmh.site.get.waybill',
    ];




}