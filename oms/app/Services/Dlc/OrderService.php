<?php namespace App\Services\Dlc;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Model\{Order};
use Illuminate\Support\Arr;

class OrderService
{
    public static function exportWarehouseOrder(){
        $data[] = [
            'OrderDate','HUSR06','Externorderkey','SKU','Lottable03','OPENQTY','Notes','C_company',
            'C_Contact1','C_Phone1','C_Address1','Others'
        ];
        $orders = Order::with('orderDataItem')
            ->where('order_status',3)->get();
        $virtual_prod_prefix = 'VIRTUAL_';
        foreach($orders as $order){
            foreach($order->orderDataItem as $item){
                //过滤掉虚拟商品
                if((strpos($item->sku,$virtual_prod_prefix) === 0)){
                    continue;
                }
                $data[] = [
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('Y-m-d'),
                    "\t".$order->order_sn,
                    "\t".$item->sku,
                    '良品',
                    $item->qty,
                    '大于15个月',
                    '微商城直营订单',
                    $order->contact,
                    "\t".$order->mobile,
                    "{$order->province}{$order->district}{$order->city}{$order->address}",
                    ''
                ];
            }
        }
        return $data;
    }


}
