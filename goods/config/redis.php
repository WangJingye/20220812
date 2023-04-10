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
    // 库存表
    'store' => 'store',
    // cate filter表
    'cateFilter' => 'cate_filter',
    // cate prod 关联表
    'mappingCateProd' => 'mapping_cate_prod',
    // virtual cate prod 关联表
    'vmappingCateProd' => 'v_mapping_cate_prod',
    // prod cate 关联表
    'mappingProdCate' => 'mapping_prod_cate',
    // prod 上下架状态表
    'prodDisplay' => 'prod_display',
    // prod 上架时间表
    'prodDisplayDate' => 'prod_display_date',
    // prod 上架时间表
    'hProdDisplayDate' => 'h_prod_display_date',
    // prod 销量表
    'prodSales' => 'prod_sales',
    // prod 销量表
    'prodSalesByUsage' => 'prod_sales_by_usage',
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
    // prod sku 关联表
    'mappingProdSku' => 'mapping_prod_sku',
    // sku prod 关联表
    'mappingSkuProd' => 'mapping_sku_prod',
    // prod doors sku 关联表
    'mappingProdDSku' => 'mapping_prod_d_sku',
    // sku 上下架状态表
    'skuDisplay' => 'sku_display',
    // sku inventory 关联表
    'mappingSkuInventory' => 'mapping_sku_inventory',
    // inventory sku 关联表
    'mappingInventorySku' => 'mapping_inventory_sku',
    // inventory store 关联表
    'mappingInventoryStore' => 'mapping_inventory_store',
    // sku inventory 关联表
    'inventory' => 'inventory',
    // cate level info 表
    'cateLevelInfo' => 'cate_level_info',
    // prod level info 表
    'prodLevelInfo' => 'product_level_info',
    // sku list 表
    'skuList' => 'sku_list',
    // prod list 表
    'prodList' => 'product_list',
    // cate list 表
    'cateList' => 'category_list',
    // prod img info 表
    'prodCommonImgsInfo' => 'product_common_imgs_info',
    // sku level info 表
    'skuLevelInfo' => 'sku_level_info',
    // door sku level info 表
    'dSkuLevelInfo' => 'd_sku_level_info',
    // inventory level info 表
    'inventoryLevelInfo' => 'inventory_level_info',
    // golden price 表
    'goldenPriceInfo' => 'gold-price-today',
    // goods golden price 表
    'goodsGoldenPriceInfo' => 'goods-gold-price-today',
    // 系统配置表
    'sysConfig' => 'system_config',
    // oms未匹配sku重试
    'omsMsgList' => 'oms_msg_list',
    // 门店商品列表
    'doorsProdList' => 'doors_product_list',
];
