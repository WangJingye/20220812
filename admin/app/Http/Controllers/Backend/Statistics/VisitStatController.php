<?php
/**
 *  ===========================================
 *  File Name   VisitTrendStatController.php
 *  Class Name  admin
 *  Date:       2019-10-30 09:57
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Http\Controllers\Backend\Statistics;


use App\Http\Controllers\Backend\Controller;
use App\Model\WxSmallRetainDaily;
use App\Model\WxSmallRetainMonthly;
use App\Model\WxSmallRetainWeekly;
use App\Model\WxSmallUserPortraitDaily;
use App\Model\WxSmallUserPortraitWeekly;
use App\Model\WxSmallUserPortraitMonthly;
use App\Model\WxSmallVisitDistribution;
use App\Model\WxSmallVisitPage;
use Illuminate\Http\Request;

class VisitStatController extends Controller
{
    static $replace_array = [
        "pages/pdt/pdt-list/pdt-list" => "产品列表页pdt-list",
        "pages/pdt/pdt-detail/pdt-detail" => "产品详情页pdt-detail",
        "pages/pdt/category/category" => "产品分类页category",
        "pages/order/order-list/order-list" => "订单列表页order-list",
        "pages/order/order-detail/order-detail" => "订单详情页order-detail",
        "pages/order/edit-nopay/edit-nopay" => "未付款订单编辑edit-nopay",
        "pages/cart/shopping-cart/shopping-cart" => "购物车shopping-cart",
        "pages/cart/checkout/checkout" => "购物车结算页checkout",
        "pages/cart/check-address/check-address" => "结算时选择地址check-address",
        "pages/cart/check-point/check-point" => "结算时选择积分check-point",
        "pages/cart/check-offer/check-offer" => "结算时使用兑换码check-offer",
        "pages/cart/check-store/check-store" => "结算时选择门店check-store",
        "pages/cart/check-card/check-card" => "结算时礼品卡check-card",
        "pages/cart/pay-success/pay-success" => "支付成功pay-success",
        "pages/center/account/account" => "个人中心-我的账户account",
        "pages/center/fav/fav" => "个人中心-收藏夹fav",
        "pages/center/coupon/coupon" => "个人中心-优惠券coupon",
        "pages/center/coupon-disable/coupon-disable" => "个人中心-优惠券-失效coupon-disable",
        "pages/center/history/history" => "个人中心-足迹history",
        "pages/center/qrcode/qrcode" => "个人中心-会员身份码qrcode",
        "pages/support/store-search/store-search" => "查询门店store-search",
        "pages/support/com-recommend/com-recommend" => "推荐产品清单com-recommend",
        "pages/support/com-promotion/com-promotion" => "推广素材分享com-promotion",
        "pages/account/login/login" => "账户登陆login",
        "pages/account/regist/regist" => "账户-注册regist",
        "pages/account/regist-set/regist-set" => "设置密码regist-set",
        "pages/account/reset/reset" => "填写重置密码资料reset",
        "pages/account/reset-set/reset-set" => "重置账户密码reset-set",
        "pages/account/success/success" => "注册成功success",
        "pages/default/home/home" => "商城首页home",
        "pages/default/template/template" => "默认-模板",
        "pages/default/web-view/web-view" => "默认-跳转外链",
        "pages/default/web-image/web-image" => "默认-展示图片",
        "pages/default/landing/landing" => "活动页landing"
    ];

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('backend.statistics.visit.index');
    }
    
    /**
     * 用户画像分布
     *
     * @version
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author  inctone(2019-11-04)
     */
    public function userPortrait()
    {
        return view('backend.statistics.visit.user_portrait');
    }
    
    /**
     * 页面访问统计
     *
     * @version
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author  inctone(2019-11-04)
     */
    public function pageStat()
    {
        return view('backend.statistics.visit.page_stats');
    }
    
    /**
     * 地区分布
     *
     * @version
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author  inctone(2019-11-04)
     */
    public function visitDist()
    {
        return view('backend.statistics.visit.visit_dist');
    }
    
    /**
     * 获取页面统计
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-04)
     */
    public function getPageStateData(Request $request)
    {
        $paginate = $request->get('page',20);
        $searchDate = $request->get('searchDate',  null);
        if(!$searchDate){
            $searchDate =  date('Y-m-d', strtotime('-1 day'));
        }
        $searchSort = $request->get('searchSort', 'page_visit_uv');
        //$list       = WxSmallVisitPage::where('ref_date', $searchDate)->paginate(10);
        if ($searchDate) {
            $list = WxSmallVisitPage::where('ref_date', $searchDate)
                                    ->orderBy('ref_date', 'desc')
                                    ->orderBy($searchSort, 'desc')
                                    ->limit($paginate)
                                    ->get();
        }
        else {
            $list = WxSmallVisitPage::orderBy('ref_date', 'desc')->orderBy($searchSort, 'desc')->limit($paginate)->get();
        }
        $reqData['label'] = $searchSort;
        if (!empty($list)) {
            $list = $list->toArray();
            
            $chartInfo = [];
            foreach ($list AS $value) {
                $chartInfo[$value['page_path']] = $value[$searchSort];
            }
            
            $reqData['ref_date'] = last($list)['ref_date'];
            $reqData['labels']   = array_keys($chartInfo);
            $reqData['lab_data'] = array_values($chartInfo);
            $reqData['list']     = $list;
        }
        else {
            $reqData['ref_date'] = $searchDate;
            $reqData['labels']   = ['暂无数据'];
            $reqData['lab_data'] = [0];
            $reqData['list']     = [];
        }


        $labels = $reqData['labels'];
        $list   = $reqData['list'];
        $new_lsit = $this->proccessList($list);
        $new_lables = $this->proccessLables($labels);
        $reqData['labels'] = $new_lables;
        $reqData['list'] = $new_lsit;

        return $this->responseJson(200, $searchDate . '数据查询成功', $reqData);
    }
    
    /**
     * 获取用户画像分布
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-04)
     */
    public function getUserPortrainData(Request $request)
    {
        $refDateEnd = $request->get('searchDate',null);
        if (!$refDateEnd){
            $refDateEnd = date('Y-m-d', strtotime('-1 day'));
        }
        $searchDays   = $request->get('searchDays', 0);
        $yesterday     = date('Y-m-d', strtotime('-1 day'));
        //dump($yestoday);
        if ($refDateEnd > $yesterday) {
            return $this->responseJson(400, '所选择日期数据尚未更新');
        }
        $refDateStart = date('Y-m-d', strtotime($refDateEnd.' - '.$searchDays.' day'));
        switch ($searchDays){
            case "0" :
                $list = WxSmallUserPortraitDaily::where('ref_date_start', $refDateStart)->where('ref_date_end', $refDateEnd)->get();
                $message = $refDateStart;
                break;
            case "6" :
                $list = WxSmallUserPortraitWeekly::where('ref_date_start', $refDateStart)->where('ref_date_end', $refDateEnd)->get();
                $message = $refDateStart . "至" . $refDateEnd;
                break;
            case "29":
                $list = WxSmallUserPortraitMonthly::where('ref_date_start', $refDateStart)->where('ref_date_end', $refDateEnd)->get();
                $message = $refDateStart . "至" . $refDateEnd;
                break;
            default:
                $list = ["result"=> null];
                $message = $refDateStart;
                break;
        }
        //$list = con
        if (empty($list)) {
            return $this->responseJson(400, $message . '暂无数据');
        }
        $userList = $newUserList = [];
        $times    = '';
        foreach ($list AS $val) {
            $times = $val['ref_date_start'].'至'.$val['ref_date_end'];
            if ($val->user_type == 1) {
                $newUserList = $val;
            }
            else {
                $userList = $val;
            }
        }
        
        ////dump();
//        dump($userList);
//        exit;
        $userProvinces    = $userList['v_province'];
        $newUserProvinces = $newUserList['v_province'];
        $userInfo         = array_column($userProvinces, 'value', 'name');
        $newUserInfo      = array_column($newUserProvinces, 'value', 'name');
        //dump($newUserInfo);
        $user    = array_slice($userInfo, 0, 10, true);
        $newUser = array_slice($newUserInfo, 0, 10, true);
        arsort($user);
        arsort($newUser);
        
        $data['user']['lables'] = array_keys($user);
        $data['user']['data']   = array_values($user);
        
        $data['newUser']['lables'] = array_keys($newUser);
        $data['newUser']['data']   = array_values($newUser);
        $data['ref_dates']         = $times;
        
        return $this->responseJson(200, $message . '数据查询成功', $data);
    }
    
    /**
     * 获取分布数据
     *
     * @param \Illuminate\Http\Request $request
     *
     * @version
     * @return array
     * @author  inctone(2019-11-04)
     */
    public function getVisitDistributionStateData(Request $request)
    {
        $searchDate = $request->get('searchDate', null);
        if (!$searchDate) {
            $searchDate = date('Y-m-d', strtotime('-1 day'));
        }
        $info = WxSmallVisitDistribution::where('ref_date', $searchDate)->first();
        //dump($info);
        //exit;
        if (empty($info)) {
            return $this->responseJson('400', $searchDate . '暂无数据可供查询');
        }
        $access_source_session_cnt = array_column($info['access_source_session_cnt'], 'value', 'key');
        arsort($access_source_session_cnt);
        $asscArr = array_slice($access_source_session_cnt, 0, 10, true);
        
        //access_staytime_info
        $access_staytime_info = array_column($info['access_staytime_info'], 'value', 'key');
        arsort($access_staytime_info);
        $asiArr = array_slice($access_staytime_info, 0, 10, true);
        
        //access_depth_info
        $access_depth_info = array_column($info['access_depth_info'], 'value', 'key');
        arsort($access_depth_info);
        $adiArr = array_slice($access_depth_info, 0, 10, true);
        
        $accsScene = WxSmallVisitDistribution::$wxScene['accs'];
        
        $data['assc']['data']    = array_values($asscArr);
        $data['assc']['labList'] = array_map(function ($v) use ($accsScene)
        {
            return isset($accsScene[$v]) ? $accsScene[$v] : '未知:'.$v;
        }, array_keys($asscArr));
        
        
        $asiScene = WxSmallVisitDistribution::$wxScene['asi'];
        
        $data['asi']['data']    = array_values($asiArr);
        $data['asi']['labList'] = array_map(function ($v) use ($asiScene)
        {
            return isset($asiScene[$v]) ? $asiScene[$v] : '未知:'.$v;
        }, array_keys($asiArr));
        
        $adiScene               = WxSmallVisitDistribution::$wxScene['adi'];
        $data['adi']['data']    = array_values($adiArr);
        $data['adi']['labList'] = array_map(function ($v) use ($adiScene)
        {
            return isset($adiScene[$v]) ? $adiScene[$v] : '未知:'.$v;
        }, array_keys($adiArr));
        
        //dump($data);
        
        return $this->responseJson(200, $searchDate . '数据查询成功', $data);
    }

    protected function proccessLables($lables){

        foreach ($lables as $key => $values){
            $lables[$key] = self::$replace_array[$values];
        }
        return $lables;
    }

    protected function proccessList($list){
        foreach ($list as $key => $values){
            $list[$key]['page_path'] =  self::$replace_array[$values['page_path']];
        }
        return $list;
    }
}
