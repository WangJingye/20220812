<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class OrderRepository
{
    public static $OrderStatus = [
        ['value'=>'WAIT_BUYER_PAY','name'=>'待付款','show'=>1],
        ['value'=>'WAIT_SELLER_SEND_GOODS','name'=>'待发货','show'=>1],
        ['value'=>'SELLER_CONSIGNED_PART','name'=>'部分发货','show'=>0],
        ['value'=>'WAIT_BUYER_CONFIRM_GOODS','name'=>'已发货','show'=>1],
        ['value'=>'TRADE_BUYER_SIGNED','name'=>'确认收货','show'=>0],
        ['value'=>'TRADE_FINISHED','name'=>'交易成功','show'=>0],
        ['value'=>'TRADE_CLOSED_BY_TAOBAO','name'=>'已取消','show'=>1],
        ['value'=>'REFUNDING','name'=>'退货中','show'=>1],
        ['value'=>'TRADE_CLOSED','name'=>'已退款','show'=>1],
    ];

    public static $PaymentMethod = ['upayAli'=>'支付宝','upayWx'=>'微信'];

    public static function getList($customer_id,...$args){
        $status = $args[0];
        $size = $args[1];
        $model = DB::table('sales_order_grid');
        if($status){
            $model->where('sales_order_grid.status',$status);
        }
        $model->where('sales_order_grid.customer_id',$customer_id)
            ->where('sales_order_item.product_type','simple')
            ->join('sales_order_item', 'sales_order_item.order_id', '=', 'sales_order_grid.entity_id')
            ->orderBy('sales_order_grid.entity_id','desc')
            ->select([
                'sales_order_grid.status as order_status',
                'sales_order_grid.increment_id as order_id',
                'sales_order_grid.created_at as order_time',
                'sales_order_item.sku as sku',
                'sales_order_item.name as name',
                'sales_order_item.price as price',
                'sales_order_item.qty_ordered as quantity',
                'sales_order_grid.payment_method as order_payment_method',
                'sales_order_grid.grand_total as order_grand_total',
            ]);
        return $model->paginate($size);
    }

    /**
     * 获取订单详情
     * @param $increment_id
     * @param null $customer_id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getDetail($increment_id,$customer_id = null){
        $model = DB::table('sales_order_grid');
        if($customer_id){
            $model->where('customer_id',$customer_id);
        }
        $model->where('increment_id',$increment_id)
            ->select([
                'entity_id',
                'grand_total',
                'subtotal',
                'created_at',
                'shipping_and_handling',
                'status',
                'payment_method',
                'increment_id',
            ]);
        return $model->first();
    }

    public static function getItems($order_id){
        return DB::table('sales_order_item')
            ->where('order_id',$order_id)
            ->select([
                'sku',
                'name',
                'price',
                'row_total',
                'qty_ordered',
            ])->get();
    }

    public static function getAddress($order_id){
        return DB::table('sales_order_address')
            ->where('parent_id',$order_id)
            ->where('address_type','shipping')
            ->select([
                'entity_id',
                'firstname',
                'telephone',
                'region',
                'city',
                'area',
                'street',
                'postcode',
            ])->get();
    }

    /**
     * 根据订单号获取发票
     * @param $increment_id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getInvoice($increment_id){
        return DB::table('ot_invoice')->where('tid',$increment_id)->first();
    }

    /**
     * 获取退货物流信息
     * @param $increment_id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getReturnsLogistics($increment_id){
        return DB::table('returns_logistics')->where('tid',$increment_id)->first();
    }

    /**
     * 保存退货物流信息
     * @param $data
     * @return bool
     */
    public static function setReturnsLogistics($data){
        return DB::table('returns_logistics')->insert($data);
    }
}
