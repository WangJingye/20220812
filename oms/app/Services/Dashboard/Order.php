<?php
namespace App\Services\Dashboard;

use Illuminate\Support\Facades\DB;

class Order
{
    public $order_table = 'oms_order_main';

    public function getData($searchStartDate,$searchEndDate){
        $sql = "SELECT count(distinct user_id) as uv FROM ".$this->order_table." WHERE channel=1 and created_at  BETWEEN \"$searchStartDate\" AND \"$searchEndDate\"  ";
        $order = DB::select($sql);
        $order = json_decode(json_encode($order),true);
        $data['created_order_uv'] = (int) $order[0]['uv'];

        $sql = "SELECT count(distinct user_id) as uv FROM ".$this->order_table." WHERE channel=1 and order_status >2 and created_at  BETWEEN \"$searchStartDate\" AND \"$searchEndDate\"  ";
        $order = DB::select($sql);
        $order = json_decode(json_encode($order),true);
        $data['paid_order_uv'] = (int) $order[0]['uv'];

        return $data;
    }
}