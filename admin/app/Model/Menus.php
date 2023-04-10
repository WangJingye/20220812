<?php

namespace App\Model;

class   Menus
{
    public static function getMenus()
    {
        return [
//            [
//                'name' => 'admin-dashboard',
//                'display_name' => 'DASHBOARD',
//                'route' => 'backend.dashboard.index',
//                'permission' => 'admin.dashboard',
//                'child' => [],
//            ],
            [
                'name' => 'admin-sales',
                'display_name' => '订单',
                'route' => '',
                'permission' => 'sales.index',
                'child' => [
//                    [
//                        'name' => 'admin-sales-order',
//                        'display_name' => '订单',
//                        'route' => 'backend.sales.order',
//                        'permission' => 'sales.order.index',
//                    ],
                    [
                        'name' => 'admin-order-index',
                        'display_name' => '订单管理',
                        'route' => 'backend.oms.order',
                        'permission' => 'oms.order.index',
                    ],
                    [
                        'name' => 'admin-returnapply-index',
                        'display_name' => '退货申请',
                        'route' => 'backend.oms.returnapply',
                        'permission' => 'oms.returnapply.index',
                    ],
//                    [
//                        'name' => 'admin-order-add',
//                        'display_name' => '手工单管理',
//                        'route' => 'backend.oms.add',
//                        'permission' => 'oms.add.index',
//                    ],

//                    [
//                        'name' => 'admin-order-item',
//                        'display_name' => '子订单',
//                        'route' => 'backend.oms.order.item',
//                        'permission' => 'oms.order.index',
//                    ],


                ],
            ],
            [
                'name' => 'admin-sales',
                'display_name' => '财务管理',
                'route' => '',
                'permission' => 'finance.index',
                'child' => [
//                    [
//                        'name' => 'admin-sales-order',
//                        'display_name' => '订单',
//                        'route' => 'backend.sales.order',
//                        'permission' => 'sales.order.index',
//                    ],
                    [
                        'name' => 'admin-order-index',
                        'display_name' => '订单退款管理',
                        'route' => 'backend.oms.order.manager',
                        'permission' => 'finance.index',
                    ],

//                    [
//                        'name' => 'admin-order-item',
//                        'display_name' => '子订单',
//                        'route' => 'backend.oms.order.item',
//                        'permission' => 'oms.order.index',
//                    ],


                ],
            ],
            [
                'name' => 'admin-store',
                'display_name' => '店铺',
                'route' => '',
                'permission' => 'store.index',
                'child' => [
//                    [
//                        'name' => 'admin-store-index',
//                        'display_name' => '门店',
//                        'route' => 'backend.store.index',
//                        'permission' => 'store.index',
//                    ],
                    [
                        'name' => 'admin-store-employee',
                        'display_name' => '导购列表',
                        'route' => 'backend.store.employee.index',
                        'permission' => 'store.index',
                    ],
//                    [
//                        'name' => 'admin-store-role',
//                        'display_name' => '职位列表',
//                        'route' => 'backend.store.role.index',
//                        'permission' => 'store.index',
//                    ],
                    /*[
                        'name' => 'admin-store-guide',
                        'display_name' => '导购列表',
                        'route' => 'backend.store.guide.index',
                        'permission' => 'store.index',
                    ],*/
                    [
                        'name' => 'admin-store-guide',
                        'display_name' => '导购实时数据列表',
                        'route' => 'backend.store.guide.realTime',
                        'permission' => 'store.index',
                    ],


                ],
            ],
            [
                'name' => 'admin-goods',
                'display_name' => '商品管理',
                'route' => '',
                'permission' => 'goods.index',
                'child' => [
                    [
                        'name' => 'admin-goods-category',
                        'display_name' => '分类管理',
                        'route' => 'backend.goods.category',
                        'permission' => 'goods.category.index',
                    ],
                    [
                        'name' => 'admin-goods-spu',
                        'display_name' => '产品管理',
                        'route' => 'backend.goods.spu',
                        'permission' => 'goods.spu.index',
                    ],
                    [
                        'name' => 'admin-goods-sku',
                        'display_name' => 'SKU管理',
                        'route' => 'backend.goods.sku',
                        'permission' => 'goods.sku.index',
                    ],
//                    [
//                        'name' => 'admin-goods-collection',
//                        'display_name' => '商品集合管理',
//                        'route' => 'backend.goods.collection',
//                        'permission' => 'goods.collection.index',
//                    ],
//                    [
//                        'name' => 'admin-hot-sale-product',
//                        'display_name' => '畅销榜单管理',
//                        'route' => 'backend.ad.item.hotsale',
//                        'permission' => 'goods.hotsale.index',
//                    ],
                    [
                        'name' => 'admin-goods-data',
                        'display_name' => '数据导入',
                        'route' => 'backend.config.data',
                        'permission' => 'system.user.index',
                    ],
                ],
            ],
            [
                'name' => 'admin-goods',
                'display_name' => '广告管理',
                'route' => '',
                'permission' => 'ad.index',
                'child' => [
                    [
                        'name' => 'admin-ad-location',
                        'display_name' => '明星产品管理',
                        'route' => 'backend.ad.location.index',
                        'permission' => 'goods.ad.location',
                    ],
//                    [
//                        'name' => 'admin-ad-bar',
//                        'display_name' => '通知栏管理',
//                        'route' => 'backend.popBar.index',
//                        'permission' => 'goods.ad.location',
//                    ],
//                    [
//                        'name' => 'admin-ad-pop',
//                        'display_name' => '弹窗管理',
//                        'route' => 'backend.popBar.index',
//                        'permission' => 'goods.ad.location',
//                    ]
                ],
            ],
            [
                'name' => 'admin-search',
                'display_name' => '搜索管理',
                'route' => '',
                'permission' => 'search.index',
                'child' => [
                    [
                        'name' => 'admin-blacklist',
                        'display_name' => '黑名单管理',
                        'route' => 'backend.goods.search.blacklist',
                        'permission' => 'search.blacklist.index',
                    ],
//                    [
//                        'name' => 'admin-redirect',
//                        'display_name' => '关键字跳转管理',
//                        'route' => 'backend.goods.search.redirectlist',
//                        'permission' => 'search.redirect.index',
//                    ],
                    [
                        'name' => 'admin-synonymlist',
                        'display_name' => '同义词管理',
                        'route' => 'backend.goods.search.synonymlist',
                        'permission' => 'search.synonymlist.index',
                    ],
                ],
            ],
            [
                'name' => 'admin-category',
                'display_name' => '会员',
                'route' => '',
                'permission' => 'member.index',
                'child' => [
                    [
                        'name' => 'admin-product',
                        'display_name' => '所有会员',
                        'route' => 'backend.member.index',
                        'permission' => 'member.all.index',
                    ],
//                    [
//                        'name' => 'admin-product',
//                        'display_name' => '合并账号',
//                        'route' => 'backend.member.merge',
//                        'permission' => 'member.merge.index',
//                    ],
//                    [
//                        'name' => 'admin-fission',
//                        'display_name' => '会员裂变',
//                        'route' => 'backend.fission',
//                        'permission' => 'member.index',
//                    ],
            
                ],
            ],

            [
                'name' => 'admin-promotion',
                'display_name' => '促销管理',
                'route' => '',
                'permission' => 'promotion.index',
                'child' => [
                    [
                        'name' => 'admin-promotion-cart',
                        'display_name' => '促销规则',
                        'route' => 'backend.promotion.cart',
                        'permission' => 'promotion.cart.index',
                        'child' => [],
                    ],
                    [
                        'name' => 'admin-promotion-log',
                        'display_name' => '操作记录',
                        'route' => 'backend.promotion.log',
                        'permission' => 'promotion.log',
                        'child' => [],
                    ],
//					[
//                        'name' => 'admin-promotion-point',
//                        'display_name' => '积分兑换',
//                        'route' => 'backend.point.index',
//                        'permission' => 'promotion.point',
//                        'child' => [],
//                    ],
                    [
                        'name' => 'admin-promotion-trial',
                        'display_name' => '付邮试用',
                        'route' => 'backend.trial.index',
                        'permission' => 'promotion.index',
                        'child' => [],
                    ],
                    [
                        'name' => 'admin-promotion-shipfee',
                        'display_name' => '邮费管理',
                        'route' => 'backend.shipfee.index',
                        'permission' => 'promotion.index',
                        'child' => [],
                    ],
                ],
            ],
            [
                'name' => 'admin-config',
                'display_name' => '页面管理',
                'route' => '',
                'permission' => 'cms.index',
                'child' => [
//                    [
//                        'name' => 'admin-page',
//                        'display_name' => '草稿管理',
//                        'route' => 'backend.page.index',
//                        'permission' => 'cms.draft.index',
//                        'child' => [],
//                    ],
                    [
                        'name' => 'admin-page-published',
                        'display_name' => '发布管理',
                        'route' => 'backend.page.published',
                        'permission' => 'cms.published.index',
                        'child' => [],
                    ],
//                    [
//                        'name' => 'admin-page-navication',
//                        'display_name' => '导航管理',
//                        'route' => 'backend.page.navication',
//                        'permission' => 'cms.navication.index',
//                        'child' => [],
//                    ],
//                    [
//                        'name' => 'admin-page-navication',
//                        'display_name' => '副导航',
//                        'route' => 'backend.page.navication1',
//                        'permission' => 'cms.navication1.index',
//                        'child' => [],
//                    ],
                ],
            ],

//            [
//                'name' => 'admin-report',
//                'display_name' => '报告',
//                'route' => '',
//                 'permission' => '',
//                'child' => [
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '操作日志',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '基础跟踪',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '页面分析',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '事件追踪 ',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '分享跟踪',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '来源跟踪',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '搜索关键字',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                    [
//                        'name' => 'admin-report-log',
//                        'display_name' => '商品数据',
//                        'route' => 'backend.report.log',
//                         'permission' => '',
//                    ],
//                ],
//            ],

//            [
//                'name' => 'Statistics-report',
//                'display_name' => '小程序数据统计',
//                'route' => '',
//                'permission' => 'miniapp.report.index',
//                'child' => [
//
//                    [
//                        'name' => 'Statistics-summary-index',
//                        'display_name' => '访客趋势数据',
//                        'route' => 'dsa.summary.index',
//                        'permission' => 'miniapp.report.summary',
//                    ],
//
//
//                    [
//                        'name' => 'Statistics-visit-trend',
//                        'display_name' => '访客数据明细',
//                        'route' => 'dsa.visit.trend.index',
//                        'permission' => 'miniapp.report.trend',
//                    ],
//
//                    [
//                        'name' => 'Statistics-retain-retain',
//                        'display_name' => '访客留存情况',
//                        'route' => 'dsa.retain.index',
//                        'permission' => 'miniapp.report.retain',
//                    ],
//
//                    [
//                        'name' => 'visit-user-portrait',
//                        'display_name' => '画像分布数据',
//                        'route' => 'dsa.user.portrait',
//                        'permission' => 'miniapp.report.portrait',
//                    ],
//
//                    [
//                        'name' => 'dsa-visit-distribution',
//                        'display_name' => '流量来源分布',
//                        'route' => 'dsa.visit.distribution',
//                        'permission' => 'miniapp.report.distribution',
//                    ],
//
//                    [
//                        'name' => 'dsa-page-stat',
//                        'display_name' => '页面访问情况',
//                        'route' => 'dsa.page.stat',
//                        'permission' => 'miniapp.report.stat',
//                    ],
//
//                ],
//            ],

            [
                'name' => 'Order-Management',
                'display_name' => 'OMS订单管理',
                'route' => '',
                'permission' => 'oms.index',
                'child' => [

                    [
                        'name' => 'Order-index',
                        'display_name' => 'OMS订单管理',
                        'route' => 'backend.oms.order',
                        'permission' => 'oms.order.index',
                    ],

                ],
            ],

//            [
//                'name' => 'Statistics-prodstat',
//                'display_name' => '商品相关数据统计',
//                'route' => '',
//                'permission' => 'miniapp.prodstat.index',
//                'child' => [
//
//                    [
//                        'name' => 'Statistics-prodstat-view',
//                        'display_name' => '商品访问次数',
//                        'route' => 'dsb.prodstat.view',
//                        'permission' => 'miniapp.prodstat.view',
//                    ],
//
//
//                    [
//                        'name' => 'Statistics-prodstat-share',
//                        'display_name' => '商品分享次数',
//                        'route' => 'dsb.prodstat.share',
//                        'permission' => 'miniapp.prodstat.share',
//                    ],
//
//                    [
//                        'name' => 'Statistics-prodstat-favorite',
//                        'display_name' => '商品收藏次数',
//                        'route' => 'dsb.prodstat.favorite',
//                        'permission' => 'miniapp.prodstat.favorite',
//                    ],
//
//                    [
//                        'name' => 'visit-user-portrait',
//                        'display_name' => '商品加购次数',
//                        'route' => 'dsb.prodstat.addcart',
//                        'permission' => 'miniapp.prodstat.addcart',
//                    ],
//
//                    [
//                        'name' => 'visit-user-portrait',
//                        'display_name' => '商品类别访问次数',
//                        'route' => 'dsb.prodstat.prodtypeview',
//                        'permission' => 'miniapp.prodstat.prodtypeview',
//                    ],
//
//
//                    [
//                        'name' => 'dsa-visit-distribution',
//                        'display_name' => '关键词搜索次数',
//                        'route' => 'dsb.prodstat.keyword',
//                        'permission' => 'miniapp.prodstat.keyword',
//                    ],
//
//                    [
//                        'name' => 'dsb-page-order',
//                        'display_name' => '订单趋势',
//                        'route' => 'dsb.prodstat.order',
//                        'permission' => 'miniapp.prodstat.order',
//                    ],
//                    [
//                        'name' => 'dsb-page-conversionRate',
//                        'display_name' => '转化率',
//                        'route' => 'dsb.prodstat.conversionRate',
//                        'permission' => 'miniapp.prodstat.conversionRate',
//                    ],
//
//                ],
//            ],


            [
                'name' => 'admin-system',
                'display_name' => '系统管理',
                'route' => '',
                'permission' => 'system.index',
                'child' => [
                    [
                        'name' => 'admin-user',
                        'display_name' => '用户管理',
                        'route' => 'backend.user',
                        'permission' => 'system.user.index',
                        'child' => [],
                    ],
                    [
                        'name' => 'admin-role',
                        'display_name' => '角色管理',
                        'route' => 'backend.role',
                        'permission' => 'system.role.index',
                        'child' => [],
                    ],
                    [
                        'name' => 'admin-permission',
                        'display_name' => '权限管理',
                        'route' => 'backend.permission',
                        'permission' => 'system.auth.index',
                        'child' => [],
                    ],
//                     [
//                         'name' => 'config-oss',
//                         'display_name' => '系统参数',
//                         'route' => 'backend.config.oss',
//                         'permission' => '',
//                         'child' => [],
//                     ],
                ],
            ],
            [
                'name' => 'admin-option',
                'display_name' => '系统配置',
                'route' => '',
                'permission' => 'config.index',
                'child' => [
//                    [
//                        'name' => 'admin-option-coupon',
//                        'display_name' => '首次购物优惠券配置',
//                        'route' => 'backend.config.coupon',
//                        'permission' => 'config.coupon.index',
//                        'child' => [],
//                    ],
                    [
                        'name' => 'admin-option-recommend',
                        'display_name' => '为您推荐',
                        'route' => 'backend.config.recommend',
                        'permission' => 'config.recommend.index',
                        'child' => [],
                    ],
//                    [
//                        'name' => 'admin-product-import',
//                        'display_name' => '商品图片导入',
//                        'route' => 'backend.product.import',
//                        'permission' => 'config.product.import',
//                        'child' => [],
//                    ],
                    [
                        'name' => 'admin-config-cache',
                        'display_name' => '缓存管理',
                        'route' => 'backend.config.cache',
                        'permission' => 'goods.sku.index',
                    ],
                ],
            ],
        ];
    }
}
