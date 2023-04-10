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
    'namespace' => 'Api',
], function () {
    //后台接口
    Route::any('goods/category/list', 'Goods\CategoryController@list');
    Route::any('goods/category/getCate', 'Goods\CategoryController@getCate');
    Route::any('goods/category/addCate', 'Goods\CategoryController@addCate');
    Route::any('goods/category/editCate', 'Goods\CategoryController@editCate');
    Route::any('goods/category/relateProds', 'Goods\CategoryController@relateProds');
    Route::any('goods/category/addProducts', 'Goods\CategoryController@addProducts');
    Route::any('goods/category/delProduct', 'Goods\CategoryController@delProduct');

    Route::any('goods/category/editRelateProds', 'Goods\CategoryController@editRelateProds');
    Route::any('goods/category/pCateList', 'Goods\CategoryController@pCateList');
    Route::any('goods/category/pCateListNoSub', 'Goods\CategoryController@pCateListNoSub');
    Route::any('goods/category/calculateProds', 'Goods\CategoryController@calculateProds');
    Route::any('goods/category/offCat', 'Goods\CategoryController@offCat');
    Route::any('goods/category/upCat', 'Goods\CategoryController@upCat');
    Route::any('goods/category/handleCatSortCsv', 'Goods\CategoryController@handleCatSortCsv');
    Route::any('goods/category/batchChangeSort', 'Goods\CategoryController@batchChangeSort');

    Route::any('goods/common/getProdTypelist', 'Goods\CommonController@getProdTypelist');
    Route::any('goods/common/getBrandColl', 'Goods\CommonController@getBrandColl');
    Route::any('goods/common/getUsage', 'Goods\CommonController@getUsage');
    Route::any('goods/inner/getProdInfoByIds', 'Goods\InnerProductController@getProdInfoByIds');
    Route::any('goods/inner/getSearchProdsByIds', 'Goods\InnerProductController@getSearchProdsByIds');
    Route::any('goods/inner/setVirtualCate', 'Goods\InnerProductController@setVirtualCate');
    Route::any('goods/inner/storeStock', 'Goods\InnerProductController@storeStock');
    Route::any('goods/inner/revertStock', 'Goods\InnerProductController@revertStock');
    Route::any('goods/inner/collectSales', 'Goods\InnerProductController@collectSales');
    Route::any('goods/inner/getDoorsBySkuId', 'Goods\InnerProductController@getDoorsBySkuId');
    Route::any('goods/spu/list', 'Goods\SpuController@list');
    Route::any('goods/spu/test', 'Goods\SpuController@test');
    Route::any('goods/spu/getCatProdAndColleList', 'Goods\SpuController@getCatProdAndColleList');
    Route::any('goods/spu/updateCatRelation', 'Goods\SpuController@updateCatRelation');
    Route::any('goods/spu/getProdAndCollList', 'Goods\SpuController@getProdAndCollList');
    Route::any('goods/spu/backList', 'Goods\SpuController@backList');
    Route::any('goods/spu/checkSpec', 'Goods\SpuController@checkSpec');
    Route::any('goods/spu/getProdOrCollList', 'Goods\SpuController@getProdOrCollList');
    Route::any('goods/spu/getProd', 'Goods\SpuController@getProd');
    Route::any('goods/spu/editProd', 'Goods\SpuController@editProd');
    Route::any('goods/spu/add', 'Goods\SpuController@add');
    Route::any('goods/spu/changeStatus', 'Goods\SpuController@changeStatus');
    Route::any('goods/spu/relateSkus', 'Goods\SpuController@relateSkus');
    Route::any('goods/spu/relateDoorSkus', 'Goods\SpuController@relateDoorSkus');
    Route::any('goods/spu/editRelateSkus', 'Goods\SpuController@editRelateSkus');
    Route::any('goods/spu/changeDisplay', 'Goods\SpuController@changeDisplay');
    Route::any('goods/spu/rawData', 'Goods\SpuController@rawData');
    Route::any('goods/spu/createCharme', 'Goods\SpuController@createCharme');
    Route::any('goods/spu/handleCsv', 'Goods\SpuController@handleCsv');
    Route::any('goods/spu/saveDetail', 'Goods\SpuController@saveDetail');
    Route::any('goods/spu/getDetail', 'Goods\SpuController@getDetail');
    Route::any('goods/spp/list', 'Goods\SppController@list');
    Route::any('goods/spp/add', 'Goods\SppController@add');
    Route::any('goods/spp/edit', 'Goods\SppController@edit');
    Route::any('goods/spp/del', 'Goods\SppController@del');
    Route::any('goods/spp/getSppRule', 'Goods\SppController@getSppRule');

    Route::any('ad/loc/list', 'Ad\LocationController@list');
    Route::any('ad/loc/insert', 'Ad\LocationController@insert');
    Route::any('ad/loc/update', 'Ad\LocationController@update');
    Route::any('ad/loc/get', 'Ad\LocationController@getLoc');

    Route::any('ad/item/list', 'Ad\ItemController@list');
    Route::any('ad/item/insert', 'Ad\ItemController@insert');
    Route::any('ad/item/update', 'Ad\ItemController@update');
    Route::any('ad/item/delete', 'Ad\ItemController@delete');
    Route::any('ad/item/get', 'Ad\ItemController@getItem');

    Route::any('goods/sku/list', 'Goods\SkuController@list');
    Route::any('goods/sku/add', 'Goods\SkuController@add');
    Route::any('goods/sku/getSku', 'Goods\SkuController@getSku');
    Route::any('goods/sku/editSku', 'Goods\SkuController@editSku');
    Route::any('goods/sku/stock', 'Goods\SkuController@stock');
    Route::any('goods/sku/updateStock', 'Goods\SkuController@updateStock');
    Route::any('goods/sku/saveDetail', 'Goods\SkuController@saveDetail');
    Route::any('goods/sku/getDetail', 'Goods\SkuController@getDetail');
    Route::any('goods/sku/all', 'Goods\SkuController@stockAll');
    Route::any('goods/sku/infoAll', 'Goods\SkuController@exportSkuInfo');

    Route::any('goods/doorSku/list', 'Goods\DoorSkuController@list');
    Route::any('goods/doorSku/getSku', 'Goods\DoorSkuController@getSku');
    Route::any('goods/doorSku/editSku', 'Goods\DoorSkuController@editSku');
    Route::any('pdtListold', 'Goods\ProductController@getCategory');
    Route::any('pdtDetail', 'Goods\ProductController@getProduct');
    Route::any('storePdt', 'Goods\ProductController@getDoorProduct');
    Route::any('goods/product/getCategoryTree', 'Goods\ProductController@getCategoryTree');
    Route::any('goods/product/getInfoBySkuIds', 'Goods\InnerProductController@getInfoBySkuIds');
    Route::any('goods/product/checkSkusStock', 'Goods\InnerProductController@checkSkusStock');
    Route::any('recommend', 'Goods\ProductController@recommend');
    Route::any('pddReport', 'Goods\ReportController@pdtView');
    Route::any('shareReport', 'Goods\ReportController@pdtShare');
    Route::any('report/catView', 'Goods\ReportController@catView');
    Route::any('report/addCart', 'Goods\ReportController@addCart');
    Route::any('promotionList', 'Goods\ProductController@getRuleCate');
    Route::any('goods/common/getBrandCollection', 'Goods\CommonController@getBrandCollection');
    Route::any('goods/recommend/list', 'Goods\RecommendController@list');
    Route::any('goods/recommend/add', 'Goods\RecommendController@addRec');
    Route::any('goods/recommend/changeStatus', 'Goods\RecommendController@changeStatus');

    //ES开发测试接口
    Route::any('keywordList', 'Search\GatewayController@searchProduct');
    Route::any('search/getFilter', 'Search\GatewayController@getFilter');
    Route::any('prodView', 'Search\ProdStatisticsController@prodViewCount');
    Route::any('prodAddcart', 'Search\ProdStatisticsController@prodAddcartCount');
    Route::any('prodShare', 'Search\ProdStatisticsController@prodShareCount');
    Route::any('prodFavorite', 'Search\ProdStatisticsController@prodFavoriteCount');
    Route::any('prodKeywords', 'Search\ProdStatisticsController@prodKeywordCount');

    Route::post('search/setbalcklist', 'Search\GatewayController@setBlackList');
    Route::post('search/setcatalog', 'Search\GatewayController@setCataLog');
    Route::post('search/producttoes', 'Search\GatewayController@proudctToEs');
    Route::post('search/jmetertoes', 'Search\GatewayController@jmterToEs');
    Route::post('pdtList', 'Search\GatewayController@searchCatalog');
    Route::post('search/redirecttoredis', 'Search\GatewayController@setRedirectToRedis');
    Route::any('search/getHotKeywords', 'Search\GatewayController@getHotKeywords');

    #搜索管理接口
//    Route::any('search/addblacklist', 'Search\ManageController@addBlackList');
//    Route::any('search/delblacklist', 'Search\ManageController@delBlackList');
//    Route::any('search/blacklist', 'Search\ManageController@blacklist');

    Route::any('search/addblackList', 'Search\BlacklistController@addBlackList');
    Route::any('search/delblackList', 'Search\BlacklistController@delBlackList');
    Route::any('search/blacklist', 'Search\BlacklistController@blacklist');
    Route::any('search/list', 'Search\BlacklistController@list');

//    Route::any('search/addRedirect', 'Goods\SearchController@addRedirect');
//    Route::any('search/delRedirect', 'Goods\SearchController@delRedirect');
//    Route::any('search/updateRedirect', 'Goods\SearchController@updateRedirect');
//    Route::any('search/redirectList', 'Goods\SearchController@redirectList');
//    Route::any('search/getRedirectInfo', 'Goods\SearchController@getRedirectInfo');

    Route::any('search/addRedirect', 'Search\RedirectController@addRedirect');
    Route::any('search/delRedirect', 'Search\RedirectController@delRedirect');
    Route::any('search/updateRedirect', 'Search\RedirectController@updateRedirect');
    Route::any('search/redirectList', 'Search\RedirectController@redirectList');
    Route::any('search/getRedirectInfo', 'Search\RedirectController@getRedirectInfo');

    Route::any('search/addSynonym', 'Search\SynonymController@addSynonym');
    Route::any('search/delSynonym', 'Search\SynonymController@delSynonym');
    Route::any('search/updateSynonym', 'Search\SynonymController@updateSynonym');
    Route::any('search/synonymList', 'Search\SynonymController@synonymList');
    Route::any('search/getSynonymInfo', 'Search\SynonymController@getSynonymInfo');
    Route::any('search/getAllSynonym', 'Search\SynonymController@getAllSynonym');


    //店铺接口
    Route::any('goods/store/redis', 'Store\LocationController@redis');
    Route::any('storeList', 'Store\LocationController@storeList');
    Route::any('storeInit', 'Store\LocationController@storeInit');
    Route::any('getCity', 'Store\LocationController@getCity');
    Route::any('storeInitCheck', 'Store\LocationController@storeInitCheck');
    Route::any('storeInventoryList', 'Store\LocationController@storeInventoryList');
    Route::post('reportAnalytics', 'Search\ReportController@reportAnalytics');

    //商品集合接口
    Route::any('goods/collection/add', 'Goods\CollectionController@add');
    Route::any('goods/collection/detail', 'Goods\CollectionController@detail');
    Route::any('goods/collection/update', 'Goods\CollectionController@update');
    Route::any('goods/collection/list', 'Goods\CollectionController@list');
    Route::any('goods/collection/changeStatus', 'Goods\CollectionController@changeStatus');
    Route::any('goods/collection/getFormatedProductList', 'Goods\CollectionController@getFormatedProductList');
    Route::any('goods/collection/saveDetail', 'Goods\CollectionController@saveDetail');
    Route::any('goods/collection/getDetail', 'Goods\CollectionController@getDetail');



    Route::any('goods/channel/update', 'Goods\SkuController@updateChannelStock');
    Route::any('goods/channel/updatesecure', 'Goods\SkuController@updateSkuSecure');
    Route::any('goods/stock/log', 'Goods\SkuController@stockLoglist');
    Route::any('goods/insertStock', 'Goods\SkuController@insertStock');

    #肌肤测试接口
    Route::any('skin/miniSkinTest', 'Skin\SkinAnalysisController@miniSkinTest');
    Route::any('skin/reportList', 'Skin\SkinAnalysisController@hisotryReportList');
    Route::any('skin/reportDetail', 'Skin\SkinAnalysisController@historyReportDetail');
    Route::any('skin/recProduct', 'Skin\SkinAnalysisController@productsRecommended');
    Route::get('skin/getOssToken', 'Skin\OssTokenController@getOssToken');
    Route::get('skin/ossCallback', 'Skin\OssTokenController@ossCallback');

    Route::post('goods/exportSkuHistory', 'Goods\SkuController@exportSkuHistory');
    Route::post('goods/exportSalesInfoHistory', 'Goods\SkuController@exportSalesInfoHistory');
    Route::post('goods/exportSpuHistory', 'Goods\SpuController@exportSpuHistory');
    Route::post('goods/exportCategoriesHistory', 'Goods\CategoryController@exportCategoriesHistory');

    ##DLC############################################################################
    Route::any('goods/category/getList', 'Goods\CategoryController@getTreeList');
    //首页活动轮播和活动分类
    Route::any('home/event', 'Goods\CategoryController@getEvent');
    Route::any('ad/get', 'Goods\CategoryController@getAd');
    //获取明星产品
    Route::any('goods/category/getStarProducts', 'Goods\CategoryController@getStarProducts');
});



Route::group([
    'namespace' => 'Outward',
], function () {
    Route::any('outward/product/getProductList', 'Goods\ProductController@getProductList');
    Route::any('outward/product/recommend', 'Goods\ProductController@recommend');
    Route::any('outward/product/hotSale', 'Goods\ProductController@hotSale');
    Route::any('outward/product/getProduct', 'Goods\ProductController@getProduct');
    Route::any('outward/product/getCatInfo', 'Goods\ProductController@getCatInfo');
    Route::any('outward/product/getProductInfoBySkuId', 'Goods\ProductController@getProductInfoBySkuId');
    Route::any('outward/product/getProductInfoBySkuIds', 'Goods\ProductController@getProductInfoBySkuIds');
    Route::any('outward/get/stock', 'Goods\SkuStockController@getStock');
    Route::any('outward/get/batchStock', 'Goods\SkuStockController@getBatchStock');
    Route::any('outward/update/batchStock', 'Goods\SkuStockController@updateBatchStock');
    Route::any('outward/update/batchStockForce', 'Goods\SkuStockController@updateBatchStockForce');
    Route::any('outward/update/stock', 'Goods\SkuStockController@updateStock');
    Route::any('outward/batch/unLockStock', 'Goods\SkuStockController@batchUnlockSku');
    ##DLC##############################################################################
    Route::any('outward/update/batchStockFull', 'Goods\SkuStockController@updateBatchStockFull');
    Route::any('outward/update/batchPrice', 'Goods\SkuStockController@updateBatchPrice');
    //商品初始化导入
    Route::any('goods/data/import', 'Goods\DataController@import');
    //缓存刷新
    Route::any('goods/cache/clear', 'Goods\CacheController@clear');
    Route::any('inner/addSalesVolume', 'Goods\ProductController@addSalesVolume');
});
