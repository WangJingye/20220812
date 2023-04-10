<?php

return [
    'dlc_crm_username'=>env('DLC_CRM_USERNAME'),
    'dlc_crm_password'=>env('DLC_CRM_PASSWORD'),
    'dlc_crm_url'=>env('DLC_CRM_URL'),

    'dlc_crm_channel'=>env('DLC_CRM_CHANNEL'),

    'dlc_yk_sms_username'=>env('DLC_YK_SMS_USERNAME'),
    'dlc_yk_sms_password'=>env('DLC_YK_SMS_PASSWORD'),
    'dlc_yk_sms_url'=>env('DLC_YK_SMS_URL'),

    'dlc_level'=>[
        [
            'id'=>1,
            'code'=>'C',
            'name'=>'普卡会员',
        ],
        [
            'id'=>2,
            'code'=>'BL',
            'name'=>'蓝卡会员',
        ],
        [
            'id'=>3,
            'code'=>'YL',
            'name'=>'黄卡会员',
        ],
        [
            'id'=>4,
            'code'=>'BK',
            'name'=>'黑卡会员',
        ]
    ],
    //会员升级所需金额
    'dlc_level_up_amounts_needs'=>[
        1=>0,
        2=>1,
        3=>3000,
        4=>6000,
    ],
    //sftp
    'sftp'=>[
        'host'=>env('SFTP_HOST'),
        'port'=>env('SFTP_PORT'),
        'user'=>env('SFTP_USER'),
        'pwd'=>env('SFTP_PWD'),
        'dir'=>env('SFTP_DIR'),
    ],

    'sftp_file'=>[
        'host'=>env('SFTP_FILE_HOST'),
        'port'=>env('SFTP_FILE_PORT'),
        'user'=>env('SFTP_FILE_USER'),
        'pwd'=>env('SFTP_FILE_PWD'),
        'dir'=>env('SFTP_FILE_DIR'),
    ],
    //个人中心公众号链接
    'inactive'=>'https://mp.weixin.qq.com/mp/profile_ext?action=home&__biz=MzU2ODg3MzYwMQ==&scene=124#wechat_redirect',
    'inactive2'=>'https://mp.weixin.qq.com/s/FVhEx5ojcXFYwIgJM8kXSQ',
];