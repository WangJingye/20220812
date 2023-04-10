<?php
const PROMOTION = 'http://promotion.el.org/';
const ORDER = 'http://order.el.org/';
const GOODS = 'http://goods.dlc.com.cn/';
const MEMBER = 'http://member.el.org/';

return [
    'map' => [
        'promotion/cart/productList' => PROMOTION.'promotion/cart/productList',
        'promotion/cart/productDetail' => PROMOTION.'promotion/cart/productDetail',
        'account/showRecently' => MEMBER.'showRecently',
        'footprint/getPagePids' => MEMBER.'footprint/getPagePids',
        'fav/getPagePids' => MEMBER.'fav/getPagePids',
        'promotion/getAllByAddSku' => PROMOTION.'promotion/coupon/getAllByAddSku',
        'getUserTypeByUid' => MEMBER.'user/getUserTypeByUid',
        'addSalesVolume' => GOODS.''
    ],
];
