<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/18
 * Time: 16:53
 */

namespace App\Repositories\Oms;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\AfterOrderSale;
use App\Model\AfterSaleItem;

class AfterOrderMainRepository extends Model
{
    protected static $order_main_table = "oms_after_sales";

    const CREATED_AT = 'transaction_time';
    const UPDATED_AT = 'update_time';

    /**
     * 联调查询订单详情
     * @param $after_order_id
     * @return mixed
     */
    public static function getAfterOrderDetailUnion($after_order_id)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $all_order_info = $oms_order_model
            ->where('oms_after_sales.id', $after_order_id)
            ->leftJoin('oms_order_items', function ($join){
                $join->on('oms_after_sales.id', '=', 'oms_order_items.after_sale_id');
            })->get();
        return $all_order_info->toArray();
    }

    public static function getAfterOrderUnionInfo($after_order_id)
    {
        $order = AfterOrderSale::where('id', $after_order_id)->with('afterOrderItem')->first();
        return $order;
    }
}