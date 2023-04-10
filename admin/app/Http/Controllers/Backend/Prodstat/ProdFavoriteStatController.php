<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/16
 * Time: 13:20
 */

namespace App\Http\Controllers\Backend\Prodstat;

use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdFavoriteStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function favorite(){
        return view("backend.prodstat.favorite.index");
    }

    public function favoriteCount(Request $request){
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->favoriteFromRedis($request);
        }else{
            return $this->favoriteFromDB($request);
        }
    }

    public function favoriteFromRedis(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $Statistics_Redis       = Redis::connection('statistics');
        $favorite_day = date("Ymd");
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $rank_favorite ="Ranking_Favorite_" . $favorite_day;
        $total = $Statistics_Redis->ZCARD($rank_favorite);
        $favorite_top10_prodIdList         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end);
        $favorite_top10_withscores         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end,"withscores");
        $result = file_get_contents("http://goods.css.com.cn/prodFavorite?page=$page&start_time=$start_time&end_time=$end_time");
        return $this->responseJson(200,"success", $favorite_top10_withscores, $total);
    }

    public function favoriteFromDB(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $favorite_detail_sql = "SELECT pdtId, SUM(day_favorite_times) as scores from prod_favorite_statistics where ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId  ORDER BY sum(day_favorite_times) desc LIMIT ?,?;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_favorite_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        if($count > 0 ){
            $favorite_detail = DB::select($favorite_detail_sql, [$offset, $limit]);
            $view_ptdIdList =  array_column($favorite_detail, 'pdtId');
            $view_scores_detail = array_column($favorite_detail, 'scores');
            $product_detail_list = getProductById($view_ptdIdList)['list'];
            $favoriteDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $favoriteDetail = '';
        }
        return $this->responseJson(200, 'success',$favoriteDetail , $count);
    }

    public function favoriteExport(Request $request){
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->favoriteExportFromRedis($request);
        }else{
            return $this->favoriteExportFromDB($request);
        }
    }

    public function favoriteExportFromRedis(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $Statistics_Redis       = Redis::connection('statistics');
        $favorite_day = date("Ymd");
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $rank_favorite ="Ranking_Favorite_" . $favorite_day;
        $total = $Statistics_Redis->ZCARD($rank_favorite);
        $favorite_top10_prodIdList         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end);
        $favorite_top10_withscores         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end,"withscores");
        $result = file_get_contents("http://goods.css.com.cn/prodFavorite?page=$page&start_time=$start_time&end_time=$end_time");
        return $this->responseJson(200,"success", $favorite_top10_withscores, $total);
    }

    public function favoriteExportFromDB(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $favorite_detail_sql = "SELECT pdtId, SUM(day_favorite_times) as scores from prod_favorite_statistics where ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId  ORDER BY sum(day_favorite_times) desc;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_favorite_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        if($count > 0 ){
            $favorite_detail = DB::select($favorite_detail_sql);
            $view_ptdIdList =  array_column($favorite_detail, 'pdtId');
            $view_scores_detail = array_column($favorite_detail, 'scores');
            $product_detail_list = getProductDetailList($view_ptdIdList);;
            $favoriteDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $favoriteDetail = '';
        }
        return $this->responseJson(200, 'success',$favoriteDetail , $count);
    }

}