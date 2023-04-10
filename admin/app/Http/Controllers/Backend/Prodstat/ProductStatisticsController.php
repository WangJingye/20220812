<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/12/27
 * Time: 11:39
 */

namespace App\Http\Controllers\Backend\Prodstat;


use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use App\Model\Search\Product;
use App\Model\WxSmallVisitPage;
use App\Http\Controllers\Backend\Statistics\VisitStatController;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;

class ProductStatisticsController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function view(){
        return view("backend.prodstat.view.index");
    }

    public function keyword(){
        return view("backend.prodstat.keyword.index");
    }


    public function order(){
        return view("backend.prodstat.order.index");
    }

    public function share(){
        return view("backend.prodstat.share.index");
    }

    public function favorite(){
        return view("backend.prodstat.favorite.index");
    }

    public function addcart(){
        return view("backend.prodstat.addcart.index");
    }


    public function prodtypeview()
    {
        return view("backend.prodstat.prodtypeview.index");
    }

    public function viewCount(Request $request){
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $view_count = file_get_contents("http://goods.css.com.cn/prodView?date=$searchDailyDate");
        return $this->responseJson(200, 'success',json_decode($view_count ,true));
    }


    public function addcartCount(Request $request){
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $addcart_count = file_get_contents("http://goods.css.com.cn/prodAddcart?date=$searchDailyDate");
        return $this->responseJson(200, 'success',json_decode($addcart_count ,true));
    }


    public function favoriteCount(Request $request){
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $page = $request->get('page', 1);
        $Statistics_Redis       = Redis::connection('statistics');
        $favorite_day = date("Ymd");
        $rank_favorite ="Ranking_Favorite_" . $favorite_day;
        $total = $Statistics_Redis->ZCARD($rank_favorite);
        $favorite_count = file_get_contents("http://goods.css.com.cn/prodFavorite?page=$page&date=$searchDailyDate");
        return $this->responseJson(200, 'success',json_decode($favorite_count ,true),$total);
    }


    public function shareCount(Request $request){
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $share_count = file_get_contents("http://goods.css.com.cn/prodShare?date=$searchDailyDate");
        return $this->responseJson(200, 'success',json_decode($share_count ,true));
    }

    public function keywordCount(Request $request)
    {
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        //链接到负责统计各种View的Redis库6
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $Statistics_Redis       = Redis::connection('statistics');
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $rank_keyword ="Ranking_SearchKeywords_" . $searchDailyDate;
        $total = $Statistics_Redis->ZCARD($rank_keyword);
        $search_keywords_top10  = $Statistics_Redis->ZREVRANGE($rank_keyword,$start ,$end);
        $search_keywords_top10_withscores  = $Statistics_Redis->ZREVRANGE($rank_keyword, $start ,$end, "withscores");
        $search_detail = [];
        foreach ($search_keywords_top10 as $key => $value)
        {
            $search_detail[$key]["keyword"] = $value;
            $search_detail[$key]["count"] = $search_keywords_top10_withscores[$value];
        }
        return $this->responseJson(200, 'success',$search_detail, $total);
    }

    public function prodTypeViewCount(Request $request)
    {
        $start_time = $request->get('start_time',null);
        $searchDailyDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        //链接到负责统计各种View的Redis库6
        $Statistics_Redis                       = Redis::connection('statistics');
        $prod_statistics = "prod_statistics_by_prod_type_" . $searchDailyDate;
        $typeViewScores        = $Statistics_Redis->ZREVRANGE($prod_statistics , 0 ,-1,"withscores");


        $typeView[0]['typeName'] = '定价黄金（GF）';
        $typeView[0]['count'] = $this->checkData("GF" ,$typeViewScores);

        $typeView[1]['typeName'] = '计价黄金（GA）';
        $typeView[1]['count'] = $this->checkData("GA" ,$typeViewScores);

        $typeView[2]['typeName'] = '定/计价铂金（PF /PA/MP）';
        $typeView[2]['count']   = $this->checkData("PF" ,$typeViewScores)
            + $this->checkData("PA" ,$typeViewScores)
            + $this->checkData("MP" ,$typeViewScores);

        $typeView[3]['typeName'] = '镶嵌（DF/DI/GS/PL/QF/SS/TF/XF）';
        $typeView[3]['count']  =  $this->checkData("DF" ,$typeViewScores)
            + $this->checkData("DI" ,$typeViewScores)
            + $this->checkData("GS" ,$typeViewScores)
            + $this->checkData("PL" ,$typeViewScores)
            + $this->checkData("QF" ,$typeViewScores)
            + $this->checkData("SS" ,$typeViewScores)
            + $this->checkData("TF" ,$typeViewScores)
            + $this->checkData("XF" ,$typeViewScores);

        $typeView[4]['typeName'] = 'K金（FJ）';
        $typeView[4]['count'] = $this->checkData("FJ" ,$typeViewScores);

        return $this->responseJson(200, 'success',$typeView);
    }


    public function orderCount(Request $request){
        $searchDate = $request->get('searchDailyDate', null);
        if (!$searchDate) {
            $searchDate= date("Y-m-d", strtotime("-1 month"));
        }
        $info = $this->curl('order/orderStatic', $searchDate);
        return $this->responseJson(200, 'success',$info);
    }

    public function setOrderRate(Request $request)
    {
        $targets = $request->get('targets', 0);
        $data = ['targets'=> $targets];
        $info = $this->curl('order/setOrderRate', $data);
        return $this->responseJson(200, 'success',$info);

    }

    /**
     * 获取产品查看排行、加购排行、收藏排行
     * @return mixed
     */
    public function getRankingList(Request $request)
    {
        //链接到负责统计各种View的Redis库5
        $Statistics_Redis       = Redis::connection('statistics');
        //按照分值从大到小取10条记录出来。
        $prod_view_top10        = $Statistics_Redis->ZREVRANGE("prod_view",0 ,9);
        $add_cart_top10         = $Statistics_Redis->ZREVRANGE("Ranking_AddCart",0 ,9);
        $favorite_top10         = $Statistics_Redis->ZREVRANGE("Ranking_Favorite",0 ,9);
        $search_keywords_top10  = $Statistics_Redis->ZREVRANGE("Ranking_SearchKeywords",0 ,9);
        $prod_share_top10       = $Statistics_Redis->ZREVRANGE("prod_share_statistics",0 ,9);

        $result['prod_view_top10'] = $prod_view_top10;
        $result['add_cart_top10'] = $add_cart_top10;
        $result['favorite_top10'] = $favorite_top10;
        $result['prod_share_top10'] = $prod_share_top10;
        $result['search_keywords_top10'] = $search_keywords_top10;

        return $result;
    }


    public function checkData($prodType ,$typeViewScores)
    {
        $result = isset($typeViewScores["$prodType"]) ? $typeViewScores["$prodType"] : 0;
        return $result;
    }


    public function getConversionRate(Request $request)
    {
        $paginate = $request->get('limit',30);
        $searchDate = $request->get('searchDate',  null);
        if(!$searchDate){
            $searchDate =  date('Y-m-d', strtotime('-1 day'));
        }
        $searchSort = $request->get('searchSort', 'page_visit_uv');

        $submit_uv = $this->getDayOrderUsers($searchDate);

        $label = [];
        $conversion_list = [];
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
        if (!empty($list)) {
            $list = $list->toArray();
            $chartInfo = [];
            foreach ($list AS $value) {
                switch ($value['page_path']){
                    case "pages/default/home/home" :
                        $miniapp_user_info = $value;
                        $miniapp_user_info['bounce_rate'] = number_format($miniapp_user_info['exitpage_pv'] / $miniapp_user_info['page_visit_uv'] ,4) * 100 . "%";
                        $miniapp_user_info['title'] = "小程序用户数";
                        $label[] = "小程序用户数";
                        break;
                    case "pages/pdt/pdt-detail/pdt-detail" :
                        $page_detail_info = $value;
                        $page_detail_info['bounce_rate'] = number_format($page_detail_info['exitpage_pv'] / $page_detail_info['page_visit_uv'] ,4) * 100 . "%";
                        $page_detail_info['title'] = "所有产品页";
                        $label[] = "所有产品页";
                        break;
                    case "pages/cart/shopping-cart/shopping-cart":
                        $add_to_cart_info = $value;
                        $add_to_cart_info['bounce_rate'] = number_format($add_to_cart_info['exitpage_pv'] / $add_to_cart_info['page_visit_uv'] ,4) * 100 . "%";
                        $add_to_cart_info['title'] = "购物车页";
                        $label[] = "购物车页";
                        break;
                    case "pages/cart/checkout/checkout":
                        $check_order_info = $value;
                        $check_order_info['bounce_rate'] = number_format($check_order_info['exitpage_pv'] / $check_order_info['page_visit_uv'] ,4) * 100 . "%";
                        $check_order_info['title'] = "确认订单页";
                        $label[] = "确认订单页";
                        break;
                    default:
                        break;
                }
            }
        }
        if(isset($miniapp_user_info) && isset($page_detail_info) &&
            isset($add_to_cart_info) && isset($check_order_info)){
            $page_detail_info['conversion_rate'] = number_format($page_detail_info['page_visit_uv'] / $miniapp_user_info['page_visit_uv'] ,4) * 100 . "%";
            $add_to_cart_info['conversion_rate'] = number_format($add_to_cart_info['page_visit_uv'] / $page_detail_info['page_visit_uv'] ,4) * 100 . "%";
            $check_order_info['conversion_rate'] = number_format($check_order_info['page_visit_uv'] / $add_to_cart_info['page_visit_uv'] ,4) * 100 . "%";
            $submit_order_info['page_visit_uv'] = $submit_uv;
            $submit_order_info['title'] = "提交订单";
            $submit_order_info['bounce_rate'] = 0;
            $submit_order_info['conversion_rate'] = number_format($submit_order_info['page_visit_uv'] / $add_to_cart_info['page_visit_uv'] ,4) * 100 . "%";
            $conversion_list[] = $miniapp_user_info;
            $conversion_list[] = $page_detail_info;
            $conversion_list[] = $add_to_cart_info;
            $conversion_list[] = $check_order_info;
            $conversion_list[] = $submit_order_info;
        }


        $reqData = $conversion_list;
        return $this->responseJson(200, $searchDate . '数据查询成功', $reqData);

    }

    /**
     * 从DB order表中读取每日下单人数
     * @param string $params
     * @return int
     */
    public function getDayOrderUsers($params = '')
    {
        $info = 0;
        $searchDate = $params;
        $result = DB::connection("order")->table('orders')->whereDate('created_at', $searchDate)->select('wechat_id')->distinct()->get();
        $info = count($result);
        return $info;
    }
    public  function efficiency()
    {

        return view("backend.prodstat.efficiency.index");

    }


    public  function conversionRate()
    {
        return view("backend.prodstat.conversionrate.index");

    }

}