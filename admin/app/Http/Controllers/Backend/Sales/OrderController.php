<?php

namespace App\Http\Controllers\Backend\Sales;

use App\Model\Order;
use App\Model\Member;
use App\Model\OrderGoods;
use http\QueryString;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Promotion\Category;
use Illuminate\Support\Facades\DB;
use App\Service\OrderTrait;

class OrderController extends Controller
{
    use OrderTrait;

    function __construct()
    {
        parent::__construct();
    }

    public function getList(Request $request)
    {
        $name = request('name');
        $orderSn = request('order_sn');
        $customerId = request('customer_id');
        $phoneCode = request('phone_code');
        $phone = request('phone');
        $startTime = request('start_time');
        $endTime = request('end_time');
        $status = request('status');
        $query = Order::query();
        if ($name) {
            $orderIdArr = OrderGoods::where('name', 'like', "%" . $name . '%')->pluck('order_id')->toArray();
            $query = $query->whereIn('id', $orderIdArr);
        }
        if ($orderSn) {
            $query = $query->where('order_sn', $orderSn);
        }
        if ($customerId) {
            $query = $query->where('customer_id', $customerId);
        }
        if ($phone) {
            $query = $query->where([['phone', '=', $phone], ['phone_code', '=', $phoneCode]]);
        }

        if ($startTime) {
            $query = $query->where('created_at', '>', $startTime);
        }
        if ($endTime) {
            $query = $query->where('created_at', '<', $endTime);
        }

        if ($status) {
            if ($status == 'pending') {
                $query = $query->where('status', 'pending');
            }
            if ($status == 'paid') {
                $query = $query->whereIn('status', ['paid',]);
            }
            if ($status == 'pending-shiped') {
                $query = $query->whereIn('status', ['shipping',]);
            }
            if ($status == 'finished-shiped') {
                $query = $query->whereIn('status', ['shipped', 'ready_for_collection']);
            }
            if ($status == 'after-sales') {
                $sql = 'select order_id from order_goods 
                        where oms_status = "SYSCANCEL" 
                        or status="refund"
                        or status= "returned"
                        or assr_status = "WAITRECV" 
                        or assr_status = "WAITPACK" ';
                $query = $query->whereRaw('((id in (' . $sql . ')) or (orders.status = ? ))', ['syscancel']);
            }
            if ($status == 'cancel') {
                $query = $query->where('status', 'cancel');
            }
        }
        $query = $query->orderBy('id', 'desc');

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->getList($request);
        $list = $query->paginate($this->pageSize)->withPath('/' . request()->route()->uri);;

        return view('backend.sales.order.index', ['list' => $list, 'action' => $this]);
    }

    public function edit(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/get', $all);
        $detail = $data['data'];

        return view('backend.sales.order.edit', [
            'order' => $detail['order_after_sale'],
            'gift_goods' => $detail['gift_goods'],
        ]);
    }

    //拆单，相同物流单号的，放在一起
    public function splitOrder($order)
    {
        $orderGoods = $order->goods;
        if ($order->service_type == 'address') {
            foreach ($orderGoods as $key => $value) {
                $shipping_id = $value->shipping_id;
                $goods[$shipping_id][] = $value;
            }
        } elseif ($order->service_type == 'pickSelf') {
            foreach ($orderGoods as $key => $value) {
                $pick_code = $value->pick_code;
                $goods[$pick_code][] = $value;
            }
        }
        rsort($goods);
        return $goods;
    }


    public function getStatusFilter($status)
    {
        switch ($status) {
            //所有
            case 'all':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'all']));
                break;
            //待付款
            case 'pending':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'pending']));
                break;
            //已支付                
            case 'paid':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'paid']));
                break;
            //待发货
            case 'pending-shiped':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'pending-shiped']));
                break;
            //已发货
            case 'finished-shiped':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'finished-shiped']));
                break;
            //售后
            case 'after-sales':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'after-sales']));
                break;
            //已关闭
            case 'cancel':
                return url('admin/sales/order') . toQuery(array_merge(request()->all(), ['status' => 'cancel']));
                break;

        }
    }


    function getActionStatus($status)
    {
        $currentStatus = request('status', 'all');
        if ($currentStatus == $status) {
            return 'layui-btn-normal';
        } else {
            return 'layui-btn-primary';
        }
    }

    public function getExpressInfo()
    {
        $response = $this->curl('sales/order/getExpressInfo', request()->all());
        $list = $response['data']['list'] ?? [];
        $msg = [];
        foreach ($list as $item) {
            $msg[] = $item['time'] . ',' . $item['content'];
        }
        $msg = implode('|', $msg);
        $response['data']['msg'] = $msg;
        return $response;
    }

    public function _export(Request $request)
    {
        $list = $this->getList($request)->get();
        $data = [];
        foreach ($list as $order) {

            $item = [
                "\t" . $order->order_sn,//订单号
                "\t" . (string)$order->created_at,//下单时间
                "\t" . $order->transaction_id,//支付订单编号
            ];
            $data[] = $item;

        }
        $result = [
            'columns' => [
                '商城订单号', '下单时间', '订单状态'
            ],
            'value' => $data,
        ];
        return $this->success($result);
    }


    public function refund(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/refund', $all);

        return $data;


    }

    public function refundInfo(Request $request)
    {
        $all = $request->all();
        $data = $this->curl('order/get', $all);

        $detail = $data['data'];

        if ($request->type == 2) {

            return view('backend.sales.order.unsendRefund', [
                'order' => $detail,
            ]);
        }
        return view('backend.sales.order.refund', [
            'order' => $detail['order_after_sale'],
        ]);

    }

    public function afterSale(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/get', $all);

        $detail = $data['data'];

        return view('backend.sales.order.add', [
            'order' => $detail,
        ]);


    }

    public function afterSaleAction(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/makeAfterSale', $all);
        return $data;
    }


}
