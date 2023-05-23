<?php

const PROMOTION = 'http://promotion.el.org/';
const ORDER = 'http://order.el.org/';
const GOODS = 'http://goods.el.org/';
const MEMBER = 'http://member.el.org/';
const STORE = 'http://store.el.org/';
const OMS = 'http://oms.el.org/';
return [
    'map' => [
        'update/batchStock' => GOODS . 'outward/update/batchStock',//dlc
        'batch/unLockStock' => GOODS . 'outward/batch/unLockStock',//支付成功后解锁库存

        'api/pay/refund' => ORDER . 'api/pay/refund',//退款

        'outward/product/getProductInfoBySkuIds' => GOODS . 'outward/product/getProductInfoBySkuIds',
        'store/dashBoardNeedData' => STORE . 'store/dashBoardNeedData',
        'pointmall/paysuccess' => MEMBER . 'pointmall/paysuccess',
        'pointmall/cancelOrder' => MEMBER . 'pointmall/cancelOrder',
        'useCoupon' => MEMBER . 'useCoupon',
        'revertCoupon' => MEMBER . 'revertCoupon',
        'api/pay/closeOrder' => ORDER . 'api/pay/closeOrder',
        'outward/product/getProductInfoBySkuIds' > GOODS . 'outward/product/getProductInfoBySkuIds',
        'goods/insertStock' => GOODS . 'goods/insertStock',
        'member/getUserInfo' => MEMBER . 'member/getUserInfo',

        'user/getByOpenId' => MEMBER . 'user/getByOpenId',
        'outward/update/batchPrice' => GOODS . 'outward/update/batchPrice',
        'outward/update/batchStockFull' => GOODS . 'outward/update/batchStockFull',
        'getPosIdByUid' => MEMBER . 'inner/user/getPosIdByUid',

        'shipFeeTryRevert' => ORDER . 'api/shipFeeTry/revert',
        'addSalesVolume' => GOODS . 'inner/addSalesVolume',

        'share/notify' => MEMBER . 'share/notify',
        'couponBack' => MEMBER . 'coupon/inner/couponBack',
        'couponUse' => MEMBER . 'coupon/inner/couponUse',
        'useBalance' => MEMBER . 'user/useBalance',
        'refundBalance' => MEMBER . 'user/refundBalance',
        'exportBalanceLog' => MEMBER . 'balance/exportBalanceLog',
        'getBalanceLogAll' => MEMBER . 'balance/getBalanceLogAll',
        'getBalanceLog' => MEMBER . 'balance/getBalanceLog',
    ]
];



