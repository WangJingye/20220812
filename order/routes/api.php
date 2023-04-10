<?php
/** @var \Laravel\Lumen\Routing\Router $router */
//前端接口(e.g:api/demo)
$router->post('demo', 'ApiExampleController@demo');
//用户
$router->post('register', 'UserController@register');
$router->post('login', 'UserController@login');
$router->post('logout', 'UserController@logout');
$router->post('getProfile', 'UserController@getProfile');
$router->post('updateProfile', 'UserController@updateProfile');
$router->post('forgot', 'UserController@forgot');
$router->post('changePassword', 'UserController@changePassword');
$router->post('sendSMS', 'UserController@sendSMS');
$router->get('captcha', 'UserController@captcha');
$router->post('saveAddress', 'AddressController@saveAddress');
$router->post('delAddress', 'AddressController@delAddress');
$router->post('addressList', 'AddressController@addressList');
$router->post('setDefAddress', 'AddressController@setDefAddress');
//联合登录
$router->post('sina/login', 'UserController@sinaLogin');
//商品
$router->post('product/getDetail', 'Product\ProductController@getDetail');
$router->post('product/getListByNavId', 'Product\ProductController@getListByNavId');
$router->post('product/importProduct', 'Product\ImportController@import');
$router->post('product/search', 'Product\SearchController@search');
$router->post('product/suggestion', 'Product\SearchController@suggestion');
$router->post('product/recommendHome', 'Product\RecommendController@homepage');
$router->post('product/recommend', 'Product\RecommendController@normal');
$router->post('product/viewed', 'Product\RecommendController@latestView');
$router->post('product/notify', 'Product\NotifyController@saveNotifyRequest');
$router->post('product/getNavList', 'Product\CategoryController@list');
$router->post('innerProduct/getSkus', 'Product\InnerProductController@getSkus');
$router->post('innerProduct/saveSales', 'Product\InnerProductController@saveSales');
$router->post('innerProduct/cancelOrderUpdateStock', 'Product\InnerProductController@cancelOrderUpdateStock');
$router->post('innerProduct/placeOrderUpdateStock', 'Product\InnerProductController@placeOrderUpdateStock');
$router->post('innerProduct/paidOrderUpdateStock', 'Product\InnerProductController@paidOrderUpdateStock');
$router->post('innerProduct/shipmentOrderUpdateStock', 'Product\InnerProductController@shipmentOrderUpdateStock');
//订单
$router->group(['middleware' => ['self.api']], function ($router) {

});
$router->post('orderDetail', 'OrderController@orderDetail');
$router->post('orderList', 'OrderController@orderList');
$router->post('cancelOrder', 'OrderController@cancelOrder');
$router->post('confirm', 'CheckoutController@confirm');
$router->post('createOrder', 'CheckoutController@createOrder');
$router->post('trial/confirm', 'ShipfeeTryController@confirm');
$router->post('trial/createOrder', 'ShipfeeTryController@createOrder');
$router->post('shipFeeTry/revert', 'ShipfeeTryController@revert');
$router->post('updateCheckoutOptions', 'CheckoutController@updateCheckoutOptions');
$router->post('orderPay', 'CheckoutController@orderPay');

//购物车(需要登录)
$router->post('getCartInfo', 'CartController@getCartInfo');
$router->post('delCart', 'CartController@delCart');
$router->post('addToCart', 'CartController@addToCart');
$router->post('updateOption', 'CartController@updateOption');
$router->post('updateSelect', 'CartController@updateSelect');
$router->post('cartCheckStock', 'CartController@checkStock');
//CMS
$router->post('getHomeList', 'CmsController@getHomeList');
//其他(magento调用)
$router->post('orderShip', 'OrderController@orderShip');
$router->post('orderReturnAllow', 'OrderController@orderReturnAllow');
$router->post('orderRefunded', 'OrderController@orderRefunded');
//支付
$router->post('pay/index','PayController@pay');
$router->post('pay/refund','PayController@refund');
$router->post('pay/query','PayController@query');
$router->post('pay/getAccess','PayController@getAccess');
$router->post('pay/closeOrder','PayController@closeOrder');
$router->post('pay/createOrderNo','PayController@createOrderNo');
$router->post('pay/GaQuery','PayController@GaQuery');


//支付回调
$router->post('pay/notify','PayController@WxNotify');
$router->post('pay/webNotify','PayController@webNotify');
$router->post('pay/NativeNotify','PayController@NativeNotify');
$router->get('pay/wechatNotify','PayController@wechatNotify');
$router->get('pay/minAppNotify','PayController@minAppNotify');

//支付宝支付回调
$router->post('pay/AliNotify','PayController@AliNotify');
//chinaPay支付回调
$router->post('pay/ChinaNotify','PayController@ChinaNotify');

//选择花呗 获取分期金额
$router->post('pay/getStagesInfo','PayController@getStagesInfo');
//更新订单状态
$router->post('pay/updatePendingOrder','PayController@updatePendingOrder');

//付邮试用
$router->post('trial/list','TrialController@list');
$router->post('trial/goodsList','TrialController@goodsList');
$router->post('trial/checkOut','TrialController@goodsList');
$router->post('trial/edit','TrialController@edit');
$router->post('trial/add','TrialController@add');
$router->post('trial/dataList','TrialController@dataList');
$router->post('trial/detail','TrialController@detail');
$router->post('trial/active','TrialController@active');





