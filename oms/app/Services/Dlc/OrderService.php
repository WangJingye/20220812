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
                "\t" . $v['order_sn'],
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

    public static function exportFinanceOrderAll($params)
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
        $res = [];
        $api = app('ApiRequestInner', ['module' => 'member']);

        $sortKeys = ['appid', 'mchid', 'transaction_id', 'order_sn', 'openid', 'trade_type', 'trade_status', 'total_amount',
            'refund_no', 'refund_time', 'refund_type', 'refund_status', 'product_name', 'order_amount', 'pay_amount', 'refund_amount',
            'balance_order_sn', 'is_balance', 'pay_time', 'mobile', 'gold_amount', 'remain_balance', 'act_balance'];
        foreach ($list as $v) {
            $v = json_decode(json_encode($v), true);

            $other = DB::table('oms_order_logistics_info')
                ->where('order_id', $v['id'])
                ->where('status', '!=', $v['log_status'])
                ->whereRaw('status in (5,7)')
                ->first();
            $goldInfo = $v['gold_info'] ? json_decode($v['gold_info'], true) : [];
            $res[$v['order_sn']] = [
                'appid' => 'wxf02ceeaa45d1c9f4',//公众账号ID
                'mchid' => '1609867196',//商户号
                'transaction_id' => "\t" . $v['payment_id'],//微信订单号
                'order_sn' => "\t" . $v['order_sn'],//商户订单号
                'openid' => $v['open_id'],//用户标识
                'trade_type' => 'JSAPI',//交易类型
                'trade_status' => 'SUCCESS',//交易状态
                'total_amount' => $v['total_amount'],//应结订单金额
                'product_name' => $v['product_name'],//商城订单商品名称
                'order_amount' => $v['total_amount'],//订单金额
                'pay_amount' => $v['pay_amount'],//现金支付金额
                'is_balance' => '否',//是否储值卡订单
                'gold_amount' => $v['gold_amount'],//储值卡消费金额
                'mobile' => $v['mobile'],//消费者手机号
                'remain_balance' => $goldInfo['remain_balance'] ?? '',
                'balance_order_sn' => '',
                'act_balance' => ($goldInfo['act_balance'] ?? 0) + $v['pay_amount'],
            ];
            if ($v['log_status'] == 5) {
                $res[$v['order_sn']]['pay_time'] = $v['log_created'];//支付时间
                if (!empty($other)) {
                    $other = json_decode(json_encode($other), true);
                    $res[$v['order_sn']]['refund_no'] = "\t" . $v['pay_order_sn'];//商户退款单号
                    $res[$v['order_sn']]['refund_amount'] = $v['pay_amount'];//退款金额
                    $res[$v['order_sn']]['refund_time'] = $other['created_at'];//退款时间
                    $res[$v['order_sn']]['refund_type'] = 'JSAPI';//退款类型
                    $res[$v['order_sn']]['refund_status'] = 'SUCCESS';//退款状态
                }
            } else {
                $other = json_decode(json_encode($other), true);
                $res[$v['order_sn']]['pay_time'] = $other['created_at'];//支付时间
                $res[$v['order_sn']]['refund_no'] = "\t" . $v['pay_order_sn'];//商户退款单号
                $res[$v['order_sn']]['refund_amount'] = $v['pay_amount'];//退款金额
                $res[$v['order_sn']]['refund_time'] = $v['log_created'];//退款时间
                $res[$v['order_sn']]['refund_type'] = 'JSAPI';//退款类型
                $res[$v['order_sn']]['refund_status'] = 'SUCCESS';//退款状态
            }
        }
        //购物金充值退款记录
        $response = $api->request('getBalanceLogAll', 'POST', $params);
        if ($response['code'] != 1) {
            throw new \Exception('获取储值卡充值退款记录失败');
        }
        $list = $response['data'];
        foreach ($list as $v) {
            //手动充值
            if ($v['order_type'] == 2) {
                $res[$v['order_sn']] = [
                    'appid' => '',//公众账号ID
                    'mchid' => '',//商户号,
                    'transaction_id' => '',//
                    'order_sn' => "\t" . $v['order_sn'],
                    'openid' => $v['open_id'],
                    'trade_type' => '手动充值',//交易类型
                    'trade_status' => 'SUCCESS',//交易状态
                    'total_amount' => $v['recharge_amount'],
                    'pay_amount' => $v['recharge_amount'],
                    'refund_no' => '',
                    'refund_time' => '',
                    'refund_type' => '',
                    'refund_status' => '',
                    'product_name' => $v['order_title'],
                    'order_amount' => $v['recharge_amount'],
                    'refund_amount' => '',
                    'balance_order_sn' => "\t" . $v['order_sn'],
                    'is_balance' => '是',
                    'pay_time' => $v['created_at'],
                    'mobile' => $v['phone'],
                    'gold_amount' => $v['balance'],
                    'remain_balance' => $v['remain_balance'],
                    'act_balance' => $v['recharge_amount']
                ];
            } else {
                $res[$v['order_sn']] = [
                    'appid' => 'wxf02ceeaa45d1c9f4',//公众账号ID
                    'mchid' => '1609867196',//商户号,
                    'transaction_id' => "\t" . $v['transaction_id'],//
                    'order_sn' => "\t" . $v['order_sn'],
                    'openid' => $v['open_id'],
                    'trade_type' => 'JSAPI',//交易类型
                    'trade_status' => 'SUCCESS',//交易状态
                    'total_amount' => $v['order_amount'],
                    'pay_amount' => $v['order_amount'],
                    'refund_no' => $v['refund_time'] ? $v['order_sn'] : '',
                    'refund_time' => $v['refund_time'],
                    'refund_type' => $v['refund_time'] ? 'JSAPI' : '',
                    'refund_status' => $v['refund_time'] ? 'SUCCESS' : '',
                    'product_name' => $v['order_title'],
                    'order_amount' => $v['order_amount'],
                    'refund_amount' => $v['refund_amount'],
                    'balance_order_sn' => "\t" . $v['order_sn'],
                    'is_balance' => '是',
                    'pay_time' => $v['pay_time'],
                    'mobile' => $v['phone'],
                    'gold_amount' => $v['balance'],
                    'remain_balance' => $v['remain_balance'],
                    'act_balance' => $v['recharge_amount']
                ];
            }
        }
        $data = [];
        foreach ($res as $k => $v) {
            $item = [];
            foreach ($sortKeys as $key) {
                $item[$key] = $v[$key] ?? '';
            }
            $sort[] = $v['pay_time'];
            $data[] = $item;
        }
        array_multisort($sort, SORT_ASC, $data);
        $title = [
            '公众账号ID', '商户号', '微信订单号', '商户订单号', '用户标识', '交易类型', '交易状态', '应结订单金额',
            '商户退款单号', '退款时间', '退款类型', '退款状态', '商城订单商品名称', '订单金额', '现金支付金额', '申请退款金额',
            '储值卡订单号', '是否储值卡订单', '支付时间', '消费者手机号',
            '储值卡消费金额', '储值卡余额', '实际消费金额'
        ];
        array_unshift($data, $title);
        return $data;
    }

}
