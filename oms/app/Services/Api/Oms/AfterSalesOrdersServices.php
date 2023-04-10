<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/7/8
 * Time: 15:19
 */

namespace App\Services\Api\Oms;

use Illuminate\Support\Facades\DB;
use App\Repositories\Oms\{OmsOrderIdRepository,OmsOrderMainRepository ,OmsOrderItemsRepository};
use Illuminate\Http\Request;
use App\Model\AfterOrderSale;


class AfterSalesOrdersServices
{
    public function getOriginalOrderInfo($orderId)
    {
        //取出原始订单数据,目前这个方法不完整，只取出了1条
        //$oms_order_detail_all = OmsOrderMainRepository::getOrderDetailUnion($orderId);
        $oms_order_main_detail = OmsOrderMainRepository::getMainOrderDetail($orderId);
        //主订单表的id是子订单表的main_order_id
        $main_order_id = $oms_order_main_detail['id'];
        $oms_order_items_detail = OmsOrderItemsRepository::itemsOrderDetail($main_order_id);
        $oms_order_info['main'] = $oms_order_main_detail;
        $oms_order_info['items'] = $oms_order_items_detail;
        return $oms_order_info;
    }

    public function afterMainOrderInfo($originalOrderInfo, $question_desc)
    {
        $afterOrderSale = new AfterOrderSale();
        $return_order_info = [];
        $return_order_info['order_main_id'] = $afterOrderSale->createAfterOrderNo();
        $return_order_info['after_sale_id'] = $afterOrderSale->createAfterOrderNo();
        $return_order_info['cms_uid'] = $originalOrderInfo['pos_id'];
        $return_order_info['store_code'] = '5004';
        $return_order_info['order_main_id'] = $originalOrderInfo['order_sn'];
        //子订单id是直接读取正向订单原子订单表中的id？
        $return_order_info['item_order_id'] = $originalOrderInfo['id'];
        //重新映射用户POS ID，这块其实还是正向订单和逆向订单统一起来好一些。
        //退货留言需要后台这边填一下，作为备注
        $return_order_info['question_desc'] = $question_desc;
        $return_order_info['status'] = 0;
        $return_order_info['refund_amount'] = 0;
        $return_order_info['returns_type'] = 1;
        $return_order_info['district'] = $originalOrderInfo['district'];
        $return_order_info['province'] = $originalOrderInfo['province'];
        $return_order_info['city'] = $originalOrderInfo['city'];
        $return_order_info['address'] = $originalOrderInfo['address'];
        $return_order_info['zip_code'] = $originalOrderInfo['zip_code'];
        $return_order_info['contact'] = $originalOrderInfo['contact'];
        $return_order_info['delivery_mode'] = $originalOrderInfo['delivery_mode'];
        $return_order_info['status'] = 0;
        $return_order_info['refund_amount'] = $originalOrderInfo['total_amount'];
        return $return_order_info;
    }

    public function afterItemsOrderInfo($originalOrderInfo, $after_sale_id)
    {
        $afterSaleItem = new AfterSaleItem();
        $after_order_items_info = [];
        foreach ($originalOrderInfo as $originalItemsInfo){
            $originalItemsInfo = object2array($originalItemsInfo);
            $after_order_item_detail = [];
            $after_order_item_detail['after_sale_id'] = $after_sale_id;
            $after_order_item_detail['item_order_id'] = $originalItemsInfo['id'];
            $after_order_item_detail['sku_id'] = $originalItemsInfo['sku'];;
            $after_order_item_detail['product_name'] = $originalItemsInfo['name'];
            $after_order_item_detail['image'] = '';
            $after_order_item_detail['num'] = 1;
            $after_order_items_info[] = $after_order_item_detail;
            $afterSaleItem->afterSaleItemInsert($after_order_item_detail);
        }
        return $after_order_items_info;
    }
    
    public function createAfterSaleOrder($originalOrderInfo, $question_desc)
    {
        $afterOrderSale = new AfterOrderSale();

        $afterOrderMainInfo = $this->afterMainOrderInfo($originalOrderInfo['main'] ,$question_desc);
        $after_sale_id = $afterOrderSale->afterSaleOrderInsert($afterOrderMainInfo);
        $after_items_order_list = $this->afterItemsOrderInfo($originalOrderInfo['items'], $after_sale_id);
        return $after_items_order_list;
    }


}