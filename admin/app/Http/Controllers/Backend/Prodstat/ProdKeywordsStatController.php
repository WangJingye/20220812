<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/12
 * Time: 10:09
 */

namespace App\Http\Controllers\Backend\Prodstat;

use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdKeywordsStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function keyword(){
        return view("backend.prodstat.keyword.index");
    }

    public function keywordCount(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->searchFromRedis($request);
        }

        else{
            return $this->searchFromDB($request);
        }
    }


    public function searchFromRedis(Request $request)
    {
        $searchStartDate = date("Ymd");
        //链接到负责统计各种View的Redis库6
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $Statistics_Redis       = Redis::connection('statistics');
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $rank_keyword ="Ranking_SearchKeywords_" . $searchStartDate;
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

    public function searchFromDB($request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $keywords_detail_sql = "SELECT keywords as keyword ,sum(search_time)as count FROM prod_keywords_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY keywords ORDER BY sum(search_time) desc limit ?,?;";
        $count_sql =   "SELECT count(DISTINCT keywords) as count FROM prod_keywords_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        $keywords_detail = DB::select($keywords_detail_sql, [$offset, $limit]);
        return $this->responseJson(200,"success", $keywords_detail, $count);
    }


    public function keywordExport(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->searchExportFromRedis($request);
        }

        else{
            return $this->searchExportFromDB($request);
        }
    }


    public function searchExportFromRedis(Request $request)
    {
        $searchStartDate = date("Ymd");
        //链接到负责统计各种View的Redis库6
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $Statistics_Redis       = Redis::connection('statistics');
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $rank_keyword ="Ranking_SearchKeywords_" . $searchStartDate;
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

    public function searchExportFromDB($request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $keywords_detail_sql = "SELECT keywords as keyword ,sum(search_time)as count FROM prod_keywords_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY keywords ORDER BY sum(search_time) desc;";
        $count_sql =   "SELECT count(DISTINCT keywords) as count FROM prod_keywords_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        $keywords_detail = DB::select($keywords_detail_sql);
        return $this->responseJson(200,"success", $keywords_detail, $count);
    }
}