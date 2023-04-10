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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'namespace' => 'Api'
], function () {
       
    //后台接口    
    Route::any('promotion/cart/dataList','Promotion\CartController@dataList');
    Route::any('promotion/cart/get','Promotion\CartController@get');
    Route::any('promotion/cart/post','Promotion\CartController@post');
    Route::any('promotion/cart/destroy','Promotion\CartController@destroy');
    Route::any('promotion/cart/export','Promotion\CartController@export');
    
    Route::any('promotion/coupon/dataList','Promotion\CouponController@dataList');
    Route::any('promotion/coupon/get','Promotion\CouponController@get');
    Route::any('promotion/coupon/post','Promotion\CouponController@post');
    Route::any('promotion/coupon/destroy','Promotion\CouponController@destroy');
    Route::any('promotion/coupon/activeList','Promotion\CouponController@activeList');
    //gift
    Route::any('promotion/gift/dataList','Promotion\GiftController@dataList');
    Route::any('promotion/gift/get','Promotion\GiftController@get');
    Route::any('promotion/gift/post','Promotion\GiftController@post');
    Route::any('promotion/gift/active','Promotion\GiftController@active');
    Route::any('promotion/gift/unactive','Promotion\GiftController@unactive');
    //log
    Route::any('promotion/log/dataList','Promotion\LogController@dataList');
    //code
    Route::any('promotion/code/dataList','Promotion\CodeController@dataList');
    Route::any('promotion/code/get','Promotion\CodeController@get');
    Route::any('promotion/code/post','Promotion\CodeController@post');
    
    //依赖接口
    
    //促销接口
    Route::any('promotion/cart/applyNew','Promotion\CartController@applyNew');
    Route::any('promotion/cart/getCrossRules','Promotion\CartController@getCrossRules');
    Route::any('promotion/cart/active','Promotion\CartController@active');
    Route::any('promotion/cart/unactive','Promotion\CartController@unactive');
    Route::any('promotion/cart/messActive','Promotion\CartController@messActive');
    Route::any('promotion/cart/messUnactive','Promotion\CartController@messUnactive');

    //外部接口
    Route::any('promotion/coupon/allList','Promotion\CouponController@allList');
    Route::any('promotion/coupon/list','Promotion\CouponController@list');
    Route::any('promotion/coupon/unactiveList','Promotion\CouponController@unactiveList');
    Route::any('promotion/cart/usedtime','Promotion\CartController@usedtime');
    Route::any('promotion/coupon/validaCoupon','Promotion\CouponController@validaCoupon');
    Route::any('promotion/coupon/incrementCouponQty','Promotion\CouponController@incrementCouponQty');
    Route::any('promotion/coupon/restoreCouponQty','Promotion\CouponController@restoreCouponQty');
    Route::any('promotion/coupon/newMemberCoupon','Promotion\CouponController@newMemberCoupon');
    Route::any('promotion/cart/productList','Promotion\CartController@productList');
    Route::any('promotion/cart/productDetail','Promotion\CartController@productDetail');
    Route::any('promotion/cart/checkCode','Promotion\CartController@checkCode');
    Route::any('promotion/coupon/getAllByAddSku','Promotion\CouponController@getAllByAddSku');

    //邮费管理
    Route::any('shipfee/list','ShipfeeController@list');
    Route::any('shipfee/detail','ShipfeeController@detail');
    Route::any('shipfee/update','ShipfeeController@update');
    Route::any('shipfee/del','ShipfeeController@del');
});
