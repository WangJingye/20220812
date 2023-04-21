<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'namespace' => 'Api'
], function () {
    //OMS接口
    Route::post('oms/orderInput', 'Oms\InputOrderController@NewOrderFromCart');
    Route::any('demo', 'ApiExampleController@demo');
    Route::any('pos/makeOrderTxt', 'Pos\getDailyOrderInfoController@makeOrderInfoToText');
    Route::any('pos/makeReturnTxt', 'Pos\getDailyOrderInfoController@makeReturnInfoToText');
    Route::any('pos/makeMemberTxt', 'Pos\getDailyOrderInfoController@makeMemberInfoToText');
    Route::any('pos/makeReceivingTxt', 'Pos\getDailyOrderInfoController@makeReceivingInfoToText');
    Route::any('pos/makeAdjustmentTxt', 'Pos\getDailyOrderInfoController@makeAdjustmentInfoToText');
    Route::any('orders/details', 'Oms\GetOrderInfoController@getOrderInfoByOmsId');
    Route::get('oms/order/listLimit', 'Oms\GetOrderInfoController@getOrderListLimit');
    Route::post('oms/update/BatchStock', 'Oms\HandleSkuController@updateBatchStock');
    Route::post('pay/success', 'Oms\InputOrderController@paySuccessOrder');
    Route::post('pay/update', 'Oms\InputOrderController@payUpdateOrder');
    Route::post('pay/updateAndSuccess', 'Oms\InputOrderController@payUpdateAndSuccessOrder');
    Route::post('message/paid', 'SubscribeMessageController@paidMessage');
    Route::post('message/send', 'SubscribeMessageController@sendMessage');
    Route::post('message/pend', 'SubscribeMessageController@pendMessage');
    Route::any('getOrderReportData', 'Report\DataController@getOrderReportData');
    Route::any('dashboard', 'Dashboard\OrderController@getData');
    //电子发票
    Route::post('invoiceQuery', 'Invoice\InvoiceController@invoiceQuery');
    Route::post('sendInvoiceEmail', 'Invoice\InvoiceController@sendInvoiceEmail');
    Route::post('oms/exportOrderHistory', 'Oms\InputOrderController@exportOrderHistory');
    //导购订单查询(steven add 20201116)
    Route::any('orders/achieveList', 'Oms\GetOrderInfoController@getAchieveList');
    //支付成功上报
    Route::post('oms/paysuccessreport', 'Oms\InputOrderController@paySuccessReport');
    ###DLC##############################################################################
    Route::any('oms/address', 'Dlc\SalesController@getOmsAddress');
    //OMS接口
    Route::post('Service/Oms/{method?}', 'Dlc\OmsController@service');
    Route::post('inner/syncStock', 'Dlc\OmsController@syncStock');
    //个人中心接口 显示指定订单状态的数量
    Route::any('orders/statusCount', 'Dlc\SalesController@getStatusCount');
    //获取物流轨迹
    Route::any('order/getLogistics', 'Dlc\SalesController@getLogistics');
    //会员code合并(内部调用)
    Route::any('inner/order/merge', 'Dlc\SalesController@orderMerge');
    Route::any('inner/order/posIdUpdate', 'Dlc\SalesController@orderPosIdUpdate');
    //获取所有省份(内部调用)
    Route::any('inner/getAllProvince', 'Dlc\SalesController@getAllProvince');
    //前端支付成功后通知后端(用于支付回调慢导致订单状态未更变的标记)
    Route::any('notice/payPendingFlag', 'Dlc\SalesController@payPendingFlag');
    //到货通知订阅消息
    Route::any('product/arrivalReminder', 'Dlc\SalesController@goodsArrivalReminder');
    //订单同步(内部调用)
    Route::any('inner/omsSync', 'Dlc\SalesController@omsSync');
    Route::any('inner/returnApplyStatusChange', 'Dlc\SalesController@returnApplyStatusChange');
    //评价
    Route::any('comment/update', 'Dlc\SalesController@omsCommentUpdate');
    Route::any('comment/get', 'Dlc\SalesController@omsCommentGet');
    //退货申请
    Route::any('return/apply', 'Dlc\SalesController@omsReturnApplyRequest');
    //导入
    Route::any('oms/data/import', 'Dlc\DataController@import');
    Route::any('inner/arrivalReminder', 'Dlc\SalesController@arrivalReminder');

    Route::any('orders/getOrderInfo', 'Oms\GetOrderInfoController@getOrderInfo');
    Route::any('order/applyInvoice', 'Dlc\SalesController@applyInvoice');
    Route::any('order/cancelInvoice', 'Dlc\SalesController@cancelInvoice');
    Route::any('order/invoice', 'Dlc\SalesController@invoice');
});

Route::group([
    'namespace' => 'Api'
    , 'middleware' => 'self.api'
], function () {
    //OMS接口
    Route::any('orders/list', 'Oms\GetOrderInfoController@getOrderInfoList');
    Route::any('order/detail', 'Oms\GetOrderInfoController@getOrderInfoDetail');
    Route::any('add/invoice', 'Oms\InputOrderController@addInvoice');
    //DLC
    Route::any('orders/lists', 'Oms\GetOrderInfoController@getOrderInfoLists');

});
Route::group([
    'namespace' => 'Backend'
], function () {

    //OMS后台接口
    Route::any('order/list', 'Oms\OrdersController@list');
    Route::post('order/get', 'Oms\OrdersController@getOrder');
    Route::any('order/update', 'Oms\OrdersController@updateOrder');
    Route::any('orderItem/list', 'Oms\OrdersController@orderItemList');
    Route::post('order/status/update', 'Oms\OrdersController@updateOrderStatus');
    Route::post('order/makeAfterSale', 'Oms\AfterSalesOrdersController@createdAfterOrder');
    Route::post('order/refund', 'Oms\AfterSalesOrdersController@refund');
    Route::post('order/free', 'Oms\OrdersController@addOrderGift');
    Route::post('order/batch/delivery', 'Oms\OrdersController@batchDelivery');
    Route::post('order/add', 'Oms\OrdersController@addOrder');
    Route::any('order/guide/list', 'Oms\OrdersController@guideList');
    Route::post('oms/order/export', 'Oms\OrdersController@export');


});

Route::group([
//    'middleware' => ['log']
], function () {
//
    Route::any('router/rest', 'TopController@routerRest');

});

//银联退款成功回调
Route::group([
//    'middleware' => ['log']
    'namespace' => 'Api'
], function () {
    Route::any('pay/refundNotify', 'Oms\AfterSalesOrdersController@afterOrderPay');

});


Route::group([
//    'middleware' => ['log']
], function () {
//
    Route::any('itfront', 'AdController@itFront');

});
