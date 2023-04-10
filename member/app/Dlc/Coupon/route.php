<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::group([
    'namespace' => 'Api',
], function () {
    Route::any('couponList', 'CouponController@couponList');
    Route::any('inner/couponUse', 'CouponController@couponUse');
    Route::any('inner/couponBack', 'CouponController@couponBack');
    Route::any('inner/couponListByPage', 'CouponController@couponListByPage');
    Route::any('inner/couponSend', 'CouponController@couponSend');
    Route::any('getMemberInfo', 'UserController@getMemberInfo');
});





