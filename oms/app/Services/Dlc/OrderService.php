<?php namespace App\Services\Dlc;

use App\Model\{OmsOrderStatus, Order};
use Illuminate\Support\Facades\DB;

class OrderService
{
    public static function exportWarehouseOrder()
    {
        $data[] = [
            'OrderDate', 'HUSR06', 'Externorderkey', 'SKU', 'Lottable03', 'OPENQTY', 'Notes', 'C_company',
            'C_Contact1', 'C_Phone1', 'C_Address1', 'Others'
        ];
        $orders = Order::with('orderDataItem')
            ->where('order_status', 3)->get();
        $virtual_prod_prefix = 'VIRTUAL_';
        foreach ($orders as $order) {
            foreach ($order->orderDataItem as $item) {
                //过滤掉虚拟商品
                if ((strpos($item->sku, $virtual_prod_prefix) === 0)) {
                    continue;
                }
                $data[] = [
                    $order->created_at->format('Y-m-d'),
                    $order->created_at->format('Y-m-d'),
                    "\t" . $order->order_sn,
                    "\t" . $item->sku,
                    '良品',
                    $item->qty,
                    '大于15个月',
                    '微商城直营订单',
                    $order->contact,
                    "\t" . $order->mobile,
                    "{$order->province}{$order->district}{$order->city}{$order->address}",
                    ''
                ];
            }
        }
        return $data;
    }

    public static function exportFinanceOrder($params)
    {
        $query = DB::table('oms_order_logistics_info as c')
            ->leftJoin('oms_order_main AS a', 'a.id', '=', 'c.order_id')
            ->leftJoin('oms_order_items AS b', 'a.id', '=', 'b.order_main_id');
        if (isset($params['mobile']) && $params['mobile'] !== '') {
            $query->where('a.mobile', $params['mobile']);
        }
        $query->whereRaw('(c.status = 5 or c.status = 7)');
        if (isset($params['payment_type']) && $params['payment_type'] !== '') {
            $query->where('a.payment_type', $params['payment_type']);
        }
        if (!empty($params['start_time'])) {
            $query->where('c.created_at', '>=', $params['start_time']);
        }
        if (!empty($params['end_time'])) {
            $query->where('c.created_at', '<=', $params['end_time']);
        }
        if (isset($params['pos_id']) && $params['pos_id'] !== '') {
            $query->where('a.pos_id', $params['pos_id']);
        }
        if (isset($params['contact']) && $params['contact'] !== '') {
            $query = $query->where('a.contact', 'like', "%" . $params['contact'] . '%');
        }
        if (!empty($params['order_payment_type'])) {
            $query = $query->where('payment_type', $params['order_payment_type']);
        }
        if (isset($params['order_sn']) && $params['order_sn'] !== '') {
            $query->where('a.order_sn', $params['order_sn']);
        }
        if (isset($params['order_type']) && $params['order_type'] !== '') {
            $query->where('a.order_type', $params['order_type']);
        }
        if (!empty($params['is_exception'])) {
            $array = array_keys($params['is_exception']);
            $query = $query->whereIn('is_exception', $array);
        }
        if (isset($params['goods_name']) && $params['goods_name'] !== '') {
            $query = $query->whereRaw('exists(select id from oms_order_items c where c.order_main_id = a.id and c.name like \'%' . $params['goods_name'] . '%\')');
        }
        $list = $query->select(['a.*', 'b.name as product_name', 'c.status as log_status', 'c.created_at as log_created'])
            ->orderBy('c.id', 'asc')
            ->groupBy(['c.id'])
            ->get()->toArray();
        $data[] = [
            '手机号码', '用户姓名', '订单编号', '产品名称', '下单时间', '支付时间',
            '退款时间', '订单状态', '订单金额', '支付类型', '微信支付金额'
        ];
        $pay_list = [
            0 => '',
            1 => '支付宝',
            2 => '微信',
            3 => '银联',
            4 => '花呗',
            5 => '货到付款',
            10 => '储值卡',
            11 => '组合'
        ];
        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        //订单数据
        foreach ($list as $v) {
            $v = json_decode(json_encode($v), true);
            $data[] = [
                $v['mobile'],
                $v['contact'],
                $v['order_sn'],
                $v['product_name'],
                $v['created_at'],
                $v['transaction_time'],
                $v['log_status'] == 8 ? $v['log_created'] : '',
                $v['log_status'] == 8 ? '退款' : '支付',
                sprintf("%1\$.2f", $v['total_amount']),
                $pay_list[$v['payment_type'] ?? ''] ?? '',
                $v['log_status'] == 8 ? sprintf("%1\$.2f", -$v['pay_amount']) : sprintf("%1\$.2f", $v['pay_amount']),
            ];
        }
        //购物金充值退款记录
        $api = app('ApiRequestInner', ['module' => 'member']);
        $res = $api->request('exportBalanceLog', 'POST', $params);
        if ($res['code'] == 1 && count($res['data'])) {
            unset($res['data'][0]);
            $data = array_merge($data, $res['data']);
        }
        return $data;
    }

}
