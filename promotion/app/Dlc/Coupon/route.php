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
    Route::any('/inner/getDetail', 'CouponController@getDetail');
    Route::any('/inner/getNewDetail', 'CouponController@getNewDetail');
    Route::any('/inner/couponUsePluck', 'CouponController@couponUsePluck');
    Route::any('/inner/couponUse', 'CouponController@couponUse');
});





