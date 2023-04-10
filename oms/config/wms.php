<?php

return [

    'url'=>env('WMS_URL', 'http://webapp03.gqslogistics.com:8083/sisley_test/HttpPost_UTF8.aspx'),
    'app_key' => '20200628',
    'sign' => 'Sisley',
    'sign_method' => 'md5',
    //未支付取消前多少分钟提醒(废弃)
    'oms_over_time' => env('ORDER_OVER_TIME', '5'),
    //取消时间(秒)(默认24小时)
    'oms_cancel_time' => env('ORDER_CANCEL_TIME',7200),
];
