<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/1
 * Time: 16:26
 */

namespace App\Http\Controllers\Backend\Oms;


use App\Http\Controllers\Api\ApiController;
use App\Model\OmsInvoice;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\OmsOrderStatus;
use App\Model\Sku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class OrdersController extends ApiController
{

    public function filterStatus($status)
    {
        switch ($status) {
            //所有
            case 'all':
                return null;
                break;
            //待付款
            case 'pending':
                return [1];
                break;
            //待发货
            case 'paid':
                return [3, 12];
                break;
            //已发货
            case 'shipped':
                return [4];
                break;
            //售后
            case 'after-sales':
                return [5, 7, 8, 13];
                break;
            //已取消
            case 'canceled':
                return [2, 11];
                break;
            //已完成
            case 'finished':
                return [9, 10];
                break;
            case 'pending-refunded':
                return [5];
                break;
            case 'refunded':
                return [7];
                break;
        }
    }

    public function trimall($str)
    {
        $qian = array(" ", '"', "　", "\t", "\n", "\r");
        return str_replace($qian, '', $str);
    }

    /**
     * 列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $input = $request->all();
        $inputs = $input;
        array_walk_recursive($input, function (&$input) {
            $input = $this->trimall($input);
        });
        $request = $request->merge($input);
        $limit = $request->limit ?: 10;

        $order = new Order;
        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $channel_array = [
            1 => '小程序',
            2 => '电脑',
            3 => '手机',
            0 => 'old',
            4 => '手工'
        ];
        $orders = $order;
        $order = $order->with(['comment','returnapply','orderInvoice']);
        if ($request->has('is_apply_return')) {
            $order = $order->where('is_apply_return', $request->is_apply_return);
        }
        if ($request->has('is_allow_return')) {
            $order = $order->where('is_allow_return', $request->is_allow_return);
        }
        if ($request->has('is_return_wms')) {
            $order = $order->where('is_return_wms', $request->is_return_wms);
        }
        if ($request->has('order_sn') && $request->get('order_sn')) {
            $order = $order->where('order_sn', $request->order_sn);
        }
        if ($request->has('share_uid') && $request->get('share_uid')) {
            $order = $order->where('share_uid', $request->share_uid);
        }
        if ($request->has('order_type') && $request->get('order_type')) {
            $order = $order->where('order_type', $request->order_type);
        }
        if ($request->has('mobile') && $request->get('mobile')) {
            $order = $order->where('mobile', $request->mobile);
        }
        if ($request->has('coupon_code') && $request->get('coupon_code')) {
            $order = $order->where('coupon_code', $request->coupon_code);
        }
        if ($request->has('coupon_id') && $request->get('coupon_id')) {
            $order = $order->where('coupon_id', $request->coupon_id);
        }
        if ($request->has('is_exception') && $request->get('is_exception')) {
            $array = array_keys($inputs['is_exception']);
            $order = $order->whereIn('is_exception',$array);
        }
        if ($request->has('contact') && $request->get('contact')) {
            $order = $order->where('contact', 'like', "%" . $request->get('contact') . '%');
        }
        if ($request->has('activity_channel') && $request->get('activity_channel')) {
            $order = $order->where('activity_channel', 'like', "%" . $request->get('activity_channel') . '%');
        }
        if ($request->has('activity') && $request->get('activity')) {
            $order = $order->where('activity', 'like', "%" . $request->get('activity') . '%');
        }

        if ($request->has('pay_order_sn') && $request->get('pay_order_sn')) {
            $order = $order->where('pay_order_sn', $request->get('pay_order_sn'));
        }
        if ($request->has('pos_id') && $request->get('pos_id')) {
            $order = $order->where('pos_id', $request->get('pos_id'));
        }
        if ($request->has('order_status') && $request->get('order_status')) {
            if (isset($inputs['order_status'])) {
                $array = array_keys($inputs['order_status']);
                $order = $order->whereIn('order_status', $array)->where('channel', '!=', 0);
            }
        }
        if ($request->has('order_state') && $request->get('order_state')) {
            $array = array_keys($inputs['order_state']);
            $order = $order->whereIn('order_state', $array)->where('channel', '!=', 0);
        }

        if ($request->has('payment_type') && $request->get('payment_type')) {
            $array = array_keys($inputs['payment_type']);
            $order = $order->whereIn('payment_type', $array);
        }
        if (isset($inputs['channel'])) {
            $channcl_array = array_keys($inputs['channel']);
            if (isset($inputs['channel'][-1])) {
                $channcl_array[] = 0;
            }
            $order = $order->whereIn('channel', $channcl_array);
        }

        if ($request->has('status') && $request->get('status')) {
            $status = $this->filterStatus($request->get('status'));
            if ($status) {
                if (in_array(7, $status)) {
                    $order = $order->whereIn('order_status', $status);
                } else {
                    $order = $order->whereIn('order_status', $status)->where('channel', '!=', 0);
                }

            }
        }
        if ($request->has('start_time') && $request->get('start_time')) {

            $order = $order->where('created_at', '>=', $inputs['start_time']);
        }
        if ($request->has('end_time') && $request->get('end_time')) {

            $order = $order->where('created_at', '<', $inputs['end_time']);
        }

        if ($request->has('start_send') && $request->get('start_send')) {

            $order = $order->where('send_at', '>=', $inputs['start_send']);
        }
        if ($request->has('end_send') && $request->get('end_send')) {
            $order = $order->where('send_at', '<', $inputs['end_send']);
        }
        if ($request->has('guide_id')) {
            if ($request->get('guide_id') > 0) {
                $order = $order->where('guide_id', '=', $inputs['guide_id']);
            }else{
                $order = $order->where('guide_id', '>', 0);
            }
        }

        if ($request->has('goods_name') && $request->get('goods_name')) {
            $goods_name = $request->get('goods_name');
            $order = $order->whereHas('orderDataItem', function ($query) use ($goods_name) {
                $query->where('name', 'like', "%" . $goods_name . '%');
            });
        }

        $order = $order->with('orderDataItem');
        if ($request->type == 2) {
            $order = $order->with('orderAfterSale');
            $data = [];
            $order->orderBy('id', 'desc')->chunkById(100, function ($order) use ($channel_array, $states_info, $status_info, &$data) {
                foreach ($order as $v) {
                    $v->channel_name = $channel_array[$v->channel];
                    $v->state_name = $states_info[$v->order_state];
                    $v->status_name = $status_info[$v->order_status];
                    $data[] = $v;
                }
            });

            return $this->success('success', $data);
        }

        $data = $order->orderBy('id', 'desc')->paginate($limit);


        foreach ($data as &$v) {
            $goods_arr = [];
            $v->channel_name = $channel_array[$v->channel];
            $v->state_name = $states_info[$v->order_state];
            $v->status_name = $status_info[$v->order_status];
            $v->next_action = $orders->orderNextStatus($v->order_status);
            $aa = $v->next_action;
            if ($v->order_status == 3 && $v->order_state == 1) {
                $aa[] = [
                    'action_name' => '客服取消',
                    'action' => 'cancel_order_unpay',
                ];
                $v->next_action = $aa;
            }

            foreach ($v['orderDataItem'] as $k => &$item) {
                $goods_arr[$item['gift_group_id']][] = $item->toArray();
            }
            $goods_arr = array_values($goods_arr);
            $v['gift_goods'] = $goods_arr;

        }
        return $this->success('success', $data);
    }

    /**
     * 获取订单详情
     */
    public function getOrder(Request $request)
    {
        $id = $request->id;
        if ($request->type >= 2) {
            $info = Order::where('id', $id)->with('orderDataItem')->with('orderAfterSale')->first();
        } else {
            $status_model = new OmsOrderStatus;
            $info = Order::where('id', $id)->with('orderDataItem')->with('orderInvoice')->with('orderStatusLog')->first();
            list($states_info, $status_info) = $status_model->getStatusMap();
            $info->state_name = $states_info[$info->order_state];
            $info->status_name = $status_info[$info->order_status];
            $info->next_action = $info->orderNextStatus($info->order_status);
            $aa = $info->next_action;
            if ($info->order_status == 3 && $info->order_state == 1) {
                $aa[] = [
                    'action_name' => '客服取消',
                    'action' => 'cancel_order_unpay',
                ];
                $info->next_action = $aa;
            }

        }


        $goods_arr = [];
        foreach ($info['orderDataItem'] as &$item) {
            $item = $item->toArray();
            if ($request->type >= 2) {
                if ($item['after_sale_id']) {
                    $item['applied_rule_ids'] = json_decode($item['applied_rule_ids'], true);
                    $goods_arr[$item['gift_group_id']][] = $item;
                }
            } else {
                $item['applied_rule_ids'] = json_decode($item['applied_rule_ids'], true);
                $goods_arr[$item['gift_group_id']][] = $item;
            }
        }
        $goods_arr = array_values($goods_arr);
        $info['gift_goods'] = $goods_arr;
        return $this->success('success', $info);
    }

    /**
     * 编辑
     */
    public function updateOrder(Request $request)
    {
        $updateData = $request->all();

        DB::beginTransaction();
        $orderinfo = Order::where('order_sn', $updateData['order_sn'])->first()->toArray();
        if (empty($orderinfo)) {
            return $this->error('未找到订单信息');
        };
        if ($updateData['type'] == 1) {
            $update = [
                'city' => $updateData['city'],
                'province' => $updateData['province'],
                'contact' => $updateData['contact'],
                'district' => $updateData['district'],
                'address' => $updateData['address'],
                'mobile' => $updateData['mobile'],
                'card_from' => $updateData['card_from'],
                'card_content' => $updateData['card_content'],
                'card_to' => $updateData['card_to'],
                'remark' => $updateData['remark'],
            ];
            $status_order = Order::where('order_sn', $updateData['order_sn'])->update(
                $update
            );
            $status_invoice = true;
        } else {
            $update = ['has_invoice' => 1];
            $status_order = Order::where('order_sn', $updateData['order_sn'])->update(
                $update
            );
            $status_invoice = OmsInvoice::updateOrCreate(
                ['order_sn' => $updateData['order_sn']],
                ['order_sn' => $updateData['order_sn'], 'pos_id' => $orderinfo['pos_id'], 'total_free' => $orderinfo['total_amount'], 'title' => $updateData['order_invoice']['title'], 'type' => empty($updateData['order_invoice']['number']) ? 'person' : 'company', 'email' => $updateData['order_invoice']['email'], 'number' => $updateData['order_invoice']['number']]
            );
        }
        if ($status_order && $status_invoice) {
            DB::commit();
            return $this->success('修改成功', []);
        } else {
            DB::rollBack();
            return $this->error('修改失败');
        }
    }

    /**
     * 列表，支持模糊查询.
     */
    public function orderItemList(Request $request)
    {

        $limit = $request->limit ?: 10;

        $orderItem = new OrderItem;
        if ($request->order_id) {
            $orderItem = $orderItem->where('order_main_id', $request->order_id);
        }

        $data = $orderItem->paginate($limit)->toArray();
        foreach ($data['data'] as &$v) {
            $v['is_gift'] = $v['is_gift'] == 1 ? '小样' : '';
            $v['is_free'] = $v['is_free'] == 1 ? '赠品' : '';

            if ($v['type'] == 1) $v['type'] = '普通商品';
            if ($v['type'] == 2) $v['type'] = '套装';
            if ($v['type'] == 3) $v['type'] = '固定礼盒';

        }
        $return = [];
        if ($data) {
            $return['pageData'] = $data['data'];
            $return['count'] = $data['total'];

        }
        return $this->success('success', $return);


    }

    /**
     * 操作订单状态
     * @param Request $request
     * @return array|mixed
     */
    public function updateOrderStatus(Request $request)
    {
        $order_sn = $request->get('order_sn');
        $action = $request->get('action');

        $order = new Order;
        $status = $order->doActionOrderStatus($order_sn, $action);
        if ($status) {
            return $this->success('success');
        } else {
            return $this->error('fail');
        }


    }

    /**
     * 增加赠品功能
     */
    public function addOrderGift(Request $request)
    {
        $order_sn = $request->get('order_sn');
        $skus = $request->get('skus', '');
        $operator = $request->get('operator', '');
        if ($request->get('type')) {
            $items = $request->get('items', '');

            return $this->deleteFreeOrder($order_sn, $items, $operator);
        } else {
            if (!$skus) {
                return $this->error('请填写要添加的小样');
            }
            $order = new Order;

            list($code, $message, $data) = $order->addFreeOrder($order_sn, $skus, $operator);
        }

        if ($code == 1) {
            return $this->success($message);
        } else {
            return $this->error($message, $data);
        }
    }


    public function deleteFreeOrder($order_sn, $items, $operator = '')
    {

        try {

            $item_arr = explode(',', $items);
            if (!is_array($item_arr)) {
                return $this->error('请选择要删除的商品');
            }

            $item_arr = array_filter(array_unique($item_arr));
            $inc_num = count($item_arr);
            if (count($item_arr) == 0) {
                return $this->error('请选择要删除的商品');
            }
            $order = Order::select('id', 'total_num', 'order_status', 'order_state', 'channel')->where('order_sn', $order_sn)->first();
            if ($order['order_status'] != 3) {
                return [0, '当前订单状态不允许修改商品赠品信息', []];
            }
            $order_goods = OrderItem::select('sku', 'qty', 'type', 'collections')->whereIn('id', $item_arr)->where('original_price', '<=', 0)->get()->toArray();
            if (count($order_goods) != $inc_num) {
                return $this->error('有商品不是小样，不能删除');
            }
            $stock_sku = [];
            foreach ($order_goods as $v) {
                if ((empty($v['collections']) || $v['collections'] == '[]')) {
                    $stock_sku[] = [$v['sku'], $v['qty'], $order_sn];
                }
                if ($v['type'] == 3) {
                    $stock_sku[] = [$v['sku'], $v['qty'], $order_sn];
                }
            }
            $order->total_num = $order->total_num - $inc_num;
            DB::beginTransaction();

            $item_status = OrderItem::whereIn('id', $item_arr)->update(
                [
                    'order_sn' => '-' . $order_sn,
                    'order_main_id' => '-' . $order['id'],
                ]
            );
            Order::orderLog($order['id'], 0, $operator . '客服删除赠品' . $items);
            if (!$order->save() || !$item_status) {
                DB::rollBack();
                return $this->error('操作失败');

            }

            $sku_model = new Sku;
            $deal_sku = $sku_model->updateBatchStock(json_encode($stock_sku), $order['channel'], 1);
            if (!$deal_sku) {
                DB::rollBack();
                $this->error('网络异常');
            }

            DB::commit();
            return $this->success('操作成功');

        } catch (\Exception $e) {
            Log::info($e);
            DB::rollBack();
            return $this->error('网络异常');

        }

    }


    public function batchDelivery(Request $request)
    {
        $items = $request->get('items', '');
        $item_arr = explode(',', $items);
        if (!is_array($item_arr)) {
            return $this->error('请选择要操作的订单');
        }
        $item_arr = array_filter(array_unique($item_arr));
        if (count($item_arr) == 0) {
            return $this->error('请选择要操作的订单');
        }

        //设置或者取消异常订单
        if ($request->get('method')=='exception') {
            $type = $request->get('type');
            $status = Order::orderExceptionOrCancle(0, $type, $item_arr);
            if ($status) {
                return $this->success('操作成功');
            } else {
                return $this->error('操作失败，请重试');
            }
        }


        $fail = [];
        foreach ($item_arr as $v) {
            $status = Order::orderDeliveryOrCancle($v, 1);
            if (!$status) {
                $fail[] = $v;
            }
        }

        if (empty($fail)) {
            return $this->success('操作成功');
        }

        if (count($item_arr) == count($fail)) {
            return $this->error('操作失败，请重试');
        }


        return $this->success('操作成功', ['ids' => implode(',', $fail)]);

    }

    public function addOrder(Order $order, Request $request)
    {
        $data = $request->all();
        list($is_true, $info) = $this->packageInfo($data);
        if (!$is_true) {
            return $this->error($info);
        }
        list($code, $message, $data) = $order->createOrder($info);
        if ($code == 1) {
            return $this->success($message, $data);
        }

        return $this->error($message, $data);
    }

    public function packageInfo($data)
    {
        //金额信息
        $sku_model = new Sku;
        $sku_data = $sku_model->getSku($data['sku_ids']);
        if ($sku_data['code'] == 0 || empty($sku_data['data'])) {
            return [false, '商品信息异常'];
        }
        $sku_data = array_values($sku_data['data']);
        $total = 0;
        $main_items = [];
        $free_items = [];
        $skus = explode(',', $data['sku_ids']);

        $num_arr = array_count_values($skus);
        foreach ($sku_data as $v) {
            $img = '';
            if (isset($v['sku']['kv_images'][0]['url'])) {
                $img = $v['sku']['kv_images'][0]['url'];
            }
            $num = $num_arr[$v['sku']['sku_id']];
            $price = sprintf("%.2f", $v['sku']['ori_price']);
            if ($v['sku']['ori_price'] > 0) {

                $main_items[] = [
                    'name' => $v['product_name'],
                    'pic' => $img,
                    'short_desc' => $v['product_desc'],
                    'product_id' => $v['product_id'],
                    'sku' => $v['sku']['sku_id'],
                    'original_price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'unit_original_price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'paid_amount' => $price * $num,
                    'qty' => $num,
                    'spec_desc' => $v['sku']['spec_desc'],
                    'spec_property' => $v['sku']['spec_property'],
                    'product_type' => $v['product_type'],
                    'display_type' => $v['display_type'],
                    'collections' => [],
                    'discount' => 0,
                    'applied_rule_ids' => '',
                    'revenue_type' => $v['sku']['revenue_type'],
                    'cats' => $v['rec_cat_id'],
                ];
                $total = $total + $price * $num;
            } else {
                $free_items[] = [
                    'name' => $v['product_name'],
                    'pic' => $img,
                    'short_desc' => $v['product_desc'],
                    'product_id' => $v['product_id'],
                    'sku' => $v['sku']['sku_id'],
                    'original_price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'unit_original_price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'price' => sprintf("%.2f", $v['sku']['ori_price']),
                    'paid_amount' => $price * $num,
                    'qty' => $num,
                    'spec_desc' => $v['sku']['spec_desc'],
                    'spec_property' => $v['sku']['spec_property'],
                    'product_type' => $v['product_type'],
                    'display_type' => $v['display_type'],
                    'collections' => [],
                    'discount' => 0,
                    'applied_rule_ids' => '',
                    'revenue_type' => $v['sku']['revenue_type'],
                    'cats' => $v['rec_cat_id'],

                ];
            }

        }

        $data['goods_list']['goods'][0]['main'] = $main_items;
        $data['goods_list']['free_try'] = $free_items;
        $data['goods_list']['goods'][0]['gifts'] = [];
        if (!isset($data['total'])) {
            $data['total'] = array(
                'total_amount' => $total,
                'total_ship_fee' => 0,
                'total_product_price' => $total,
                'total_discount' => 0,
                'total_point_discount' => 0,
                'total_wrap_fee' => 0,
            );
        }
        if (!isset($data['used_points'])) {
            $data['used_points'] = [
                'points' => 0,
                'money' => 0,
            ];
        }
        if (!isset($data['card'])) {
            $data['card'] = array(

                'from' => '',
                'content' => '',
                'to' => '',

            );
        }
        $data['shipping_address']['district'] = $data['shipping_address']['area'];
        $data['shipping_address']['address_detail'] = $data['shipping_address']['address'];
        $data['trade_type'] = 0;
        $data['customer_id'] = $data['user_id'];
        $data['openid'] = 0;
        $data['coupon_id'] = 0;

        return [true, $data];
    }

    public function guideList(Request $request)
    {
        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $model = Order::query();
        if($request->get('order_sn')){
            $model->where('order_sn',$request->get('order_sn'));
        }
        if($request->get('start_time')){
            $model->where('created_at','>=',$request->get('start_time').' 00:00:00');
        }
        if($request->get('end_time')){
            $model->where('created_at','<=',$request->get('end_time').' 23:59:59');
        }
        if($request->get('guide_id')){
            $model->where('guide_id',$request->get('guide_id'));
        }
//        if($request->get('store_id')){
//            $model->where('store_code',$request->get('store_id'));
//        }
        if($request->get('order_status')){
            $model->where('order_status',$request->get('order_status'));
        }
        $model->whereNotNull('guide_id');
        $model->where('guide_id','<>',0);
//        $model->whereNotNull('store_code');
//        $model->where('store_code','<>',0);

        $list = $model->orderByDesc('id')->paginate($request->get('limit')?:10);
        $orders = $list->items();
        foreach($orders as $k=>$order){
            $orders[$k]['state_name'] = array_get($status_info,$order['order_state']);
            $orders[$k]['status_name'] = array_get($status_info,$order['order_status']);
        }
        return $this->success('success', [
            'data'=>$orders,
            'total'=>$list->total(),
        ]);


    }

    public function export(Request $request)
    {
        $type = $request->get('action');
        if($type=='warehouse'){
            $data = \App\Services\Dlc\OrderService::exportWarehouseOrder();
        }

        return $this->success('success',$data??[]);
    }
}
