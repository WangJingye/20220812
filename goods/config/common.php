<?php

return [
    'five_minute'=>300,
    'a_minute'=>60,
    'ten_minute'=>600,
    'a_day'=>86400,
    'a_month'=>2592000,
    'a_hour'=>3600,

    //筛选的价格区间
    'option_price'=>[
        [
            'key'=>1,
            'name'=>'600以下',
            'condition'=>['gte'=>'0.01','lte'=>'599.99'],
        ],
        [
            'key'=>2,
            'name'=>'600-1000',
            'condition'=>['gte'=>'600','lte'=>'1000'],
        ],
        [
            'key'=>3,
            'name'=>'1000-2000',
            'condition'=>['gte'=>'1000','lte'=>'2000'],
        ],
        [
            'key'=>4,
            'name'=>'2000以上',
            'condition'=>['gte'=>'2000','lte'=>'99999999'],
        ]
    ],
];