<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/29
 * Time: 15:02
 */

namespace App\Repositories\Oms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
class OmsOrderItemsRepository extends Model
{
    protected static $order_items_table = 'oms_order_items';

    const CREATED_AT = 'transaction_time';
    const UPDATED_AT = 'update_time';


    public function mainOrder()
    {
        return $this->belongsTo('App\Repositories\Oms\OmsOrderMainRepository');
    }


    /**
     * 拆分出来的子订单信息存储(主要记录买了哪些东西，数量，价格）
     * @param $items_order_info
     * @return bool
     */
    public static function itemsOrderInsert($items_order_info)
    {
        $oms_order_items_model = DB::table(self::$order_items_table);
        $order_created_info =  $oms_order_items_model->updateOrInsert($items_order_info);
        return $order_created_info;
    }


    public static function itemsOrderDetail($oms_main_order_id)
    {
        $oms_order_items_model = DB::table(self::$order_items_table);
        $items_order_detail = $oms_order_items_model
            ->select('id', 'sku', 'order_sn', 'original_price',
                'order_main_id', 'spu', 'qty', 'name',
                'product_amount_total', 'order_amount_total', 'pic',
                'short_desc', 'discount', 'type')
            ->where('order_main_id', $oms_main_order_id)
            ->get()->toArray();
        return $items_order_detail;
    }



}
