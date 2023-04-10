<?php

return [
    //渠道配置 名称 id 初始默认占比，后期数据库设置后该比例无用
    'channel' => [
        ['name' => 'miniapp', 'id' => 1, 'percent' =>10],
//        ['name' => 'mobile', 'id' => 2, 'percent' => 2],
//        ['name' => 'pc', 'id' => 3, 'percent' => 3],
    ],

    'config'=>[
        'stock'=>0,//共享库存
        'secureinc'=>0,//安全库存增量
        'is_share'=>1,////共享库存
        'secure'=>0,  //安全库存默认值
        'stockinc'=>0,  //预扣增量
        'is_secure'=>0 //是否设置安全库存 1是 0否
    ]
];

?>