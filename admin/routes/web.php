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

use Illuminate\Support\Facades\Route;

define('ADMIN', 'admin');

Route::get('/', function () {
    return redirect(ADMIN . '/login');
});
Route::group(['prefix' => 'ajax', 'namespace' => 'Backend', 'middleware' => 'auth'], function () {
    require __DIR__ . '/web/ajax.php';
});
//后台
Route::group([
    'prefix' => ADMIN,
    'namespace' => 'Backend',
], function () {
    Route::get('/', function () {
        return redirect(ADMIN . '/index');
    });
    Route::any('point/index', 'PointController@index')->name('backend.point.index');
    Route::any('point/dataList', 'PointController@dataList')->name('backend.point.dataList');
    Route::any('point/get', 'PointController@get')->name('backend.point.get');
    Route::any('point/post', 'PointController@post')->name('backend.point.post');

    Route::get('index', 'IndexController@index')->name('backend.index');

    Route::get('login', 'Auth\LoginController@showLoginForm');
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::any('logout', 'Auth\LoginController@logout')->name('logout');

//    Route::any('dashboard', 'DashboardController@index');
    //首页数据统计
    Route::any('collects', 'IndexController@collects')->name('backend.collects');
    //数据表格接口
    Route::get('data', 'IndexController@data')->name('backend.data');
    //用户管理
    Route::group(['middleware' => []], function () {
        Route::get('user', 'UserController@index')->name('backend.user')->middleware('permission:system.user.index');
        //添加
        Route::get('user/create', 'UserController@create')->name('backend.user.create');
        Route::post('user/store', 'UserController@store')->name('backend.user.store');
        //编辑
        Route::get('user/{id}/edit', 'UserController@edit')->name('backend.user.edit');
        Route::put('user/{id}/update', 'UserController@update')->name('backend.user.update');
        //删除
        Route::delete('user/destroy', 'UserController@destroy')->name('backend.user.destroy');
        //分配角色
        Route::get('user/{id}/role', 'UserController@role')->name('backend.user.role');
        Route::put('user/{id}/assignRole', 'UserController@assignRole')->name('backend.user.assignRole');
        //分配权限
        Route::get('user/{id}/permission', 'UserController@permission')->name('backend.user.permission');
        Route::put('user/{id}/assignPermission', 'UserController@assignPermission')
            ->name('backend.user.assignPermission');
    });
    //角色管理
    Route::group(['middleware' => []], function () {
        Route::get('role', 'RoleController@index')->name('backend.role')->middleware('permission:system.role.index');
        //添加
        Route::get('role/create', 'RoleController@create')->name('backend.role.create');
        Route::post('role/store', 'RoleController@store')->name('backend.role.store');
        //编辑
        Route::get('role/{id}/edit', 'RoleController@edit')->name('backend.role.edit');
        Route::put('role/{id}/update', 'RoleController@update')->name('backend.role.update');
        //删除
        Route::delete('role/destroy', 'RoleController@destroy')->name('backend.role.destroy');
        //分配权限
        Route::get('role/{id}/permission', 'RoleController@permission')->name('backend.role.permission');
        Route::put('role/{id}/assignPermission', 'RoleController@assignPermission')
            ->name('backend.role.assignPermission');
    });
    //权限管理
    Route::group(['middleware' => []], function () {
        Route::get('permission', 'PermissionController@index')->name('backend.permission')->middleware('permission:system.auth.index');
        //添加
        Route::get('permission/create', 'PermissionController@create')->name('backend.permission.create');
        Route::post('permission/store', 'PermissionController@store')->name('backend.permission.store');
        //编辑
        Route::get('permission/{id}/edit', 'PermissionController@edit')->name('backend.permission.edit');
        Route::put('permission/{id}/update', 'PermissionController@update')->name('backend.permission.update');
        //删除
        Route::delete('permission/destroy', 'PermissionController@destroy')->name('backend.permission.destroy');
    });

    Route::get('page', 'PageController@index')->name('backend.page.index');
    Route::get('page/published', 'PageController@published')->name('backend.page.published');
    Route::post('page/status', 'PageController@status');
    Route::get('page/detail/{type}', 'PageController@detail')->where('type', 'new|draft|published');
    Route::post('page/detail/{type}', 'PageController@post')->where('type', 'draft|published|published_and_online|offline');
    Route::post('page/ajaxUpload', 'PageController@ajaxUpload')->name('backend.page.ajaxUpload');
    Route::post('page/ajaxUploadShareImage', 'PageController@ajaxUploadShareImage')->name('backend.page.ajaxUploadShareImage');
    Route::post('page/ajaxUploadProduct', 'PageController@ajaxUploadProduct')->name('backend.page.ajaxUploadProduct');
    Route::get('page/del/{type}', 'PageController@del')->where('type', 'draft|published');
    Route::any('page/tree', 'PageController@tree');
    Route::any('page/files', 'PageController@files');
    Route::any('page/ajaxAuthor', 'PageController@ajaxAuthor');
    Route::any('page/ajaxConfig', 'PageController@ajaxConfig');
    Route::any('page/product/list', 'PageController@productList');
    Route::any('page/page/list', 'PageController@pageList');
    Route::any('page/phpinfo', 'PageController@phpinfo');
    Route::any('page/iframe', 'PageController@iframe');
    Route::any('page/couponList', 'PageController@couponList');
    Route::any('page/kewrordCheck', 'PageController@kewrordCheck');
    Route::any('page/getToken', 'PageController@getToken');
    Route::any('page/getUnlimited/{key}', 'PageController@getUnlimited');
    Route::any('page/ajaxKey', 'PageController@ajaxKey');
    Route::any('page/navication', 'PageController@navication')->name('backend.page.navication');
    Route::any('page/navication1', 'PageController@navication1')->name('backend.page.navication1');
    Route::post('page/navicationSave/{type}', 'PageController@navicationSave')->where('type', 'draft|published|published_and_online|offline');;
    Route::post('page/navicationSave1/{type}', 'PageController@navicationSave1')->where('type', 'draft|published|published_and_online|offline');;

    //配置页面
    Route::get('configpage', 'ConfigpageController@index')->name('backend.configpage.index');
    Route::get('configpage/sms', 'ConfigpageController@sms')->name('backend.configpage.sms');
    Route::get('configpage/taobao', 'ConfigpageController@taobao')->name('backend.configpage.taobao');
    Route::get('configpage/crm', 'ConfigpageController@crm')->name('backend.configpage.crm');
    Route::get('configpage/redis', 'ConfigpageController@redis')->name('backend.configpage.redis');
    Route::post('configpage/save', 'ConfigpageController@save')->name('backend.configpage.save');

    Route::get('config/oss', 'ConfigController@oss')->name('backend.config.oss');
    Route::post('configOss/save', 'ConfigController@ossSave')->name('backend.config.osssave');
    Route::post('category/tree', 'Controller@tree')->name('backend.category.tree');

    //promotion
    Route::get('promotion/cart', 'Promotion\CartController@index')->name('backend.promotion.cart')->middleware('permission:promotion.cart.index');
    Route::post('promotion/cart/dataList', 'Promotion\CartController@dataList')->name('backend.promotion.cart.dataList');
    Route::any('promotion/cart/edit', 'Promotion\CartController@edit')->name('backend.promotion.cart.edit');
    Route::any('promotion/cart/post', 'Promotion\CartController@post')->name('backend.promotion.cart.post');
    Route::post('promotion/cart/destroy', 'Promotion\CartController@destroy')->name('backend.promotion.cart.destroy');
    Route::get('promotion/cart/export', 'Promotion\CartController@_export')->name('backend.promotion.cart.export');
    Route::any('promotion/cart/getCrossRules', 'Promotion\CartController@getCrossRules')->name('backend.promotion.cart.getCrossRules');
    Route::any('promotion/cart/active', 'Promotion\CartController@active')->name('backend.promotion.cart.active');
    Route::any('promotion/cart/unactive', 'Promotion\CartController@unactive')->name('backend.promotion.cart.unactive');
    Route::any('promotion/cart/view', 'Promotion\CartController@view')->name('backend.promotion.cart.view');
    Route::any('promotion/cart/messActive', 'Promotion\CartController@messActive')->name('backend.promotion.cart.messActive');
    Route::any('promotion/cart/messUnactive', 'Promotion\CartController@messUnactive')->name('backend.promotion.cart.messUnactive');
    //coupon
    Route::get('promotion/coupon', 'Promotion\CouponController@index')->name('backend.promotion.coupon');
    Route::post('promotion/coupon/dataList', 'Promotion\CouponController@dataList')->name('backend.promotion.coupon.dataList');
    Route::any('promotion/coupon/edit', 'Promotion\CouponController@edit')->name('backend.promotion.coupon.edit');
    Route::any('promotion/coupon/post', 'Promotion\CouponController@post')->name('backend.promotion.coupon.post');
    Route::post('promotion/coupon/destroy', 'Promotion\CouponController@destroy')->name('backend.promotion.coupon.destroy');
    Route::get('promotion/coupon/export', 'Promotion\CouponController@_export')->name('backend.promotion.coupon.export');
    //gift
    Route::get('promotion/gift', 'Promotion\GiftController@index')->name('backend.promotion.gift');
    Route::post('promotion/gift/dataList', 'Promotion\GiftController@dataList')->name('backend.promotion.gift.dataList');
    Route::any('promotion/gift/edit', 'Promotion\GiftController@edit')->name('backend.promotion.gift.edit');
    Route::any('promotion/gift/post', 'Promotion\GiftController@post')->name('backend.promotion.gift.post');
    Route::any('promotion/gift/active', 'Promotion\GiftController@active')->name('backend.promotion.gift.active');
    Route::any('promotion/gift/unactive', 'Promotion\GiftController@unactive')->name('backend.promotion.gift.unactive');
    Route::any('promotion/gift/uploadPic', 'Promotion\GiftController@uploadPic')->name('backend.promotion.gift.uploadPic');
    Route::any('promotion/gift/view', 'Promotion\GiftController@view')->name('backend.promotion.gift.view');
    //promotion log 
    Route::get('promotion/log', 'Promotion\LogController@index')->name('backend.promotion.log');
    Route::post('promotion/log/dataList', 'Promotion\LogController@dataList')->name('backend.promotion.log.dataList');
    //promotion code
    Route::get('promotion/code', 'Promotion\CodeController@index')->name('backend.promotion.code');
    Route::post('promotion/code/dataList', 'Promotion\CodeController@dataList')->name('backend.promotion.code.dataList');
    Route::any('promotion/code/edit', 'Promotion\CodeController@edit')->name('backend.promotion.code.edit');
    Route::any('promotion/code/post', 'Promotion\CodeController@post')->name('backend.promotion.code.post');

    //log
    Route::get('report/log', 'Report\LogController@index')->name('backend.report.log');
    Route::post('report/log/dataList', 'Report\LogController@dataList')->name('backend.report.log.dataList');
    Route::any('report/log/edit', 'Report\LogController@edit')->name('backend.report.log.edit');
    Route::any('report/log/post', 'Report\LogController@post')->name('backend.report.log.post');
    Route::post('report/log/destroy', 'Report\LogController@destroy')->name('backend.report.log.destroy');
    Route::get('report/log/export', 'Report\LogController@_export')->name('backend.report.log.export');

    //goods
//     Route::get('goods/category', 'Goods\CategoryController@index')->name('backend.goods.category');
    Route::get('goods/category', 'Goods\CategoryController@index')->name('backend.goods.category')->middleware('permission:goods.category.index');
    Route::get('goods/category/list', 'Goods\CategoryController@list')->name('backend.goods.category.list');
    Route::get('goods/category/prodList', 'Goods\CategoryController@prodList')->name('backend.goods.category.prodList');
    Route::get('goods/category/relateProds', 'Goods\CategoryController@relateProds')
        ->name('backend.goods.category.relateProds');
    Route::post('goods/category/editRelateProds', 'Goods\CategoryController@editRelateProds')
        ->name('backend.goods.category.editRelateProds');
    Route::any('goods/category/edit', 'Goods\CategoryController@edit')->name('backend.goods.category.edit');
    Route::any('goods/category/batchChangeSort', 'Goods\CategoryController@batchChangeSort')->name('backend.goods.category.batchChangeSort');
    Route::any('goods/category/get', 'Goods\CategoryController@get')->name('backend.goods.category.get');
    Route::any('goods/category/getCatProdAndColleList', 'Goods\CategoryController@getCatProdAndColleList')->name('backend.goods.category.getCatProdAndColleList');
    Route::any('goods/category/updateCatRelation', 'Goods\CategoryController@updateCatRelation')->name('backend.goods.category.updateCatRelation');
    Route::any('goods/category/getProdAndCollList', 'Goods\CategoryController@getProdAndCollList')->name('backend.goods.category.getProdAndCollList');
    Route::any('goods/category/relate', 'Goods\CategoryController@relate')->name('backend.goods.category.relate');
    Route::any('goods/category/look', 'Goods\CategoryController@look')->name('backend.goods.category.look');
    Route::any('goods/category/add', 'Goods\CategoryController@add')->name('backend.goods.category.add');
    Route::any('goods/category/create', 'Goods\CategoryController@create')->name('backend.goods.category.create');
    Route::any('goods/category/addProducts', 'Goods\CategoryController@addProducts')->name('backend.goods.category.addproducts');
    Route::any('goods/category/delProduct', 'Goods\CategoryController@delProduct')->name('backend.goods.category.delproduct');

    Route::any('goods/category/pCateListNoSub', 'Goods\CategoryController@pCateListNoSub')->name('backend.goods.category.pCateListNoSub');
    Route::any('goods/category/pCateList', 'Goods\CategoryController@pCateList')->name('backend.goods.category.pCateList');
    Route::any('goods/category/getProdTypelist', 'Goods\CategoryController@getProdTypelist')->name('backend.goods.category.getProdTypelist');
    Route::any('goods/category/calculateProdIds', 'Goods\CategoryController@calculateProdIds')->name('backend.goods.category.calculateProdIds');
    Route::any('goods/category/offCat', 'Goods\CategoryController@offCat')->name('backend.goods.category.offCat');
    Route::any('goods/category/upCat', 'Goods\CategoryController@upCat')->name('backend.goods.category.upCat');
    Route::any('goods/category/handleCatSortCsv', 'Goods\CategoryController@handleCatSortCsv')->name('backend.goods.category.handleCatSortCsv');

//     Route::get('goods/spu', 'Goods\SpuController@index')->name('backend.goods.spu');
    Route::get('goods/spu', 'Goods\SpuController@index')->name('backend.goods.spu')->middleware('permission:goods.spu.index');
    Route::get('goods/spu/list', 'Goods\SpuController@list')->name('backend.goods.spu.list');
    Route::any('goods/spu/import', 'Goods\SpuController@import')->name('backend.goods.spu.import');
    Route::any('goods/spu/export', 'Goods\SpuController@export')->name('backend.goods.spu.export');
    Route::get('goods/spu/changeStatus', 'Goods\SpuController@changeStatus')->name('backend.goods.spu.changeStatus');
    Route::get('goods/spu/relateSkus', 'Goods\SpuController@relateSkus')->name('backend.goods.spu.relateSkus');
    Route::get('goods/spu/relateDoorSkus', 'Goods\SpuController@relateDoorSkus')->name('backend.goods.spu.relateDoorSkus');
    Route::post('goods/spu/editRelateSkus', 'Goods\SpuController@editRelateSkus')
        ->name('backend.goods.spu.editRelateSkus');
    Route::any('goods/spu/edit', 'Goods\SpuController@edit')->name('backend.goods.spu.edit');
    Route::any('goods/spu/get', 'Goods\SpuController@get')->name('backend.goods.spu.get');
    Route::any('goods/spu/add', 'Goods\SpuController@add')->name('backend.goods.spu.add');
    Route::any('goods/spu/add', 'Goods\SpuController@insert')->name('backend.goods.spu.insert');
    Route::any('goods/spu/changeDisplay', 'Goods\SpuController@changeDisplay')->name('backend.goods.spu.changeDisplay');
    Route::any('goods/spu/rawData', 'Goods\SpuController@rawData')->name('backend.goods.spu.rawData');
    Route::any('goods/spu/uploadFile', 'Goods\SpuController@uploadFile')->name('backend.goods.spu.uploadFile');
    Route::any('goods/spu/checkSpec', 'Goods\SpuController@checkSpec')->name('backend.goods.spu.checkSpec');

    Route::any('goods/search/addBlackList', 'Goods\SearchController@addBlackList')->name('backend.goods.search.addBlackList');
    Route::any('goods/search/delBlackList', 'Goods\SearchController@delBlackList')->name('backend.goods.search.delBlackList');
    Route::any('goods/search/getBlackList', 'Goods\SearchController@getBlackList')->name('backend.goods.search.getBlackList');
    Route::any('goods/search/blacklist', 'Goods\SearchController@blacklist')->name('backend.goods.search.blacklist');

    #搜索相关
    Route::any('search/addBlackList', 'Search\BlacklistController@addBlackList')->name('backend.goods.search.addBlackList');
    Route::any('search/delBlackList', 'Search\BlacklistController@delBlackList')->name('backend.goods.search.delBlackList');
    Route::any('search/getBlackList', 'Search\BlacklistController@getBlackList')->name('backend.goods.search.getBlackList');
    Route::any('search/blacklist', 'Search\BlacklistController@list')->name('backend.goods.search.blacklist');

    Route::any('search/addRedirect', 'Search\RedirectController@addRedirect')->name('backend.goods.search.addRedirect');
    Route::any('search/delRedirect', 'Search\RedirectController@delRedirect')->name('backend.goods.search.delRedirect');
    Route::any('search/getRedirect', 'Search\RedirectController@getRedirect')->name('backend.goods.search.getRedirect');
    Route::any('search/updateRedirect', 'Search\RedirectController@updateRedirect')->name('backend.goods.search.updateRedirect');
    Route::any('search/redirectlist', 'Search\RedirectController@list')->name('backend.goods.search.redirectlist');
    Route::any('search/getRedirectList', 'Search\RedirectController@getRedirectList')->name('backend.goods.search.getRedirectList');
    Route::any('search/getRedirect', 'Search\RedirectController@getRedirect')->name('backend.goods.search.getRedirect');
    Route::any('search/redirect/add', 'Search\RedirectController@add')->name('backend.goods.search.redirect.add');
    Route::any('search/redirect/edit', 'Search\RedirectController@edit')->name('backend.goods.search.redirect.edit');

    Route::any('search/addSynonym', 'Search\SynonymController@addSynonym')->name('backend.goods.search.addSynonym');
    Route::any('search/delSynonym', 'Search\SynonymController@delSynonym')->name('backend.goods.search.delSynonym');
    Route::any('search/getSynonym', 'Search\SynonymController@getSynonym')->name('backend.goods.search.getSynonym');
    Route::any('search/updateSynonym', 'Search\SynonymController@updateSynonym')->name('backend.goods.search.updateSynonym');
    Route::any('search/synonymlist', 'Search\SynonymController@list')->name('backend.goods.search.synonymlist');
    Route::any('search/getSynonymList', 'Search\SynonymController@getSynonymList')->name('backend.goods.search.getSynonymList');
    Route::any('search/getSynonym', 'Search\SynonymController@getSynonym')->name('backend.goods.search.getSynonym');
    Route::any('search/synonym/add', 'Search\SynonymController@add')->name('backend.goods.search.synonym.add');
    Route::any('search/synonym/edit', 'Search\SynonymController@edit')->name('backend.goods.search.synonym.edit');
    Route::any('search/synonym/export', 'Search\SynonymController@export')->name('backend.goods.search.synonym.export');

    Route::any('goods/sku/add', 'Goods\SkuController@add')->name('backend.goods.sku.add');
    Route::any('goods/sku/insert', 'Goods\SkuController@insert')->name('backend.goods.sku.insert');

    Route::any('ad/loc', 'Ad\LocationController@index')->name('backend.ad.location.index');
    Route::any('ad/loc/list', 'Ad\LocationController@list')->name('backend.ad.location.list');
    Route::any('ad/loc/add', 'Ad\LocationController@add')->name('backend.ad.location.add');
    Route::any('ad/loc/insert', 'Ad\LocationController@insert')->name('backend.ad.location.insert');
    Route::any('ad/loc/edit', 'Ad\LocationController@edit')->name('backend.ad.location.edit');
    Route::any('ad/loc/update', 'Ad\LocationController@update')->name('backend.ad.location.update');

    Route::any('ad/item', 'Ad\ItemController@index')->name('backend.ad.item.index');
    Route::any('ad/item/list', 'Ad\ItemController@list')->name('backend.ad.item.list');
    Route::any('ad/item/add', 'Ad\ItemController@add')->name('backend.ad.item.add');
    Route::any('ad/item/insert', 'Ad\ItemController@insert')->name('backend.ad.item.insert');
    Route::any('ad/item/edit', 'Ad\ItemController@edit')->name('backend.ad.item.edit');
    Route::any('ad/item/update', 'Ad\ItemController@update')->name('backend.ad.item.update');
    Route::any('ad/item/delete', 'Ad\ItemController@delete')->name('backend.ad.item.delete');

    #定制的广告位
    Route::any('ad/item/hotsale', 'Ad\ItemController@hotsale')->name('backend.ad.item.hotsale');

    #通知栏+弹窗
    Route::any('pop/info', 'PopBarController@popList')->name('backend.popBar.dataList');
    Route::any('pop/add', 'PopBarController@popAdd')->name('backend.popBar.add');
    Route::any('pop/index', 'PopBarController@index')->name('backend.popBar.index');
    #Route::any('bar/add', 'PopBarController@barAdd')->name('backend.popBar.add');

    Route::any('goods/collection/cms', 'Goods\CollectionController@cms')->name('backend.goods.collection.cms');
    Route::any('goods/collection/cmssave', 'Goods\CollectionController@cmssave')->name('backend.goods.collection.cmssave');
    Route::get('goods/collection', 'Goods\CollectionController@index')->name('backend.goods.collection')->middleware('permission:goods.collection.index');
    Route::get('goods/collection/list', 'Goods\CollectionController@list')->name('backend.goods.collection.list');
    Route::get('goods/collection/get', 'Goods\CollectionController@get')->name('backend.goods.collection.get');
    Route::any('goods/collection/update', 'Goods\CollectionController@update')->name('backend.goods.collection.update');
    Route::any('goods/collection/add', 'Goods\CollectionController@add')->name('backend.goods.collection.add');
    Route::any('goods/collection/insert', 'Goods\CollectionController@insert')->name('backend.goods.collection.insert');
    Route::any('goods/collection/changeStatus', 'Goods\CollectionController@changeStatus')->name('backend.goods.collection.changeStatus');
    Route::any('goods/collection/getProdAndCollList', 'Goods\CollectionController@getFormatedProductList')->name('backend.goods.collection.getFormatedProductList');

    Route::get('goods/recommend', 'Goods\RecommendController@index')->name('backend.goods.recommend.index');
    Route::get('goods/recommend/list', 'Goods\RecommendController@list')->name('backend.goods.recommend.list');
    Route::get('goods/recommend/changeStatus', 'Goods\RecommendController@changeStatus')->name('backend.goods.recommend.changeStatus');
    Route::any('goods/recommend/add', 'Goods\RecommendController@add')->name('backend.goods.recommend.add');
    Route::any('goods/recommend/insert', 'Goods\RecommendController@insert')->name('backend.goods.recommend.insert');


    Route::any('goods/sku/cms', 'Goods\SkuController@cms')->name('backend.goods.sku.cms');
    Route::any('goods/sku/cmssave', 'Goods\SkuController@cmssave')->name('backend.goods.sku.cmssave');

    Route::any('goods/spu/cms', 'Goods\SpuController@cms')->name('backend.goods.spu.cms');
    Route::any('goods/spu/cmssave', 'Goods\SpuController@cmssave')->name('backend.goods.spu.cmssave');
    Route::any('goods/spu/add', 'Goods\SpuController@add')->name('backend.goods.spu.add');
    Route::any('goods/spu/insert', 'Goods\SpuController@insert')->name('backend.goods.spu.insert');
//     Route::get('goods/sku', 'Goods\SkuController@index')->name('backend.goods.sku');
    Route::get('goods/sku', 'Goods\SkuController@index')->name('backend.goods.sku')->middleware('permission:goods.sku.index');
    Route::get('goods/sku/list', 'Goods\SkuController@list')->name('backend.goods.sku.list');
    Route::any('goods/sku/edit', 'Goods\SkuController@edit')->name('backend.goods.sku.edit');
    Route::any('goods/sku/get', 'Goods\SkuController@get')->name('backend.goods.sku.get');
    Route::any('goods/sku/export', 'Goods\SkuController@_export')->name('backend.goods.sku.export');
    Route::any('goods/sku/getStock', 'Goods\SkuController@getStock')->name('backend.goods.sku.getStock');
    Route::any('goods/sku/updateStock', 'Goods\SkuController@updateStock')->name('backend.goods.sku.updateStock');
    Route::get('goods/doorSku', 'Goods\DoorSkuController@index')->name('backend.goods.doorSku')->middleware('permission:goods.doorSku.index');
    Route::get('goods/doorSku/list', 'Goods\DoorSkuController@list')->name('backend.goods.doorSku.list');
    Route::any('goods/doorSku/edit', 'Goods\DoorSkuController@edit')->name('backend.goods.doorSku.edit');
    Route::any('goods/doorSku/get', 'Goods\DoorSkuController@get')->name('backend.goods.doorSku.get');
    Route::any('goods/channel/update', 'Goods\SkuController@updateChannelStock')->name('backend.goods.channel.update');
    Route::any('goods/channel/updatesecure', 'Goods\SkuController@updateSkuSecure')->name('backend.goods.update.secure');
    Route::any('goods/stock/log', 'Goods\SkuController@stockLoglist')->name('backend.goods.stock.log');

    //order
    Route::get('sales/order', 'Sales\OrderController@index')->name('backend.sales.order');
    Route::post('sales/order/dataList', 'Sales\OrderController@dataList')->name('backend.sales.order.dataList');
    Route::any('sales/order/edit', 'Sales\OrderController@edit')->name('backend.sales.order.edit');
    Route::any('sales/order/afterSale', 'Sales\OrderController@afterSale')->name('backend.sales.order.add');
    Route::any('sales/order/afterSaleAction', 'Sales\OrderController@afterSaleAction')->name('backend.sales.order.after.action');
    Route::any('sales/order/refund', 'Sales\OrderController@refund')->name('backend.sales.order.refund');
    Route::any('sales/order/refundInfo', 'Sales\OrderController@refundInfo')->name('backend.sales.order.refund.info');


    Route::any('sales/order/post', 'Sales\OrderController@post')->name('backend.sales.order.post');
    Route::post('sales/order/destroy', 'Sales\OrderController@destroy')->name('backend.sales.order.destroy');
    Route::any('sales/order/export', 'Oms\OrderController@_export')->name('backend.sales.order.export');
    Route::any('sales/order/getExpressInfo', 'Sales\OrderController@getExpressInfo')->name('backend.sales.order.getExpressInfo');


    //OMS
//    Route::get('oms/order/manager', 'Oms\OrderManagerController@index')->name('backend.oms.order');

    //config
    Route::get('config/coupon', 'Config\CouponController@index')->name('backend.config.coupon');
    Route::get('config/coupon', 'Config\CouponController@index')->name('backend.config.coupon')->middleware('permission:config.coupon.index');
    Route::any('config/coupon/save', 'Config\CouponController@save')->name('backend.config.coupon.save');
    Route::any('config/coupon/uploadPic', 'Config\CouponController@uploadPic')->name('backend.config.coupon.uploadPic');

    Route::get('config/recommend', 'Config\RecommendController@index')->name('backend.config.recommend')->middleware('permission:config.recommend.index');
    Route::any('config/import', 'Config\ImportController@index')->name('backend.product.import');
    Route::any('config/import/datalist', 'Config\ImportController@datalist')->name('backend.product.import.datalist');
    Route::any('config/import/ajaxUpload', 'Config\ImportController@ajaxUpload')->name('backend.product.import.ajaxUpload');
    Route::post('config/recommend/save', 'Config\RecommendController@save')->name('backend.config.recommend.save');
    Route::get('config/spp', 'Config\SppController@index')->name('backend.config.spp')->middleware('permission:config.spp.index');
    Route::get('config/spu', 'Config\SpuController@index')->name('backend.config.spu');
    Route::get('config/spu', 'Config\SpuController@index')->name('backend.config.spu')->middleware('permission:config.spu.index');
    Route::post('config/spu/upload/{img}', 'Config\SpuController@upload')->name('backend.config.spu.upload')->where('img', 'prod_default|model_default');;
    Route::get('config/spp/list', 'Config\SppController@list')->name('backend.config.spp.list');
    Route::any('config/spp/add', 'Config\SppController@add')->name('backend.config.spp.add');
    Route::any('config/spp/look', 'Config\SppController@look')->name('backend.config.spp.look');
    Route::any('config/spp/get', 'Config\SppController@get')->name('backend.config.spp.get');
    Route::any('config/spp/edit', 'Config\SppController@edit')->name('backend.config.spp.edit');
    Route::any('config/spp/del', 'Config\SppController@del')->name('backend.config.spp.del');
    Route::any('config/return', 'Config\ReturnController@index')->name('backend.config.return')->middleware('permission:config.return.index');
    Route::any('config/return/save', 'Config\ReturnController@save')->name('backend.config.return.save');
    Route::any('file/uploadPic', 'FileController@uploadPic')->name('backend.file.uploadPic');
    Route::any('config/mail', 'Config\MailController@index')->name('backend.config.mail');
    Route::any('config/mail/create', 'Config\MailController@create')->name('backend.config.mail.create');
    //缓存刷新
    Route::get('config/cache/index', 'Config\CacheController@index')->name('backend.config.cache')->middleware('permission:goods.sku.index');
    Route::any('config/cache/clear/{action}', 'Config\CacheController@clear')->name('backend.config.cache.clear');
    //数据导入
    Route::get('config/data/index', 'Config\DataController@index')->name('backend.config.data')->middleware('permission:goods.sku.index');
    Route::any('config/data/import/{action}', 'Config\DataController@import')->name('backend.config.data.import');

    Route::group(['namespace' => 'Member', 'prefix' => 'member'], function () {
        Route::get('index', function (Request $request) {
            return view('backend.member.index');
        })->name('backend.member.index')->middleware('permission:member.all.index');
        Route::any('list', 'MemberController@list')->name('backend.member.list');
        Route::get('edit', 'MemberController@detail')->name('backend.member.list.edit');
        Route::post('destroy', 'MemberController@destroy')->name('backend.member.list.destroy');
        Route::any('export', 'ExcelController@export')->name('backend.member.list.export');
        Route::any('importCoupon', 'MemberController@importCoupon')->name('backend.member.import.coupon');


        Route::any('merge', 'MemberController@merge')->name('backend.member.merge');
        Route::any('getSlaveAndMasterMember', 'MemberController@getSlaveAndMasterMember')->name('backend.member.merge.getSlaveAndMasterMember');
        Route::any('mergeSlaveMemberIntoMasterMember', 'MemberController@mergeSlaveMemberIntoMasterMember')->name('backend.member.merge.mergeSlaveMemberIntoMasterMember');

        Route::get('/level/index', function (Request $request) {
            return view('backend.member.level.index');
        })->name('backend.member.level.index');
        Route::post('level/list', 'MemberGradeController@list')->name('backend.member.level.list');
        Route::get('level/edit', 'MemberGradeController@detail')->name('backend.member.level.list.edit');
        Route::post('level/destroy', 'MemberGradeController@destroy')->name('backend.member.level.list.destroy');
        Route::get('user/address', 'MemberController@userAddress')->name('backend.member.address');
        Route::get('user/info', 'MemberController@getUserInfo')->name('backend.member.info');
        Route::any('export/member', 'MemberController@exportMember')->name('backend.member.exportMember');


        // Route::get('export', 'ExcelController@export')->name('backend.member.list.export');
    });

    //数据统计分析 Data Statistic Analysis
    Route::group(['namespace' => 'Statistics', 'prefix' => 'dsa'], function () {
        //菜单

        //留存
        Route::get('retain/stat', 'RetainStatController@index')->name('dsa.retain.index');
        Route::post('retain/daily', 'RetainStatController@getDailyRetainData')->name('dsa.retain.daily');
        Route::post('retain/monthly', 'RetainStatController@getMonthlyRetainData')->name('dsa.retain.monthly');
        Route::post('retain/weekly', 'RetainStatController@getWeeklyRetainData')->name('dsa.retain.weekly');
        //概况
        Route::get('summary/stat', 'SummaryStatController@index')->name('dsa.summary.index');
        Route::get('summary/lists', 'SummaryStatController@getSummaryData')->name('dsa.summary.daily');
        //趋势
        Route::get('visit/trend/stat', 'VisitTrendStatController@index')->name('dsa.visit.trend.index');
        Route::post('visit/trend/daily', 'VisitTrendStatController@getDailyVisitTrendData')
            ->name('dsa.visit.trend.daily');
        Route::post('visit/trend/monthly', 'VisitTrendStatController@getMonthlyVisitTrendData')
            ->name('dsa.visit.trend.monthly');
        Route::post('visit/trend/weeekly', 'VisitTrendStatController@getWeeklyVisitTrendData')
            ->name('dsa.visit.trend.weekly');

        //画像统计
        Route::any('portrait/stat', 'VisitStatController@userPortrait')->name('dsa.user.portrait');
        Route::any('user/portrait/lists', 'VisitStatController@getUserPortrainData')->name('dsa.visit.user.portrait');
        //访问分布
        Route::get('visit/dist/stat', 'VisitStatController@visitDist')->name('dsa.visit.distribution');
        Route::any('visit/dist/lists', 'VisitStatController@getVisitDistributionStateData')->name('dsa.visit.dist');
        //页面统计
        Route::get('page/stat', 'VisitStatController@pageStat')->name('dsa.page.stat');
        Route::any('visit/pages/lists', 'VisitStatController@getPageStateData')->name('dsa.visit.page');

    });

    //数据统计分析 Data Statistic Analysis
    Route::group(['namespace' => 'Prodstat', 'prefix' => 'dsb'], function () {
        Route::get('prodstat/view', 'ProductStatisticsController@view')->name('dsb.prodstat.view');
        Route::get('prodstat/addcart', 'ProductStatisticsController@addcart')->name('dsb.prodstat.addcart');
        Route::get('prodstat/favorite', 'ProductStatisticsController@favorite')->name('dsb.prodstat.favorite');
        Route::get('prodstat/share', 'ProductStatisticsController@share')->name('dsb.prodstat.share');
        Route::get('prodstat/keyword', 'ProductStatisticsController@keyword')->name('dsb.prodstat.keyword');
        Route::get('prodstat/order', 'ProductStatisticsController@order')->name('dsb.prodstat.order');
        Route::get('prodstat/prodtypeview', 'ProductStatisticsController@prodtypeview')->name('dsb.prodstat.prodtypeview');


        Route::post('prodstat/viewCount', 'ProdViewStatController@viewCount')->name('dsb.prodstat.viewCount');
        Route::post('prodstat/addcartCount', 'ProdAddCartStatController@addcartCount')->name('dsb.prodstat.addcartCount');
        Route::post('prodstat/shareCount', 'ProdShareStatController@shareCount')->name('dsb.prodstat.shareCount');
        Route::post('prodstat/favoriteCount', 'ProdFavoriteStatController@favoriteCount')->name('dsb.prodstat.favoriteCount');
        Route::post('prodstat/keywordCount', 'ProdKeywordsStatController@keywordCount')->name('dsb.prodstat.keywordCount');
        Route::any('prodstat/orderCount', 'ProductStatisticsController@orderCount')->name('dsb.prodstat.orderCount');
        Route::post('prodstat/setOrderRate', 'ProductStatisticsController@setOrderRate')->name('dsb.prodstat.setOrderRate');
        Route::post('prodstat/prodTypeViewCount', 'ProdTypeViewStatController@prodTypeViewCount')->name('dsb.prodstat.prodTypeViewCount');

        Route::post('prodstat/viewExport', 'ProdViewStatController@viewExport')->name('dsb.prodstat.viewExport');
        Route::post('prodstat/addcartExport', 'ProdAddCartStatController@addcartExport')->name('dsb.prodstat.addcartExport');
        Route::post('prodstat/shareExport', 'ProdShareStatController@shareExport')->name('dsb.prodstat.shareExport');
        Route::post('prodstat/favoriteExport', 'ProdFavoriteStatController@favoriteExport')->name('dsb.prodstat.favoriteExport');
        Route::post('prodstat/keywordExport', 'ProdKeywordsStatController@keywordExport')->name('dsb.prodstat.keywordExport');
        Route::any('prodstat/orderExport', 'ProductStatisticsController@orderExport')->name('dsb.prodstat.orderExport');
        Route::post('prodstat/prodTypeViewExport', 'ProdTypeViewStatController@prodTypeViewExport')->name('dsb.prodstat.prodTypeViewExport');


        Route::any('prodstat/getConversionRate', 'ProductStatisticsController@getConversionRate')->name('dsb.prodstat.getconversionRate');

        Route::any('prodstat/conversionRate', 'ProductStatisticsController@conversionRate')->name('dsb.prodstat.conversionRate');
        Route::get('prodstat/efficiency', 'ProductStatisticsController@efficiency')->name('dsb.prodstat.efficiency');


    });

    Route::group(['namespace' => 'Store', 'prefix' => 'store'], function () {

        Route::get('index', 'StoreController@index')->name('backend.store.index');
        Route::any('list', 'StoreController@list')->name('backend.store.list');
        Route::get('edit', 'StoreController@edit')->name('backend.store.edit');
        Route::post('update', 'StoreController@update')->name('backend.store.update');
        Route::get('add', 'StoreController@add')->name('backend.store.add');

        Route::get('employee/index', 'EmployeeController@index')->name('backend.store.employee.index');
        Route::any('employee/list', 'EmployeeController@list')->name('backend.store.employee.list');
        Route::get('employee/edit', 'EmployeeController@edit')->name('backend.store.employee.edit');
        Route::post('employee/update', 'EmployeeController@update')->name('backend.store.employee.update');
        Route::get('employee/add', 'EmployeeController@add')->name('backend.store.employee.add');
        Route::any('employee/bindAll', 'EmployeeController@bindAll')->name('backend.store.employee.bindAll');

        Route::get('role/index', 'RoleController@index')->name('backend.store.role.index');
        Route::any('role/list', 'RoleController@list')->name('backend.store.role.list');
        Route::get('role/edit', 'RoleController@edit')->name('backend.store.role.edit');
        Route::post('role/update', 'RoleController@update')->name('backend.store.role.update');
        Route::get('role/add', 'RoleController@add')->name('backend.store.role.add');

        Route::get('guide/index', 'GuideController@index')->name('backend.store.guide.index');
        Route::any('guide/list', 'GuideController@list')->name('backend.store.guide.list');
        Route::any('guide/showInfo', 'GuideController@showInfo')->name('backend.store.guide.showInfo');
        Route::any('guide/showInfoList', 'GuideController@showInfoList')->name('backend.store.guide.showInfoList');
        Route::any('guide/realTime', 'GuideController@realTime')->name('backend.store.guide.realTime');
        Route::any('guide/realTimeList', 'GuideController@realTimeList')->name('backend.store.guide.realTimeList');
        Route::any('guide/getStoreFromCity', 'GuideController@getStoreFromCity')->name('backend.store.guide.getStoreFromCity');
        Route::any('guide/dashBoardNeedData', 'GuideController@dashBoardNeedData')->name('backend.store.guide.dashBoardNeedData');


    });
    Route::group(['namespace' => 'Oms', 'prefix' => 'oms'], function () {

        Route::get('order/index', 'OrderController@index')->name('backend.oms.order');
        Route::any('order/list', 'OrderController@list')->name('backend.oms.order.list');
        Route::get('order/edit', 'OrderController@edit')->name('backend.oms.order.edit');
        Route::post('order/update', 'OrderController@update')->name('backend.oms.order.update');
        Route::any('orderItem/list', 'OrderController@orderItem')->name('backend.oms.order.item.list');
        Route::any('orderItem/index', 'OrderController@orderItemIndex')->name('backend.oms.order.item');
        Route::any('orderItem/list', 'OrderController@orderItemList')->name('backend.oms.order.item.list');
        Route::any('order/status/update', 'OrderController@updateOrderStatus')->name('backend.oms.order.status.update');
        Route::any('order/free', 'OrderController@orderFree')->name('backend.oms.order.free');
        Route::post('order/batch/delivery', 'OrderController@batchDelivery')->name('backend.oms.order.batch.delivery');
        Route::post('order/invoice', 'OrderController@invoice')->name('backend.oms.order.invoice');

        Route::get('order/add/index', 'OrderAddedController@index')->name('backend.oms.add');
        Route::any('order/add/list', 'OrderAddedController@list')->name('backend.oms.add.list');
        Route::get('order/add/edit', 'OrderAddedController@edit')->name('backend.oms.add.edit');
        Route::get('order/add/create', 'OrderAddedController@create')->name('backend.oms.add.create');
        Route::get('order/get/sku', 'OrderAddedController@getSku')->name('backend.oms.add.sku');
        Route::any('order/insert', 'OrderAddedController@addOrder')->name('backend.oms.add.insert');

        //订单同步
        Route::any('order/omsSync', 'OrderController@omsSync')->name('backend.oms.order.omsSync');
        Route::any('order/export/{type}', 'OrderController@export')->name('backend.oms.order.export');

        //退单申请
        Route::get('returnapply/index', 'ReturnapplyController@index')->name('backend.oms.returnapply');
        Route::any('returnapply/list', 'ReturnapplyController@list')->name('backend.oms.returnapply.list');
        Route::get('returnapply/edit', 'ReturnapplyController@edit')->name('backend.oms.returnapply.edit');
        Route::any('returnapply/returnApplyStatusChange', 'ReturnapplyController@returnApplyStatusChange')->name('backend.oms.returnapply.returnApplyStatusChange');
    });

    Route::group(['namespace' => 'Oms', 'prefix' => 'oms'], function () {

        Route::get('order/manager/index', 'OrderManagerController@index')->name('backend.oms.order.manager');
        Route::any('order/manager/list', 'OrderManagerController@list')->name('backend.oms.order.manager.list');
        Route::get('order/manager/edit', 'OrderManagerController@edit')->name('backend.oms.order.manager.edit');

    });

    //会员裂变
    Route::group(['namespace' => 'Member', 'prefix' => 'fission'], function () {

        Route::any('backend/memeber/fission', 'FissionController@list')->name('backend.fission');
        Route::any('backend/memeber/fissione/edit', 'FissionController@edit')->name('backend.fission.edit');
        Route::any('backend/fissionlist', 'FissionController@dataList')->name('backend.fission.dataList');
        Route::any('backend/fissionadd', 'FissionController@add')->name('backend.fission.add');
        Route::any('backend/fission/update', 'FissionController@update')->name('backend.fission.update');
        Route::any('backend/fission/view', 'FissionController@view')->name('backend.fission.view');
        Route::any('backend/fission/log', 'FissionController@log')->name('backend.fission.log');
        Route::any('backend/fission/active', 'FissionController@active')->name('backend.fission.active');
        Route::any('backend/fission/unactive', 'FissionController@unactive')->name('backend.fission.unactive');
        Route::any('backend/fission/detail', 'FissionController@detail')->name('backend.fission.detail');
    });

    //付邮
    Route::group(['namespace' => 'Trial', 'prefix' => 'trial'], function () {
        Route::any('backend/trial/index', 'TrialController@index')->name('backend.trial.index');
        Route::any('backend/trial/dataList', 'TrialController@dataList')->name('backend.trial.dataList');
        Route::any('backend/trial/add', 'TrialController@add')->name('backend.trial.add');
        Route::any('backend/trial/edit', 'TrialController@edit')->name('backend.trial.edit');
        Route::any('backend/trial/view', 'TrialController@view')->name('backend.trial.view');
        Route::any('backend/trial/update', 'TrialController@update')->name('backend.trial.update');
        Route::any('backend/trial/active', 'TrialController@active')->name('backend.trial.active');
        Route::any('backend/trial/unactive', 'TrialController@unactive')->name('backend.trial.unactive');
    });

    Route::group(['namespace' => 'Shipfee', 'prefix' => 'shipfee'], function () {
        Route::any('backend/shipfee/index', 'ShipfeeController@index')->name('backend.shipfee.index');
        Route::any('backend/shipfee/dataList', 'ShipfeeController@dataList')->name('backend.shipfee.dataList');
        Route::any('backend/shipfee/add', 'ShipfeeController@add')->name('backend.shipfee.add');
        Route::any('backend/shipfee/edit', 'ShipfeeController@edit')->name('backend.shipfee.edit');
        Route::any('backend/shipfee/view', 'ShipfeeController@view')->name('backend.shipfee.view');
        Route::any('backend/shipfee/update', 'ShipfeeController@update')->name('backend.shipfee.update');
    });

    Route::group(['namespace' => 'Dashboard', 'prefix' => 'dashboard'], function () {
        Route::any('allData', 'DashboardController@allData')->name('backend.dashboard.alldata');
        Route::any('index', 'DashboardController@index')->name('backend.dashboard.index');
        Route::any('product', 'ProductController@getData')->name('backend.dashboard.product');
        Route::any('miniapp', 'MiniController@getData')->name('backend.dashboard.miniapp');
        Route::any('analysis/getOrderReportData', 'AnalysisController@getOrderReportData')->name('backend.dashboard.analysis.orderReport');
    });

    Route::group(['namespace' => 'Coupon', 'prefix' => 'coupon'], function () {
        Route::any('couponsend/edit', 'CouponSendController@edit')->name('backend.coupon.couponsend.edit');
        Route::any('couponsend/update', 'CouponSendController@update')->name('backend.coupon.couponsend.update');

        Route::any('mycoupon/index', 'MyCouponController@index')->name('backend.coupon.mycoupon.index');
        Route::any('mycoupon/list', 'MyCouponController@dataList')->name('backend.coupon.mycoupon.dataList');
    });
    #储值卡
    Route::any('gold/index/index', 'Gold\IndexController@index')->name('backend.gold.index');
    Route::any('gold/index/list', 'Gold\IndexController@list')->name('backend.gold.list');
    Route::any('gold/index/changeStatus', 'Gold\IndexController@changeStatus')->name('backend.gold.changeStatus');
    Route::any('gold/index/delete', 'Gold\IndexController@delete')->name('backend.gold.delete');
    Route::any('gold/index/add', 'Gold\IndexController@add')->name('backend.gold.add');
    Route::any('gold/index/insert', 'Gold\IndexController@insert')->name('backend.gold.insert');
    Route::any('gold/index/recharge', 'Gold\IndexController@recharge')->name('backend.gold.recharge');
    Route::any('gold/order/list', 'Gold\IndexController@order')->name('backend.gold.order');
    Route::any('gold/order/getOrderList', 'Gold\IndexController@getOrderList')->name('backend.gold.getOrderList');
    Route::any('gold/index/userBalanceLog', 'Gold\IndexController@userBalanceLog')->name('backend.gold.userBalanceLog');
    Route::any('gold/index/getUserBalanceLogs', 'Gold\IndexController@getUserBalanceLogs')->name('backend.gold.getUserBalanceLogs');
    Route::any('gold/index/exportLog', 'Gold\IndexController@exportLog')->name('backend.gold.exportLog');
    Route::any('gold/index/userBalanceList', 'Gold\IndexController@userBalanceList')->name('backend.gold.userBalanceList');
    Route::any('gold/index/getUserBalanceList', 'Gold\IndexController@getUserBalanceList')->name('backend.gold.getUserBalanceList');
    Route::any('gold/index/refund', 'Gold\IndexController@refund')->name('backend.gold.refund');
    Route::any('gold/index/invoice', 'Gold\IndexController@invoice')->name('backend.gold.invoice');

});
