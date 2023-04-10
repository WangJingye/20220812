<?php
$admin_domain = env('ADMIN_DOMAIN','http://admin.el.org/');
$goods_domain = env('GOODS_DOMAIN','http://goods.el.org/');
return [
    'map'=>[      
        //
        'admin/config/coupon'=>$admin_domain.'api/config/coupon',
        'goods/rule/category'=>$goods_domain.'goods/inner/setVirtualCate',
        'outward/product/getProductInfoBySkuIds'=>$goods_domain.'outward/product/getProductInfoBySkuIds',
    ]
];

