<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/1
 * Time: 16:34
 */

namespace App\Services\Api\Oms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Repositories\Oms\{OmsOrderIdRepository,OmsOrderMainRepository ,OmsOrderItemsRepository};
class GetOrdreInfoServices
{
    public static function getOrderInfoByOmsId($omsId){
        $oms_order_detail_all = OmsOrderMainRepository::getOrderDetailUnion($omsId);
        return $oms_order_detail_all;
    }

    public static function getAllOrderByPage(Request $request)
    {
        $page = $request->input("page");
        $limit = $request->input("limit");
        $oms_order_detail_list = OmsOrderMainRepository::getOrderDetailList($page, $limit);
        return $oms_order_detail_list;
    }
}
