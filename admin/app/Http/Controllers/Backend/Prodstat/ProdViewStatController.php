<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/12
 * Time: 10:08
 */

namespace App\Http\Controllers\Backend\Prodstat;


use App\Http\Controllers\Backend\Controller;
use App\Service\DashBoard\Product;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
#use App\Model\Search\Product;
use App\Model\WxSmallVisitPage;
use App\Http\Controllers\Backend\Statistics\VisitStatController;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;

class ProdViewStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function view(){
        return view("backend.prodstat.view.index");
    }

    public function viewCount(Request $request){
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        return $this->viewFromDB($request);
    }

    public function viewFromRedis(Request $request)
    {
        
    }

    public function viewFromDB(Request $request)
    {
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));
        $data = (new Product())->getProdView($start_time,$end_time,$offset,$limit);
        return $this->responseJson(200,"success", $data['top_prod_view'], $data['count']);
    }


    public function viewExport(Request $request){
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        if($searchStartDate === date("Ymd")){
            return $this->viewExportFromRedis($request);
        }
        else{
            return $this->viewExportFromDB($request);
        }
    }

    public function viewExportFromRedis(Request $request)
    {

    }

    public function viewExportFromDB(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $searchStartDate = $start_time ? date("Ymd",strtotime($start_time)) : date("Ymd");
        $searchEndDate = $end_time ? date("Ymd",strtotime($end_time)) : date("Ymd");
        $view_detail_sql = "SELECT pdtId ,sum(day_view_times)as scores FROM prod_view_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate GROUP BY pdtId ORDER BY sum(day_view_times) desc;";
        $count_sql =   "SELECT count(DISTINCT pdtId) as count FROM prod_view_statistics WHERE ref_date BETWEEN $searchStartDate AND $searchEndDate;";
        $count_object = DB::select($count_sql);
        $count = object2array($count_object)[0]['count'];
        if($count > 0 ){
            $view_detail = DB::select($view_detail_sql);
            $view_ptdIdList =  array_column($view_detail, 'pdtId');
            $view_scores_detail = array_column($view_detail, 'scores');
            $product_detail_list = getProductDetailList($view_ptdIdList);
            $viewProductDetail = processProductDetail($product_detail_list,$view_scores_detail);
        }
        else{
            $viewProductDetail = '';
        }
        return $this->responseJson(200,"success", $viewProductDetail, $count);

    }

}