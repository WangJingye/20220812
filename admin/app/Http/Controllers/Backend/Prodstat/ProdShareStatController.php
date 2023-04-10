<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/16
 * Time: 13:20
 */

namespace App\Http\Controllers\Backend\Prodstat;

use App\Http\Controllers\Backend\Controller;
use App\Service\DashBoard\Product;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdShareStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function share(){
        return view("backend.prodstat.share.index");
    }



    public function shareCount(Request $request){
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        return $this->shareFromDB($request);
    }


    public function shareFromRedis($request)
    {
        
    }

    public function shareFromDB($request)
    {
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));
        $data = (new Product())->getProdShareView($start_time,$end_time,$offset,$limit);
        return $this->responseJson(200,"success", $data['top_share'], $data['count']);
    }
    
    public function shareExport(Request $request){
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->shareExportFromRedis($request);
        }
        else{
            return $this->shareExportFromDB($request);
        }
    }


    public function shareExportFromRedis($request)
    {

    }

    public function shareExportFromDB($request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $share_detail_sql = "SELECT pdtId ,sum(day_share_times)as scores FROM prod_share_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId ORDER BY sum(day_share_times) desc;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_share_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        if($count > 0 ){
            $share_detail = DB::select($share_detail_sql);
            $view_ptdIdList =  array_column($share_detail, 'pdtId');
            $view_scores_detail = array_column($share_detail, 'scores');
            $product_detail_list = getProductDetailList($view_ptdIdList);
            $shareProductDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $shareProductDetail = '';
        }
        return $this->responseJson(200,"success", $shareProductDetail, $count);
    }
}