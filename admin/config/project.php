<?php
/**
 *  ===========================================
 *  File Name   project.php
 *  Class Name  admin
 *  Date:       2019-10-24 11:00
 *  Created by
 *  Use for:    定义所有需要维护的常量
 *  ===========================================
 **/

return [
    //小程序-微信-数据统计
    'small_wechat_date_statistics' => [
        'wechat_api' => [
            //api-url
            'api_uri'    => 'https://api.weixin.qq.com/datacube/',
            //api-action
            'api_action' => [
                //用户留存-日留存
                'daily_retain'        => 'getweanalysisappiddailyretaininfo',
                //用户留存-月留存
                'monthly_retain'      => 'getweanalysisappidmonthlyretaininfo',
                //用户留存-周留存
                'weekly_retain'       => 'getweanalysisappidweeklyretaininfo',
                //小程序访问数据概括
                'daily_summary'       => 'getweanalysisappiddailysummarytrend',
                //访问趋势-日趋势
                'daily_visit_trend'   => 'getweanalysisappiddailyvisittrend',
                //访问趋势-月趋势
                'monthly_visit_trend' => 'getweanalysisappidmonthlyvisittrend',
                //访问趋势-周趋势
                'weekly_visit_trend'  => 'getweanalysisappidweeklyvisittrend',
                //数据分布-新增或活跃用户的画像
                'user_portrait'       => 'getweanalysisappiduserportrait',
                //数据分布-用户访问
                'visit_distribution'  => 'getweanalysisappidvisitdistribution',
                //访问页面
                'visit_page'          => 'getweanalysisappidvisitpage',
            ],
        ],
    ],
];
