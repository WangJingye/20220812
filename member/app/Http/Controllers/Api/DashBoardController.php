<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Service\DashBoard\DashBoard;
use Illuminate\Http\Request;

class DashBoardController extends Controller
{

    public function index(Request $request){
        $dash_board = new DashBoard();
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
        $start_time = '1970-08-01 00:00:00';
        $end_time = date("Y-m-d H:i:s");
        if($range == 'day'){
            $start_time = date("Y-m-d 00:00:00",strtotime("-1 day"));
            $end_time = date("Y-m-d 23:59:59",strtotime("-1 day"));
        }
        if($range == 'week'){
            $start_time = date("Y-m-d 00:00:00",strtotime("-7 day"));
        }
        if($range == 'month'){
            $start_time = date("Y-m-d 00:00:00",strtotime("last month"));
        }
        $register_data = $dash_board->getRegisterTotal($start_time,$end_time);
        $login_data = $dash_board->getLoginData($start_time,$end_time);
        $data = array_merge($register_data,$login_data);
        return $this->success('',$data);
    }

}
