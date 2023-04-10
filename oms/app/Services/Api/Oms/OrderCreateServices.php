<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/18
 * Time: 17:20
 */

namespace App\Services\Api\Oms;

use App\Model\Log;
use App\Repositories\Oms\{OmsOrderIdRepository,OmsOrderMainRepository,OmsOrderItemsRepository};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class OrderCreateServices
{
    public static function makeOrderInfo(Request $request){
        //初始化订单请求信息
        $order_init_info = json_decode($request->getContent() ,true)['order'];
        // 开启事务
        DB::beginTransaction();
        try {
            $main_order_id = self::mainOrderToDB($order_init_info);
            if(!$main_order_id){
                throw new \Exception(false);
            }
            $itmes_order_status = self::itemsOrderToDB($order_init_info , $main_order_id);
            DB::commit();
            return $itmes_order_status;
        }
        catch (\Exception $e){
            DB::rollBack();
            //这里需要额外处理下异常的Log记录
            return $e;
        }

    }


    public static function mainOrderToDB($order_init_info)
    {
        $main_order_info = [];
        $channel = $order_init_info['channel'];
        $main_order_info['oms_order_sn'] = OmsOrderIdRepository::incrementOrderId($channel ,$order_init_info['orderId']);
        $main_order_info['posID'] = $order_init_info['posID'];
        $main_order_info['channel'] = $order_init_info['channel'];
        $main_order_info['store_code'] = $order_init_info['store_code'];
        $main_order_info['transaction_date'] = date("Y-m-d");
        $main_order_info['transaction_time'] = date("Y-m-d H:i:s");
        $main_order_info['totalPrice'] = $order_init_info['totalPrice'];
        $main_order_info['orderId'] = $order_init_info['orderId'];
        $main_order_info['payment_type'] = $order_init_info['paymentInfo']['paymentMode'];
        $main_order_info['paymentId'] = $order_init_info['paymentInfo']['paymentId'];
        $main_order_info['provice'] = $order_init_info['deliveryInfo']['province'];
        $main_order_info['city'] = $order_init_info['deliveryInfo']['city'];
        $main_order_info['district']  = $order_init_info['deliveryInfo']['district'];
        $main_order_info['title']  = $order_init_info['deliveryInfo']['title'];
        $main_order_info['zip_code']  = $order_init_info['deliveryInfo']['zip_code'];
        $main_order_info['contact']  = $order_init_info['deliveryInfo']['contact'];
        $main_order_info['telephone']  = $order_init_info['deliveryInfo']['telephone'];
        $main_order_info['address']  = $order_init_info['deliveryInfo']['address'];

        $main_order_info['deliveryMode']  = $order_init_info['deliveryInfo']['deliveryMode'];
        if($order_init_info['paymentInfo']['paymentId']){
            $main_order_info['order_status']  = 1;
        }
        else{
            $main_order_info['order_status']  = 0;
        }
        try{
            $order_created_info = OmsOrderMainRepository::mainOrderInsert($main_order_info);
            if (!$order_created_info){
                throw new \Exception(false);
            }
        }
        catch (\Exception $exception){
            echo  $exception;
        }
        if($order_created_info){
            $main_order_id = OmsOrderMainRepository::getMainOrderId($main_order_info['oms_order_sn']);
        }
        return $main_order_id;
    }

    public static function itemsOrderToDB($order_init_info , $main_order_id = null)
    {
        $order_items_info = [];
        $order_items_entries = $order_init_info['orderEntries'];
        try{
            foreach ($order_items_entries as $order_items_entrie){
                $order_items_info['order_main_id'] = $main_order_id;
                $order_items_info['good_sku'] = $order_items_entrie['sku'];
                $order_items_info['quantity'] = $order_items_entrie['qty'];
                $order_items_info['product_name'] = $order_items_entrie['description'];
                $order_items_status = OmsOrderItemsRepository::itemsOrderInsert($order_items_info);
                if(!$order_items_status){
                    throw new \Exception(false);
                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }

    }
}
