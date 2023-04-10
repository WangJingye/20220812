<?php
namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Backend\Controller;
//use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Service\DashBoard\Miniapp;
use App\Service\DashBoard\Product;
use App\Lib\Http;

//独立的dashboard页面
class DashboardController extends Controller
{

    public function index(Request $request){
        return view('backend.dashboard.index');
    }

    public function allData(Request $request){
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
            $end_date = date("Y-m-d",strtotime("-1 day"));
        }
        if($range == 'week'){
            $start_date = date("Y-m-d",strtotime("-7 day"));
        }
        if($range == 'month'){
            $start_date = date("Y-m-d",strtotime("last month"));
        }
        $member_dashboard = (new Http)->curl('member/dashboard',request()->all());
        $member_data = $member_dashboard['data'];
        if($view_type == 1){
            $data['data'] = [
                'member_data'=>$member_data,
            ];
            return $this->response($data);
        }
        $product_data = (new Product())->getData($start_date,$end_date);
        if($view_type == 2){
            $data['data'] = [
                'product_data'=>$product_data,
            ];
            return $this->response($data);
        }
        $mini_data = (new Miniapp())->getData($start_date,$end_date);
        $oms_data = (new Http)->curl('oms/dashboard',request()->all());

        $pdt_view_percent = '0%';
        $create_order_percent = '0%';
        $paid_order_percent = '0%';
        if($mini_data['uv']){
            $pdt_view_percent = bcdiv($mini_data['pdt_uv'],$mini_data['uv'],2) * 100;
            $pdt_view_percent = (int) $pdt_view_percent;
            $pdt_view_percent = $pdt_view_percent . '%';
        }
        if($mini_data['pdt_uv']){
            $create_order_percent = bcdiv($oms_data['created_order_uv'],$mini_data['pdt_uv'],2) * 100;
            $create_order_percent = (int) $create_order_percent;
            $create_order_percent = $create_order_percent. '%';
        }
        if($oms_data['created_order_uv']){
            $paid_order_percent = bcdiv($oms_data['paid_order_uv'],$oms_data['created_order_uv'],2) * 100;
            $paid_order_percent = (int) $paid_order_percent;
            $paid_order_percent = $paid_order_percent . '%';
        }
        $oms_data['pdt_view_percent'] = $pdt_view_percent;
        $oms_data['pdt_uv'] = $mini_data['pdt_uv'];
        $oms_data['create_order_percent'] = $create_order_percent;
        $oms_data['paid_percent'] = $paid_order_percent ;
        $oms_data['uv'] = $mini_data['uv'];
        $oms_data['add_cart_times'] = $product_data['add_cart_times'];
        if($view_type == 3){
            $data['data'] = [
                'mini_data'=>$mini_data,
                'oms_data'=>$oms_data,
            ];
            return $this->response($data);
        }
        //返回全部数据
        $data['data'] = [
            'mini_data'=>$mini_data,
            'product_data'=>$product_data,
            'member_data'=>$member_data,
            'oms_data'=>$oms_data,
        ];
        return $this->response($data);
    }

    public function response($data){
        return $data;
    }

}