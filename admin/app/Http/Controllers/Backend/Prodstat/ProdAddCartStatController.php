<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/16
 * Time: 13:19
 */

namespace App\Http\Controllers\Backend\Prodstat;

use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdAddCartStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }


    public function addcart(){
        return view("backend.prodstat.addcart.index");
    }

    public function addcartCount(Request $request){
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->addCartFromRedis($request);
        }else{
            return $this->addCartFromDB($request);
        }
    }

    public function addCartFromRedis($request)
    {
        return $this->responseJson(200, 'success','');

    }

    public function addCartFromDB($request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $addcart_detail_sql = "SELECT pdtId, SUM(day_add_times) as scores from prod_add_cart_statistics where ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId  ORDER BY sum(day_add_times) desc LIMIT ?,?;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_add_cart_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];

        if($count > 0 ){
            $addtocart_detail = DB::select($addcart_detail_sql, [$offset, $limit]);
            $view_ptdIdList =  array_column($addtocart_detail, 'pdtId');
            $view_scores_detail = array_column($addtocart_detail, 'scores');
            $product_detail_list = getProductById($view_ptdIdList)['list'];
            $addToCartDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $addToCartDetail = '';
        }

        return $this->responseJson(200, 'success',$addToCartDetail , $count);

    }

    public function addcartExport(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->addCartExportFromRedis($request);
        }else{
            return $this->addCartExportFromDB($request);
        }
    }


    public function addCartExportFromRedis($request)
    {
        return $this->responseJson(200, 'success','');

    }

    public function addCartExportFromDB($request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $addcart_detail_sql = "SELECT pdtId, SUM(day_add_times) as scores from prod_add_cart_statistics where ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId  ORDER BY sum(day_add_times) desc;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_add_cart_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];

        if($count > 0 ){
            $addtocart_detail = DB::select($addcart_detail_sql);
            $view_ptdIdList =  array_column($addtocart_detail, 'pdtId');
            $view_scores_detail = array_column($addtocart_detail, 'scores');
            $product_detail_list = getProductDetailList($view_ptdIdList);;
            $addToCartDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $addToCartDetail = '';
        }

        return $this->responseJson(200, 'success',$addToCartDetail , $count);

    }

}