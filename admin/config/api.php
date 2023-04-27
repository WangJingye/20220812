<?php

const PROMOTION = 'http://promotion.el.org/';
const ORDER = 'http://order.el.org/';
const GOODS = 'http://goods.el.org/';
const MEMBER = 'http://member.el.org/';
const STORE = 'http://store.el.org/';
const OMS = 'http://oms.el.org/';

return [
    'map' => [

        'pointmall/dataList' => MEMBER . 'pointmall/dataList',
        'pointmall/get' => MEMBER . 'pointmall/get',
        'pointmall/post' => MEMBER . 'pointmall/post',

        'promotion/cart/dataList' => PROMOTION . '/promotion/cart/dataList',
        'promotion/cart/get' => PROMOTION . '/promotion/cart/get',
        'promotion/cart/post' => PROMOTION . '/promotion/cart/post',
        'promotion/cart/destroy' => PROMOTION . '/promotion/cart/destroy',
        'promotion/cart/export' => PROMOTION . '/promotion/cart/export',
        'promotion/coupon/dataList' => PROMOTION . '/promotion/coupon/dataList',
        'promotion/coupon/get' => PROMOTION . '/promotion/coupon/get',
        'promotion/coupon/post' => PROMOTION . '/promotion/coupon/post',
        'promotion/coupon/destroy' => PROMOTION . '/promotion/coupon/destroy',
        'promotion/coupon/activeList' => PROMOTION . '/promotion/coupon/activeList',
        'promotion/cart/getCrossRules' => PROMOTION . '/promotion/cart/getCrossRules',
        'promotion/cart/active' => PROMOTION . '/promotion/cart/active',
        'promotion/cart/unactive' => PROMOTION . '/promotion/cart/unactive',
        'promotion/cart/messActive' => PROMOTION . '/promotion/cart/messActive',
        'promotion/cart/messUnactive' => PROMOTION . '/promotion/cart/messUnactive',
        'promotion/gift' => PROMOTION . '/promotion/gift',
        'promotion/gift/dataList' => PROMOTION . '/promotion/gift/dataList',
        'promotion/gift/edit' => PROMOTION . '/promotion/gift/edit',
        'promotion/gift/post' => PROMOTION . '/promotion/gift/post',
        'promotion/gift/get' => PROMOTION . '/promotion/gift/get',
        'promotion/gift/active' => PROMOTION . '/promotion/gift/active',
        'promotion/gift/unactive' => PROMOTION . '/promotion/gift/unactive',
        'promotion/code' => PROMOTION . '/promotion/code',
        'promotion/code/dataList' => PROMOTION . '/promotion/code/dataList',
        'promotion/code/edit' => PROMOTION . '/promotion/code/edit',
        'promotion/code/post' => PROMOTION . '/promotion/code/post',
        'promotion/log/dataList' => PROMOTION . '/promotion/log/dataList',

        'sales/order/dataList' => ORDER . '/sales/order/dataList',
        'sales/order/get' => ORDER . '/sales/order/get',
        'sales/order/getExpressInfo' => ORDER . '/getExpressInfo',
        'order/getDiffInfo' => ORDER . '/getDiffInfo',
        'order/getOrderRefundStatus' => ORDER . '/order/getOrderRefundStatus',
        'order/refund' => OMS . '/order/refund',
        'order/orderStatic' => ORDER . 'getDailySaleData',
        'order/setOrderRate' => ORDER . 'setOrderRate',
        //会员裂变
        'member/fission/dataList' => MEMBER . 'fission/dataList',
        'member/fission/add' => MEMBER . 'fission/add',
        'member/fission/detail' => MEMBER . 'fission/detail',
        'member/fission/log' => MEMBER . 'fission/getFissionRank',
        'member/fission/active' => MEMBER . 'fission/active',
        //付邮试用
        'order/trial/dataList' => ORDER . 'api/trial/dataList',
        'order/trial/add' => ORDER . 'api/trial/add',
        'order/trial/detail' => ORDER . 'api/trial/detail',
        'order/trial/update' => ORDER . 'api/trial/update',
        'order/trial/active' => ORDER . 'api/trial/active',
        //邮费管理
        'shipfee/list' => PROMOTION . 'shipfee/list',
        'shipfee/detail' => PROMOTION . 'shipfee/detail',
        'shipfee/update' => PROMOTION . 'shipfee/update',
        'shipfee/del' => PROMOTION . 'shipfee/del',

        //'order/trial/log' => ORDER .'api/trial/getFissionRank',
        'inner/getAllProvince' => OMS . 'inner/getAllProvince',
        'inner/arrivalReminder' => OMS . 'inner/arrivalReminder',

        //商品模块
        'outward/product/getProduct' => GOODS . '/outward/product/getProduct',
        'goods/category/list' => GOODS . '/goods/category/list',
        'goods/category/search' => GOODS . '/goods/category/search',
        'goods/category/getCate' => GOODS . '/goods/category/getCate',
        'goods/category/addCate' => GOODS . '/goods/category/addCate',
        'goods/category/editCate' => GOODS . '/goods/category/editCate',
        'goods/category/addProducts' => GOODS . '/goods/category/addProducts',
        'goods/category/delProduct' => GOODS . '/goods/category/delProduct',
        'goods/category/handleCatSortCsv' => GOODS . '/goods/category/handleCatSortCsv',
        'goods/category/batchChangeSort' => GOODS . '/goods/category/batchChangeSort',

        'goods/category/relateProds' => GOODS . '/goods/category/relateProds',
        'goods/category/editRelateProds' => GOODS . '/goods/category/editRelateProds',
        'goods/category/pCateList' => GOODS . '/goods/category/pCateList',
        'goods/category/pCateListNoSub' => GOODS . '/goods/category/pCateListNoSub',
        'goods/category/calculateProds' => GOODS . '/goods/category/calculateProds',
        'goods/category/offCat' => GOODS . '/goods/category/offCat',
        'goods/category/upCat' => GOODS . '/goods/category/upCat',

        //广告
        'ad/loc/list' => GOODS . '/ad/loc/list',
        'ad/loc/get' => GOODS . '/ad/loc/get',
        'ad/loc/insert' => GOODS . '/ad/loc/insert',
        'ad/loc/update' => GOODS . '/ad/loc/update',

        'ad/item/list' => GOODS . '/ad/item/list',
        'ad/item/get' => GOODS . '/ad/item/get',
        'ad/item/insert' => GOODS . '/ad/item/insert',
        'ad/item/update' => GOODS . '/ad/item/update',
        'ad/item/delete' => GOODS . '/ad/item/delete',

        'goods/spu/getProdOrCollList' => GOODS . 'goods/spu/getProdOrCollList',
        'goods/spu/list' => GOODS . '/goods/spu/list',
        'goods/spu/add' => GOODS . '/goods/spu/add',
        'goods/spu/changeStatus' => GOODS . '/goods/spu/changeStatus',
        'goods/spu/backList' => GOODS . '/goods/spu/backList',
        'goods/spu/editProd' => GOODS . '/goods/spu/editProd',
        'goods/spu/getProd' => GOODS . '/goods/spu/getProd',
        'goods/spu/relateSkus' => GOODS . '/goods/spu/relateSkus',
        'goods/spu/relateDoorSkus' => GOODS . '/goods/spu/relateDoorSkus',
        'goods/spu/editRelateSkus' => GOODS . '/goods/spu/editRelateSkus',
        'goods/spu/changeDisplay' => GOODS . '/goods/spu/changeDisplay',
        'goods/spu/rawData' => GOODS . '/goods/spu/rawData',
        'goods/spu/createCharme' => GOODS . '/goods/spu/createCharme',
        'goods/spu/checkSpec' => GOODS . 'goods/spu/checkSpec',
        'goods/spu/getCatProdAndColleList' => GOODS . 'goods/spu/getCatProdAndColleList',
        'goods/spu/updateCatRelation' => GOODS . 'goods/spu/updateCatRelation',
        'goods/spu/getProdAndCollList' => GOODS . 'goods/spu/getProdAndCollList',
        'goods/spu/handleCsv' => GOODS . 'goods/spu/handleCsv',

        #搜索相关
//        'goods/search/addBlackList' => GOODS . 'search/addblacklist',
//        'goods/search/delBlackList' => GOODS . 'search/delblacklist',
//        'goods/search/blacklist' => GOODS . 'search/blacklist',

        //搜索相关
        'goods/search/addBlackList' => GOODS . '/search/addblackList',
        'goods/search/delBlackList' => GOODS . '/search/delblackList',
        'goods/search/blacklist' => GOODS . '/search/blacklist',

        'goods/search/addRedirect' => GOODS . '/search/addRedirect',
        'goods/search/updateRedirect' => GOODS . '/search/updateRedirect',
        'goods/search/delRedirect' => GOODS . '/search/delRedirect',
        'goods/search/redirectList' => GOODS . '/search/redirectList',
        'goods/search/getRedirectInfo' => GOODS . '/search/getRedirectInfo',

        'goods/search/addSynonym' => GOODS . '/search/addSynonym',
        'goods/search/updateSynonym' => GOODS . '/search/updateSynonym',
        'goods/search/delSynonym' => GOODS . '/search/delSynonym',
        'goods/search/synonymList' => GOODS . '/search/synonymList',
        'goods/search/getSynonymInfo' => GOODS . '/search/getSynonymInfo',
        'goods/search/getAllSynonym' => GOODS . '/search/getAllSynonym',


        'goods/collection/list' => GOODS . '/goods/collection/list',
        'goods/collection/get' => GOODS . '/goods/collection/detail',
        'goods/collection/update' => GOODS . '/goods/collection/update',
        'goods/collection/changeStatus' => GOODS . '/goods/collection/changeStatus',
        'goods/collection/insert' => GOODS . '/goods/collection/add',
        'goods/collection/getFormatedProductList' => GOODS . '/goods/collection/getFormatedProductList',

        'goods/recommend/list' => GOODS . '/goods/recommend/list',
        'goods/recommend/add' => GOODS . '/goods/recommend/add',
        'goods/recommend/changeStatus' => GOODS . '/goods/recommend/changeStatus',

        'goods/sku/list' => GOODS . '/goods/sku/list',
        'goods/sku/add' => GOODS . '/goods/sku/add',
        'goods/sku/editSku' => GOODS . '/goods/sku/editSku',
        'goods/sku/getSku' => GOODS . '/goods/sku/getSku',
        'goods/sku/stock' => GOODS . '/goods/sku/stock',
        'goods/sku/data' => GOODS . '/goods/sku/all',
        'goods/sku/infoAll' => GOODS . 'goods/sku/infoAll',
        'goods/sku/updateStock' => GOODS . '/goods/sku/updateStock',
        'outward/update/batchStock' => GOODS . '/outward/update/batchStock',
        'outward/update/batchStockForce' => GOODS . '/outward/update/batchStockForce',
        'goods/product/getCategoryTree' => GOODS . 'goods/product/getCategoryTree',
        'goods/common/getProdTypelist' => GOODS . 'goods/common/getProdTypelist',
        'goods/common/getBrandColl' => GOODS . 'goods/common/getBrandColl',
        'goods/common/getUsage' => GOODS . 'goods/common/getUsage',
        'goods/spp/list' => GOODS . 'goods/spp/list',
        'goods/spp/add' => GOODS . 'goods/spp/add',
        'goods/spp/edit' => GOODS . 'goods/spp/edit',
        'goods/spp/del' => GOODS . 'goods/spp/del',
        'goods/spp/getSppRule' => GOODS . 'goods/spp/getSppRule',
        'goods/doorSku/list' => GOODS . '/goods/doorSku/list',
        'goods/doorSku/editSku' => GOODS . '/goods/doorSku/editSku',
        'goods/doorSku/getSku' => GOODS . '/goods/doorSku/getSku',

        'goods/channel/updatesecure' => GOODS . 'goods/channel/updatesecure',
        'goods/channel/update' => GOODS . 'goods/channel/update',
        'goods/stock/log' => GOODS . 'goods/stock/log',
        'goods/spu/saveDetail' => GOODS . 'goods/spu/saveDetail',
        'goods/spu/getDetail' => GOODS . 'goods/spu/getDetail',
        'goods/collection/saveDetail' => GOODS . 'goods/collection/saveDetail',
        'goods/collection/getDetail' => GOODS . 'goods/collection/getDetail',
        'goods/sku/saveDetail' => GOODS . 'goods/sku/saveDetail',
        'goods/sku/getDetail' => GOODS . 'goods/sku/getDetail',
        'outward/product/getProductInfoBySkuIds' => GOODS . '/outward/product/getProductInfoBySkuIds',
        //商品初始化导入
        'goods/data/import' => GOODS . 'goods/data/import',
        'goods/cache/clear' => GOODS . 'goods/cache/clear',

        'category/tree' => PROMOTION . '/category',
        'product' => PROMOTION . '/product',
//        'getCategoryBySku' => 'http://promotion.rcss.org/api/getCategoryBySku',
        'getPriceBySku' => PROMOTION . '/getPriceBySku',

        // 会员列表
        'member/list' => MEMBER . '/member/list',
        'member/excel/list' => MEMBER . '/member/excel/list',
        'member/detail' => MEMBER . '/member/detail',
        'member/destroy' => MEMBER . '/member/destroy',
        'member/importCoupon' => MEMBER . '/member/importCoupon',
        'member/mergeSlaveMemberIntoMasterMember' => MEMBER . '/member/mergeSlaveMemberIntoMasterMember',
        'member/getSlaveAndMasterMember' => MEMBER . '/member/getSlaveAndMasterMember',
        'member/innerGetUserAddress' => MEMBER . '/user/innerGetUserAddress',
        'member/getMemberInfo' => MEMBER . '/user/getMemberInfo',
        'member/exportMember' => MEMBER . 'member/exportMember',

        'promotion/cart/type' => PROMOTION . '/promotion/cart/type',


        'store/list' => MEMBER . '/store/list',
        'store/AllList' => MEMBER . '/store/AllList',
        'store/get' => MEMBER . '/store/get',
        'store/update' => MEMBER . '/store/update',
        // 雇员列表
        'employee/list' => MEMBER . '/employee/list',
        'employee/allList' => MEMBER . '/employee/AllList',
        'employee/get' => MEMBER . '/employee/get',
        'employee/update' => MEMBER . '/employee/update',
        'employee/bindAll' => MEMBER . '/employee/bindAll',
        // 雇员列表
        'role/list' => MEMBER . '/role/list',
        'role/get' => MEMBER . '/role/get',
        'role/update' => MEMBER . '/role/update',
        'role/AllList' => MEMBER . '/role/AllList',

        // 订单列表
        'order/index' => OMS . '/order/list',
        'order/get' => OMS . '/order/get',
        'order/update' => OMS . '/order/update',
        'orderItem/list' => OMS . '/orderItem/list',
        'order/status/update' => OMS . '/order/status/update',
        'order/makeAfterSale' => OMS . '/order/makeAfterSale',
        'order/free' => OMS . '/order/free',
        'order/batch/delivery' => OMS . 'order/batch/delivery',
        'order/add' => OMS . 'order/add',
        'order/guide/list' => OMS . '/order/guide/list',
        'oms/order/export' => OMS . '/oms/order/export',
        //导购列表
        'store/allGuideInfoPage' => MEMBER . '/store/allGuideInfoPage',
        'store/getSearchData' => MEMBER . '/store/getSearchData',
        'store/guideInfoPage' => MEMBER . '/store/guideInfoPage',
        'store/realTimeGuideInfo' => MEMBER . '/guide/realTimeGuideInfo',
        'store/getStoreFromCity' => MEMBER . '/store/getStoreFromCity',
        'store/dashBoardNeedData' => MEMBER . '/store/dashBoardNeedData',

        'order/getOrderReportData' => OMS . '/getOrderReportData',
        //dashboard
        'member/dashboard' => MEMBER . '/dashboard',
        'oms/dashboard' => OMS . '/dashboard',
        'member/popInfo' => MEMBER . '/pop/info',
        'member/popAdd' => MEMBER . '/pop/add',
        'member/barInfo' => MEMBER . '/bar/Info',
        'member/barAdd' => MEMBER . '/bar/add',
        //开票列表
        'invice/list' => OMS . '/invice/list',
        //订单同步
        'inner/omsSync' => OMS . '/inner/omsSync',
        'oms/data/import' => OMS . 'oms/data/import',
        'inner/returnApplyStatusChange' => OMS . '/inner/returnApplyStatusChange',
        //优惠券cr
        'inner/couponListByPage' => MEMBER . '/coupon/inner/couponListByPage',
        'inner/couponSend' => MEMBER . '/coupon/inner/couponSend',

        //储值卡
        'goods/gold/list' => GOODS . 'gold/list',
        'goods/gold/changeStatus' => GOODS . 'gold/changeStatus',
        'goods/gold/delete' => GOODS . 'gold/delete',
        'goods/gold/add' => GOODS . 'gold/add',
        'order/goldOrder/list' => ORDER . 'api/goldOrder/list',
        'member/user/getBalanceLogs' => MEMBER . 'user/getBalanceLogs',
        'member/user/exportBalanceLogs' => MEMBER . 'user/exportBalanceLogs',
        'member/user/getUserBalanceList' => MEMBER . 'user/getUserBalanceList',
        'order/goldOrder/refund' => ORDER . 'api/goldOrder/refund',
        'member/user/invoice' => MEMBER . 'user/invoice',
        'member/balance/recharge' => MEMBER . 'balance/recharge',
        'oms/order/invoice' => OMS . 'order/invoice',
    ],

];
