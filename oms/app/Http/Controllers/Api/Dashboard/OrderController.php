<?php
namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Dashboard\Order;

//dashboard需要的数据
class OrderController extends Controller
{
    //小程序的下单人数和支付人数
    public function getData(Request $request){
        $params = $request->all();
        $range = $params['range']??'';
        $view_type = $params['viewType']??0;//数据类型1，会员数据，2，商品数据，3，小程序数据(包括订单数据)
        $view_type = (int) $view_type;
        $data_type = $params['dateType']??4;//数据范围4,全部，3，月份，2,7天，1，昨天
        $data_type = (int) $data_type;
        if($data_type == '1'){
            $range = 'day';
        }
        if($data_type == '2'){
            $range = 'week';
        }
        if($data_type == '3'){
            $range = 'month';
        }
        $start_date = '1970-08-01';
        $end_date = date("Y-m-d");
        if($range == 'day'){
            $start_date = date("Y-m-d",strtotime("-1 day"));
        }
        if($range == 'week'){
            $start_date = date("Y-m-d",strtotime("-7 day"));
        }
        if($range == 'month'){
            $start_date = date("Y-m-d",strtotime("last month"));
        }
        $data = (new Order())->getData($start_date,$end_date);
        return $this->response($data);
    }

    public function response($data){
        return $data;
    }
}