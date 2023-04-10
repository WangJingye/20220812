<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//define('ADMIN','admin');

/*
Route::get('/', function () {
    return  redirect(ADMIN.'/login');
});
Route::group(['prefix' => 'ajax','namespace' => 'Backend', 'middleware' => 'auth'], function () {
        require __DIR__.'/web/ajax.php';
    });
//后台
Route::group([
    'prefix'=>ADMIN,
    'namespace'=>'Backend'
], function () {
    Route::get('/', function () {
        return  redirect(ADMIN.'/index');
    });
    Route::get('index', 'IndexController@index')->name('backend.index');

    Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::any('logout', 'Auth\LoginController@logout')->name('logout');

    Route::any('dashboard', 'DashboardController@index');

    Route::any('user', 'UserController@index')->name('backend.user.index');
    Route::get('user/detail', 'UserController@detail');
    Route::post('user/detail', 'UserController@post');
    Route::get('user/del', 'UserController@del');

    Route::get('page','PageController@index')->name('backend.page.index');
    Route::post('page/status','PageController@status');
    Route::get('page/detail','PageController@detail');
    Route::post('page/detail','PageController@post');
    Route::post('page/ajaxUpload','PageController@ajaxUpload');
    Route::get('page/del','PageController@del');
    Route::any('page/tree','PageController@tree');
    Route::any('page/files','PageController@files');
    Route::any('page/ajaxAuthor','PageController@ajaxAuthor');
    Route::any('page/ajaxConfig','PageController@ajaxConfig');









    
    //配置页面
    Route::get('configpage','ConfigpageController@index')->name('backend.configpage.index');
    Route::get('configpage/sms','ConfigpageController@sms')->name('backend.configpage.sms');
    Route::get('configpage/taobao','ConfigpageController@taobao')->name('backend.configpage.taobao');
    Route::get('configpage/crm','ConfigpageController@crm')->name('backend.configpage.crm');
    Route::get('configpage/redis','ConfigpageController@redis')->name('backend.configpage.redis');
    Route::post('configpage/save','ConfigpageController@save')->name('backend.configpage.save');

    Route::get('config/oss', 'ConfigController@oss')->name('backend.config.oss');
    Route::post('configOss/save','ConfigController@ossSave')->name('backend.config.osssave');
    
    
    Route::get('promotion/category', 'Promotion\CategoryController@index')->name('backend.promotion.category');
    Route::post('promotion/category/dataList', 'Promotion\CategoryController@dataList')->name('backend.promotion.category.dataList');
    Route::any('promotion/category/edit', 'Promotion\CategoryController@edit')->name('backend.promotion.category.edit');
    Route::any('promotion/category/post', 'Promotion\CategoryController@post')->name('backend.promotion.category.post');
    Route::post('promotion/category/destroy', 'Promotion\CategoryController@destroy')->name('backend.promotion.category.destroy');
    Route::get('promotion/category/export', 'Promotion\CategoryController@_export')->name('backend.promotion.category.export');
    
    Route::get('promotion/cart', 'Promotion\CartController@index')->name('backend.promotion.cart');
    Route::post('promotion/cart/dataList', 'Promotion\CartController@dataList')->name('backend.promotion.cart.dataList');
    Route::any('promotion/cart/edit', 'Promotion\CartController@edit')->name('backend.promotion.cart.edit');
    Route::any('promotion/cart/post', 'Promotion\CartController@post')->name('backend.promotion.cart.post');
    Route::post('promotion/cart/destroy', 'Promotion\CartController@destroy')->name('backend.promotion.cart.destroy');
    Route::get('promotion/cart/export', 'Promotion\CartController@_export')->name('backend.promotion.cart.export');
    
    
    Route::get('promotion/coupon', 'Promotion\CouponController@index')->name('backend.promotion.coupon');
    Route::post('promotion/coupon/dataList', 'Promotion\CouponController@dataList')->name('backend.promotion.coupon.dataList');
    Route::any('promotion/coupon/edit', 'Promotion\CouponController@edit')->name('backend.promotion.coupon.edit');
    Route::any('promotion/coupon/post', 'Promotion\CouponController@post')->name('backend.promotion.coupon.post');
    Route::post('promotion/coupon/destroy', 'Promotion\CouponController@destroy')->name('backend.promotion.coupon.destroy');
    Route::get('promotion/coupon/export', 'Promotion\CouponController@_export')->name('backend.promotion.coupon.export');
    
    Route::get('promotion/coupon_tag', 'Promotion\CouponTagController@index')->name('backend.promotion.coupon_tag');
    Route::post('promotion/coupon_tag/dataList', 'Promotion\CouponTagController@dataList')->name('backend.promotion.coupon_tag.dataList');
    Route::any('promotion/coupon_tag/edit', 'Promotion\CouponTagController@edit')->name('backend.promotion.coupon_tag.edit');
    Route::any('promotion/coupon_tag/post', 'Promotion\CouponTagController@post')->name('backend.promotion.coupon_tag.post');
    Route::post('promotion/coupon_tag/destroy', 'Promotion\CouponTagController@destroy')->name('backend.promotion.coupon_tag.destroy');
    Route::get('promotion/coupon_tag/export', 'Promotion\CouponTagController@_export')->name('backend.promotion.coupon_tag.export');
    
    Route::get('promotion/coupon', 'Promotion\CouponController@index')->name('backend.promotion.coupon');
    Route::post('promotion/coupon/dataList', 'Promotion\CouponController@dataList')->name('backend.promotion.coupon.dataList');
    Route::any('promotion/coupon/edit', 'Promotion\CouponController@edit')->name('backend.promotion.coupon.edit');
    Route::any('promotion/coupon/post', 'Promotion\CouponController@post')->name('backend.promotion.coupon.post');
    Route::post('promotion/coupon/destroy', 'Promotion\CouponController@destroy')->name('backend.promotion.coupon.destroy');
    
    
    

});
*/