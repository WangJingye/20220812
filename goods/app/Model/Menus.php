<?php

namespace App\Model;

class Menus
{
    static public function getMenus(){
        return [
            [
                'name'=>'admin-system',
                'display_name' => '系统管理',
                'route' => '',
                'child' => [
                    [
                        'name'=>'admin-user',
                        'display_name' => '用户管理',
                        'route' => 'backend.user.index',
                        'child' => []
                    ],
                    [
                        'name'=>'config-oss',
                        'display_name' => 'OSS配置',
                        'route' => 'backend.config.oss',
                        'child' => []
                    ],
                ]
            ],
            [
                'name'=>'admin-config',
                'display_name' => '配置管理',
                'route' => '',
                'child' => [
                    [
                        'name'=>'admin-page',
                        'display_name' => '页面管理',
                        'route' => 'backend.page.index',
                        'child' => []
                    ],
                ]
            ],
            
            
            [
                'name'=>'admin-promotion',
                'display_name' => '促销规则',
                'route' => '',
                'child' => [
                    [
                        'name'=>'admin-promotion-cateogry',
                        'display_name' => '分类规则',
                        'route' => 'backend.promotion.category',
                        'child' => []
                    ],
                    [
                        'name'=>'admin-promotion-cart',
                        'display_name' => '购物车规则',
                        'route' => 'backend.promotion.cart',
                        'child' => []
                    ],
                    [
                        'name'=>'admin-promotion-coupon',
                        'display_name' => '优惠劵',
                        'route' => 'backend.promotion.coupon',
                        'child' => []
                    ],
                    [
                        'name'=>'admin-promotion-coupon_tag',
                        'display_name' => '优惠劵标签',
                        'route' => 'backend.promotion.coupon_tag',
                        'child' => []
                    ]
                ],
            ]
            
            
        ];
    }

}
