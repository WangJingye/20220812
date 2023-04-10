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
    
    Route::any('report/log/post','LogController@post');
    Route::any('miniapp/report/dailyRetain','MiniAppCollectController@getDataDailyRetain');
    Route::any('miniapp/report/MonthlyRetain','MiniAppCollectController@getDataMonthlyRetain');
    Route::any('miniapp/report/WeeklyRetain','MiniAppCollectController@getDataWeeklyRetain');
    //Summary的和Trend的两个一起组成了完成的每日数据
    Route::any('miniapp/report/DailySummary','MiniAppCollectController@getDataDailySummary');
    Route::any('miniapp/report/DailyVisitTrend','MiniAppCollectController@getDataDailyVisitTrend');
    Route::any('miniapp/report/WeeklyVisitTrend','MiniAppCollectController@getDataWeeklyVisitTrend');
    Route::any('miniapp/report/MonthlyVisitTrend','MiniAppCollectController@getDataMonthlyVisitTrend');


    Route::any('miniapp/report/UserPortrait','MiniAppCollectController@getDataUserPortrait');
    Route::any('miniapp/report/UserPortraitDaily','MiniAppCollectController@getDataUserPortraitDaily');
    Route::any('miniapp/report/UserPortraitWeekly','MiniAppCollectController@getDataUserPortraitWeekly');
    Route::any('miniapp/report/UserPortraitMonthly','MiniAppCollectController@getDataUserPortraitMonthly');

    Route::any('miniapp/report/VisitDistribution','MiniAppCollectController@getDataVisitDistribution');
    Route::any('miniapp/report/VisitPage','MiniAppCollectController@getDataVisitPage');
    Route::post('miniapp/accessToken','MiniAppAccessToken@wechatTransit');
    Route::any('miniapp/searchkeywordtodb','ProdstatToDbController@SearchKeywordToDB');
    Route::any('miniapp/addcarttodb','ProdstatToDbController@AddCartToDB');
    Route::any('miniapp/prodfavoritetodb','ProdstatToDbController@ProdFavoriteToDB');
    Route::any('miniapp/prodsharetodb','ProdstatToDbController@ProdShareToDB');
    Route::any('miniapp/prodviewtodb','ProdstatToDbController@ProdViewToDB');
    Route::any('miniapp/prodviewbyprodtypetodb','ProdstatToDbController@ProdViewByProdTypeToDB');



    Route::any('demo/productList','DemoController@productList');
    Route::any('config/coupon','Config\CouponController@newMemberCoupon');
});





