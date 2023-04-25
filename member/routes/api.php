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


//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['namespace' => 'Api'], function () {
    Route::any('importCoupon', 'CouponController@importCoupon');
    Route::any('user/getMemberInfo', 'UserNoLoginController@getMemberInfo');
    Route::any('social/list', 'CustomerLoginController@socialList');
    Route::any('send/msg', 'CustomerLoginController@sendMsg');
    Route::any('user/exist', 'CustomerLoginController@userExist');
    Route::any('user/register', 'CustomerLoginController@registerUser');
    Route::any('login/password', 'CustomerLoginController@loginByPassword');
    Route::any('get/captcha', 'CustomerLoginController@getCaptcha');
    Route::any('check/captcha', 'CustomerLoginController@checkCaptcha');
    Route::any('login/phone', 'CustomerLoginController@loginByPhone');
    Route::any('user/login', 'CustomerLoginController@login');
    Route::any('set/password', 'CustomerLoginController@setPassword');
    Route::any('sso/{socialite_name}', 'SocialCallbackController@sso');
    Route::any('get/socialite/{socialite_name}', 'SocialCallbackController@getSocialite');
    Route::post('approach/info', 'UserController@approachInfo');
//    Route::get('user/account', 'UserController@userCenter');
    Route::post('social/login', 'SocialCallbackController@socialLogin');
    #####################################################################################
    Route::any('user/account', 'Dlc\UserController@userCenter');
    Route::any('userinfo/show', 'Dlc\UserController@userInfoShow');
    Route::any('userinfo/update', 'Dlc\UserController@userInfoUpdate');
    Route::any('user/getByOpenId', 'Dlc\UserController@getMemberInfoByOpenId');
    Route::any('user/getUserTypeByUid', 'Dlc\UserController@getUserTypeByUid');
    Route::any('inner/user/getPosIdByUid', 'Dlc\UserController@getPosIdByUid');
    Route::any('member/exportMember', 'Dlc\UserController@exportMember');

    #储值卡
    Route::any('user/getBalance', 'Dlc\UserController@getBalance');
    Route::any('user/getBalanceList', 'Dlc\BalanceController@getBalanceList');
    Route::any('user/getUserBalanceList', 'Dlc\BalanceController@getUserBalanceList');
    Route::any('user/getBalanceInfo', 'Dlc\BalanceController@getBalanceInfo');
    Route::any('user/setBalanceInvoice', 'Dlc\BalanceController@setBalanceInvoice');
    Route::any('user/invoice', 'Dlc\BalanceController@invoice');
    Route::any('user/refundBalanceCard', 'Dlc\BalanceController@refundBalanceCard');
    Route::any('balance/applyRefund', 'Dlc\BalanceController@applyRefund');
    Route::any('user/refundBalance', 'Dlc\BalanceController@refundBalance');
    Route::any('user/addBalance', 'Dlc\BalanceController@addBalance');
    Route::any('user/useBalance', 'Dlc\BalanceController@useBalance');
    Route::any('user/getBalanceLogs', 'Dlc\BalanceController@getBalanceLogs');
    Route::any('user/exportBalanceLogs', 'Dlc\BalanceController@exportBalanceLogs');
    Route::any('balance/cancelInvoice', 'Dlc\BalanceController@cancelInvoice');
    Route::any('balance/applyInvoice', 'Dlc\BalanceController@applyInvoice');
    Route::any('balance/exportBalanceLog', 'Dlc\BalanceController@exportBalanceLog');
});

Route::group([
    'namespace' => 'Api'
    , 'middleware' => 'self.api'
], function () {


//    Route::any('member/social/callback/{socialName}', 'SocialCallbackController@socialCallback');
//     // 会员列表- 分页
//    Route::get('member/delete', 'TestController@test');
//

//
//    // 小程序登录， 获取openid
//    Route::post('getOpenid', 'WxController@signin');
//    // 获取手机号
//    Route::post('getWxPhone', 'WxController@getPhoneNumber');
//    // 注册周友
//    Route::any('registSet', 'RegisterController@create');
//    // 更新密码
//    Route::any('forgotSet', 'RegisterController@forgetPassword');
//    // 通过FamilyName + FirstName 判断时候周友
//    Route::any('forgot', 'RegisterController@customerByInfo');
//    // 通过unionid 验证是否周友
//    Route::post('checkBind', 'RegisterController@customerByUnionId');
//
//    // 手机号 发送验证码
//    Route::any('sendSMS', 'SmsController@sendByPhone');
//    // 手机号 + 验证码 校验
//    Route::any('regist', 'SmsController@smsForm');
//    // 周友登录
//    Route::post('login', 'LoginController@login');
//    // 金价
//    Route::any('getGold', 'GoldPriceController@index');
//    // 是否有资格领取
//    Route::post('checkNew', 'CouponController@isAccept');
//    // 记录领取（拒绝）优惠券行为
//    Route::any('addCoupon', 'CouponController@action');
//    // 使用优惠券
//    Route::any('useConpon', 'MemberController@useConpon');
//    // 标记新客优惠券
//    Route::any('useNewerCoupon', 'MemberController@useNewerCoupon');
//    //取消订单归还优惠券
//    Route::any('revertCoupon', 'MemberController@revertCoupon');
//    // 增加历史记录
//    Route::post('addHistory', 'BrowseHistoryController@add');
    // 收藏
    Route::any('addFav', 'FavoriteController@collect');
    // 取消收藏
    Route::any('delFav', 'FavoriteController@cancel');
//    // 最近6个收藏，return proIdList
//    Route::post('showRecently', 'FavoriteController@showRecently');
//    // 最近6个浏览，同上
//    Route::post('showRecentlyBrowse', 'BrowseHistoryController@showRecentlyBrowse');
    // 收藏列表
    Route::any('showFav', 'FavoriteController@show');
    Route::any('getFavPids', 'FavoriteController@getFavPids');
    Route::any('fav/getPagePids', 'FavoriteController@getPagePids');
//    Route::any('favList', 'FavoriteController@show');

    //地址管理
    Route::any('addAddress', 'UserController@addAddress');
    Route::any('updateAddress', 'UserController@updateAddress');
    Route::any('delAddress', 'UserController@delAddress');
    Route::any('showAddress', 'UserController@showAddress');
    Route::any('setDefaultAddress', 'UserController@setDefaultAddress');

    //足迹管理
    Route::any('addFootprint', 'FootprintController@add');
    Route::any('showFootprint', 'FootprintController@show');
    Route::any('footprint/getPagePids', 'FootprintController@getPagePids');


//  优惠券列表
    Route::any('couponList', 'CouponController@list');  //有效优惠券user

    Route::any('allCoupons', 'CouponController@allCoupons');  //我的所有优惠券
    Route::any('addCoupon', 'CouponController@action');

//
//    Route::middleware(['crm.login'])->group(function () {
//        // 会员中心-获取会员信息
//        Route::any('account', 'CustomerController@account');
//        // 会员中心-获取会员二维码
//        Route::any('getQrcode', 'CustomerController@myQrcode');
//
//        // 失效优惠券列表
//        Route::any('couponDisableList', 'CouponController@failList');
//
//        // 历史记录
//        Route::post('historyList', 'BrowseHistoryController@show');
//
//        // 获取会员信息
//        Route::post('getCustomerInfo', 'CustomerController@getCustomerInfo');
//
//        // 检测是否登录状态
//        Route::post('checkLogin', 'RegisterController@checkLogin');
//    });
//    Route::post('crm/goldPrice', 'WarningController@goldPrice');
    Route::post('user/logout', 'CustomerLoginController@logout');
    Route::post('mark/activity', 'UserController@saveActivity');


});
//微信
Route::group(['namespace' => 'Api'], function () {
//    Route::post('wx/login', 'WxController@signin');
    Route::post('wx/login/test', 'WxController@signinTest');
    Route::post('decode/mobile', 'WxController@getPhoneNumber');
    Route::post('wx/register', 'WxController@bingUser');
    Route::post('wx/account/login', 'WxController@wxLogin');
//    Route::post('wx/phone/login', 'WxController@wxPhoneLogin');
####Dlc###############################################################################
    Route::post('wxLogin', 'Dlc\WxController@wxLogin');//获取sessionkey
    Route::post('wx/login', 'Dlc\WxController@signin');
    Route::post('wx/phone/login', 'Dlc\WxController@wxPhoneLogin');
    Route::post('wx/getQrCode', 'Dlc\WxController@getQrCode');
    //分享裂变
    Route::any('share/bind', 'Share\ShareController@bind');
    Route::any('share/notify', 'Share\ShareController@notify');
});
//后台
Route::group(['namespace' => 'Api'], function () {
    // 会员列表- excel导出
    Route::post('member/excel/list', 'MemberController@export');
    // 会员详情
    Route::post('member/detail', 'MemberController@detail');
    // 会员删除
    Route::any('member/list', 'MemberController@pageList');

    Route::any('member/destroy', 'MemberController@destroy');
    Route::any('member/mergeSlaveMemberIntoMasterMember', 'MemberController@mergeSlaveMemberIntoMasterMember');
    Route::any('member/getSlaveAndMasterMember', 'MemberController@getSlaveAndMasterMember');
    Route::any('member/getUserInfo', 'MemberController@getUserInfo');
    Route::any('member/getUserInfoByOpenid', 'MemberController@getUserInfoByOpenid');
    Route::any('member/getUserInfoByUserId', 'MemberController@getUserInfoByUserId');


});


Route::group(['namespace' => 'Api'], function () {
    Route::any('pointmall/list', 'PointmallController@list');
    Route::any('pointmall/convert', 'PointmallController@convert');
    Route::any('pointmall/my/convert', 'PointmallController@myconvert');
    Route::any('pointmall/getDetail', 'PointmallController@getDetail');
    Route::any('pointmall/paysuccess', 'PointmallController@paysuccess');
    Route::any('pointmall/cancelOrder', 'PointmallController@cancelOrder');
    Route::any('pointmall/dataList', 'PointmallController@dataList');
    Route::any('pointmall/get', 'PointmallController@get');
    Route::any('pointmall/post', 'PointmallController@post');
    Route::any('pointmall/token', 'PointmallController@token');
    Route::any('useCoupon', 'CouponController@useCoupon');  //使用优惠券
    Route::any('revertCoupon', 'CouponController@revertCoupon');  //回退优惠券
    Route::any('delCoupon', 'CouponController@delCoupon');  //删除优惠券
    Route::any('apiGrantCoupon', 'CouponController@apiGrantCoupon');  //内部接口发放优惠券
    Route::any('apiGetUserCouponInfo', 'CouponController@apiGetUserCouponInfo');  //内部接口发放优惠券
    Route::any('user/exists', 'UserController@userExist');  //内部接口发放优惠券
    Route::any('user/innerGetUserAddress', 'UserController@innerGetUserAddress');
    Route::post('user/exportUserHistory', 'UserController@exportUserHistory');
});

Route::group(['namespace' => 'Api'], function () {
    Route::any('dashboard', 'DashBoardController@index');
});


Route::group(['namespace' => 'Api'], function () {
    //会员裂变
    Route::any('fission/edit', 'FissionController@edit');
    Route::any('fission/add', 'FissionController@add');
    Route::any('fission/dataList', 'FissionController@dataList');
    Route::any('fission/detail', 'FissionController@detail');
    Route::any('fission/registerAdd', 'FissionController@registerAdd');
    Route::any('fission/getFissionRank', 'FissionController@getFissionRank');
    Route::any('fission/active', 'FissionController@active');
    Route::any('fission/info', 'FissionController@getFissionInfo');

    //导购 太阳码
    Route::any('getShareImg', 'Advert\GuideController@getGuideMiniCode');
    Route::any('readShare', 'Advert\GuideController@getGuideInfo');
    Route::any('whiteShareId', 'Advert\GuideController@getGuideMini');
    Route::any('recordEmpId', 'Advert\EmployeeController@recordEmpId');
    Route::any('StoreList', 'StoreController@StoreList');
    Route::any('getGuideService', 'Advert\GuideController@getGuideService');

    //导购信息
    Route::any('store/getSearchData', 'StoreController@getSearchData');
    Route::any('store/guideStoreCityTop', 'StoreController@guideStoreCityTop');
    Route::any('store/goodsSalesVolumeTop', 'StoreController@goodsSalesVolumeTop');
    Route::any('store/typeSaleVolumeTop', 'StoreController@typeSaleVolumeTop');
    Route::any('store/channelSaleVolumeTop', 'StoreController@channelSaleVolumeTop');
    Route::any('store/guideRegisterUserCount', 'StoreController@guideRegisterUserCount');
    Route::any('store/getStoreFromCity', 'StoreController@getStoreFromCity');
    Route::any('store/dashBoardNeedData', 'StoreController@dashBoardNeedData');
    Route::any('store/getAllStoreData', 'StoreController@getAllStoreData');
    Route::any('bar_pop', 'NotifyBarController@PopBar');
    Route::any('bar/info', 'NotifyBarController@info');
    Route::any('bar/add', 'NotifyBarController@add');
    Route::any('pop/info', 'NotifyBarController@infoPop');
    Route::any('pop/add', 'NotifyBarController@addPop');
    /* Route::any('store/list', 'StoreController@list');
     Route::post('store/get', 'StoreController@getStore');
     Route::any('store/update', 'StoreController@updateStore');

     Route::any('employee/list', 'EmployeeController@list');
     Route::post('employee/get', 'EmployeeController@getEmployee');
     Route::any('employee/update', 'EmployeeController@updateEmployee');

     Route::any('role/list', 'RoleController@list');
     Route::post('role/get', 'RoleController@getRole');
     Route::any('role/update', 'RoleController@updateRole'); */
});


Route::group(['namespace' => 'Backend'], function () {
//'middleware' => 'self.api'
    Route::any('store/list', 'StoreController@list');
    Route::post('store/get', 'StoreController@getStore');
    Route::any('store/update', 'StoreController@updateStore');
    Route::any('store/AllList', 'StoreController@AllList');

    Route::any('employee/list', 'EmployeeController@list');
    Route::any('employee/AllList', 'EmployeeController@AllList');
    Route::post('employee/get', 'EmployeeController@getEmployee');
    Route::any('employee/update', 'EmployeeController@updateEmployee');
    Route::any('employee/realTimeGuideInfo', 'EmployeeController@realTimeGuideInfo');
    Route::any('employee/bindAll', 'EmployeeController@bindAll');
    Route::any('role/list', 'RoleController@list');
    Route::post('role/get', 'RoleController@getRole');
    Route::any('role/update', 'RoleController@updateRole');
    Route::any('role/AllList', 'RoleController@AllList');


});




