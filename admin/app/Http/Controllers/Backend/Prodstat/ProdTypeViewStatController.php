<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/15
 * Time: 15:55
 */

namespace App\Http\Controllers\Backend\Prodstat;

use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use App\Service\DashBoard\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdTypeViewStatController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function prodtypeview()
    {
        return view("backend.prodstat.prodtypeview.index");
    }

    public function prodTypeViewCount(Request $request)
    {
        $start_time = $request->get('start_time',date("Ymd",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Ymd",strtotime("-1 day")));

        if ($start_time>$end_time){
            return $this->responseJson(1,"开始时间不能大于结束时间");
        }
        return $this->ViewTypeFromDB($request);

    }

    public function checkData($prodType ,$typeViewScores)
    {
        $result = isset($typeViewScores["$prodType"]) ? $typeViewScores["$prodType"] : 0;
        return $result;
    }


    public function ViewTypeFromRedis(Request $request)
    {
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));
        $data = (new Product())->getProdCatView($start_time,$end_time,$offset,$limit);
        return $this->responseJson(200,"success", $data['top_cat_view'], $data['count']);
    }

    public function ViewTypeFromDB(Request $request)
    {
        $page = $request->get("page",1);
        $limit = $request->get("limit",10);
        $offset = ($page - 1) * $limit;
        $start_time = $request->get('start_time',date("Y-m-d",strtotime("-1 day")));
        $end_time = $request->get('end_time',date("Y-m-d",strtotime("-1 day")));
        $data = (new Product())->getProdCatView($start_time,$end_time,$offset,$limit);
        return $this->responseJson(200,"success", $data['top_cat_view'], $data['count']);
    }


}