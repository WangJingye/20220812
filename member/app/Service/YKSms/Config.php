<?php namespace App\Service\YKSms;

class Config
{
    const RESPONSE_CODE = [
        0=>'成功',
        -1=>'用户身份验证失败',
        -2=>'短信内容长度错误',
        -3=>'短信内容包括禁发关键字',
        -4=>'剩余短信数量不足',
        -5=>'手机号码和或内容为空/手机号码个数和信息编号个数配对错误',
        -1001=>'系统发生异常',
        500=>'内部服务器错误，Json格式和参数有错误',
    ];

    const API_MAP = [
        //查询剩余短信数量_请求消息
        'getRemain'=>'JsonGetBalance',
        //相同内容群发短信_请求消息
        'sendSms'=>'JsonSendSms',
    ];




}