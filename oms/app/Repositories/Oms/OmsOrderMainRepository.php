<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/18
 * Time: 17:33
 */
namespace App\Repositories\Oms;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
class OmsOrderMainRepository extends Model
{
    protected static $order_main_table = "oms_order_main";

    const CREATED_AT = 'transaction_time';
    const UPDATED_AT = 'update_time';
    /**
     * 订单写入主订单表
     * @param $order_info
     * @return bool
     */
    public static function mainOrderInsert($order_info)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $order_created_info =  $oms_order_model->updateOrInsert($order_info);
        return $order_created_info;
    }

    /**
     * 通过order_id来以后的order_sn
     * @param $order_id
     * @return bool
     */
    public static function getOrderSn($order_id)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $order_sn_info = $oms_order_model->where('orderId', $order_id)->pluck('oms_order_sn')->toArray();
        if(sizeof($order_sn_info ) > 0){
            return $order_sn_info[0];
        }
        return false;
    }


    /**
     * 获取主订单的ID，作为子订单的外键
     * @param $oms_order_sn
     * @return mixed
     */
    public static function getMainOrderId($oms_order_sn)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $main_order_id = $oms_order_model->where('order_sn', $oms_order_sn)->pluck('id')->toArray();
        return $main_order_id[0];
    }

    /**
     * 通过主订单的ID，获取主订单信息
     * @param $oms_order_sn
     * @return array
     */
    public static function getMainOrderDetail($oms_order_sn)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $main_order_info = $oms_order_model->where('order_sn', $oms_order_sn);
        return object2array($main_order_info->get()->toArray()[0]);
    }

    public static function getItemsOrderDetail($main_order_id)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $main_order_info = $oms_order_model->where('order_main_id', $main_order_id)->get()->toArray();
        return $main_order_info;
    }


    public static function getSql()
    {
        DB::listen(function ($sql) {
            dump($sql);
            $singleSql = $sql->sql;
            if ($sql->bindings) {
                foreach ($sql->bindings as $replace) {
                    $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                    $singleSql = preg_replace('/\?/', $value, $singleSql, 1);
                }
                dump($singleSql);
            } else {
                dump($singleSql);
            }
        });
    }


    /**
     * 联调查询订单详情
     * @param $order_id
     * @return mixed
     */
    public static function getOrderDetailUnion($order_id)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        //self::getSql();
        $all_order_info = $oms_order_model
            ->where('oms_order_main.id', $order_id)
            ->leftJoin('oms_order_items', function ($join){
            $join->on('oms_order_main.id', '=', 'oms_order_items.order_main_id');
        })->get();
        return $all_order_info->toArray();
    }

    /**
     * 读取所有
     * @return mixed
     */
    public static function getOrderListToPos()
    {
        //约定某个字段为0的代表需要导出给POS
        $oms_order_model = DB::table(self::$order_main_table);
        $all_order_info = $oms_order_model
            ->where('oms_order_items.pos_txt_status', 0)
            ->leftJoin('oms_order_items', function ($join){
                $join->on('oms_order_main.id', '=', 'oms_order_items.order_main_id');
            })->get();
        return $all_order_info->toArray();
    }

    public static function getOrderDetailList($page, $limit)
    {
        $oms_order_model = DB::table(self::$order_main_table);
        $all_order_info = $oms_order_model
            ->leftJoin('oms_order_items', function ($join){
                $join->on('oms_order_main.id', '=', 'oms_order_items.order_main_id');
            })->get();

        var_export($all_order_info->toArray());exit;
        return $all_order_info->toArray();

    }

}
