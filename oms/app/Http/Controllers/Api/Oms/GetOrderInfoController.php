<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/1
 * Time: 16:26
 */

namespace App\Http\Controllers\Api\Oms;

use App\Http\Requests\getOrderInfoRequest;
use App\Http\Controllers\Controller;
use App\Model\OmsOrderStatus;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;
use function AlibabaCloud\Client\json;

class GetOrderInfoController extends Controller
{
    /**
     * 根据订单号 order_sn 查询商品订单详情
     * @param Order $order
     * @param Request $request
     * @return array|mixed
     */
    public function getOrderInfoByOmsId(Order $order, Request $request)
    {
        $order_sn = $request->input("order_sn", '');
        $pay_order_sn = $request->input("pay_order_sn", '');
        $type = $request->input("type", '');
        $order_info = $order::orderInfo($order_sn, $pay_order_sn, $type);

        return $this->success('success', $order_info);
    }

    /**
     * 根据pos_id分页获取订单列表
     * @param Request $request
     * @return mixed
     */
    public function getOrderInfoList(Order $order, Request $request)
    {
        $from = $request->header('from');
        if ($from != 3) {
            $oms_order_list = $order::orderList($request->pos_id, $request->page, 1000);
        } else {
            $oms_order_list = $order::orderList($request->pos_id, $request->page, 100);
        }

        return $this->success('', $oms_order_list);
    }

    public function getOrderInfoDetail(Order $order, Request $request)
    {
        $v = Validator::make($request->all(), [
            "order_sn" => 'required',
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        $uid = $this->getUid();
        if (!$uid) return $this->expire();
        $order_sn = $request->input("order_sn", '');
        $order_info = $order::orderInfoDetail($uid, $order_sn);
        return $this->success('success', $order_info);
    }

    public function getOrderInfoLists(Order $order, Request $request)
    {
        $uid = $this->getUid();
        if (!$uid) return $this->expire();
        $page = $request->page ? (intval($request->page)) : 1;
        $order_status = $request->get('order_status');
        $data = $order::orderListsByStatus($uid, $order_status, $page, 100);
        return $this->success('OK', $data);
    }

    /**
     * 根据pos_id分页获取订单列表
     * @param Request $request
     * @return mixed
     */
    public function getOrderList(Order $order, Request $request)
    {
        $oms_order_list = $order::orderList($request->pos_id, $request->page);
        return $this->success('', $oms_order_list);
    }

    /**
     * 根据pos_id分页获取订单列表
     * @param Request $request
     * @return mixed
     */
    public function getOrderListLimit(Order $order, Request $request)
    {

        $oms_order_list = $order::orderListLimit($request->pos_id, $request->page);
        return $this->success('', $oms_order_list);
    }

    /**
     * 查询子订单详情
     * @param Request $request
     */
    public function getOrderInfoByItemsId(Request $request)
    {
        $itemsId = $request->input("itemsId");
    }

    /**
     *
     * @param Request $request
     */
    public function getOrderInfoByOpenId(Request $request)
    {
        $openId = $request->input("openId");
    }

    /**
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @author Steven
     */
    public function getAchieveList(Order $order, Request $request)
    {
        $uid = \App\Support\Token::getUidByToken($request->header('token'));
        if (empty($uid)) {
            return ['code' => 2, 'message' => '未登录'];
        }
        $time = time();
        $guide_id = $request->get('guide_id');
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date', date('Y-m-d', $time));
        $date_type = $request->get('date_type');
        if ($date_type == 'week') {
            $start_date = date('Y-m-d', strtotime('-7' . ' days', $time));
        } elseif ($date_type == 'month') {
            $start_date = date('Y-m-d', strtotime('-30' . ' days', $time));
        }
        $oms_order_list = $order::getAchieveList($guide_id, 10, compact('start_date', 'end_date'));
        return $this->success('', $oms_order_list);
    }

    /**
     * 查询订单
     * @param Request $request
     */
    public function getOrderInfo(Request $request)
    {
        $params = $request->all();
        $orderInfo = Order::query()->where('order_sn', $params['order_sn'])->first();
        $res = json_decode(json_encode($orderInfo), true);
        list($states_info, $status_info) = (new OmsOrderStatus())->getStatusMap();
        $res['status_name'] = $status_info[$res['order_status']];
        return $this->success('success', $res);
    }

}
