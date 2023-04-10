<?php

// +----------------------------------------------------------------------
// | redis表设置
// +----------------------------------------------------------------------

return [
    //redis
    'REDIS_HOST' => env('REDIS_HOST', '127.0.0.1'),
    'REDIS_PORT' => env('REDIS_PORT', '6379'),
    'REDIS_AUTH' => env('REDIS_PASSWORD', ''),
    'REDIS_TIMEOUT' => env('REDIS_TIMEOUT', '10'),
    // prod 浏览量表
    'prodView' => 'prod_view',
    // prod 浏览量表
    'prodViewByUsage' => 'prod_view_by_usage',
    // prod 按type细分流量量
    'prodViewByProdType' => 'prod_view_by_prod_type',
    // prod 汇总统计
    'prodStatisticsByProdType' => 'prod_statistics_by_prod_type',
    // prod 分享表
    'prodShareStatistics' => 'prod_share_statistics',
    //cat 浏览量
    'catView' => 'cat_view',

];
