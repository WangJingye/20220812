<?php

return [
    /*
    Inner Api中接口和请求的外部接口的对应关系
    */
    'promotion/cart/applyNew' => 'promotion/cart/applyNew',
    'outward/update/batchStock' => 'outward/update/batchStock',//库存
    'showAddress' => 'showAddress',//配送地址
    'user/getMemberInfo' => 'user/getMemberInfo',
    'oms/orderInput' => 'oms/orderInput',
    'pay/success' => 'pay/success',
    'pay/update' => 'pay/update',
    'pay/updateAndSuccess' => 'pay/updateAndSuccess',
    'orders/details' => 'orders/details',
    'member/addCoupon' => 'member/addCoupon',
    'outward/product/getProductInfoBySkuIds' => 'outward/product/getProductInfoBySkuIds',

    'pointmall/my/convert' => 'pointmall/my/convert',
    'message/refund' => 'oms/refundMessage',
    'message/paid' => 'oms/paidMessage',
    #储值卡
    'getBalanceInfo' => 'user/getBalanceInfo',
    'getBalance' => 'user/getBalance',
    'addBalance' => 'user/addBalance',
    'setBalanceInvoice' => 'user/setBalanceInvoice',
    'refundBalanceCard' => 'user/refundBalanceCard',

    'getGoldInfo' => 'gold/detail',
    'getUserInfoByOpenid' => 'member/getUserInfoByOpenid',
    'getUserInfoByUserId' => 'member/getUserInfoByUserId',
];

