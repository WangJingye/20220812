<?php
const PROMOTION = 'http://promotion.el.org/';
const ORDER = 'http://order.el.org/';
const GOODS = 'http://goods.el.org/';
const MEMBER = 'http://member.el.org/';
const OMS = 'http://oms.el.org/';

return [
    'map' => [
        'promotion/cart/productList' => PROMOTION.'promotion/coupon/allList',
        'outward/product/getProductInfoBySkuIds' => GOODS.'outward/product/getProductInfoBySkuIds',
        'outward/update/batchStock' => GOODS.'outward/update/batchStock',
        'orders/details' => OMS.'orders/details',
        'delCoupon' => MEMBER.'delCoupon',
        'apiGrantCoupon' => MEMBER. 'apiGrantCoupon',
        'apiGetUserCouponInfo' => MEMBER. 'apiGetUserCouponInfo',
        'getOrderStatusCount' => OMS. 'orders/statusCount',
        'orderMerge' => OMS . 'inner/order/merge',
        'orderPosIdUpdate' => OMS . 'inner/order/posIdUpdate',
        //coupon
        'getPromotionDetail' => PROMOTION. 'promotion/cart/productDetail',
    ],
];
