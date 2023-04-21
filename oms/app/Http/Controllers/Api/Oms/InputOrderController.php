<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/12
 * Time: 10:19
 */

namespace App\Http\Controllers\Api\Oms;

use App\Http\Requests\InputOrderFromCartRequest;
use App\Http\Controllers\Controller;
use App\Model\OmsInvoice;
use Validator;
use App\Model\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Model\OrderPaySuccessReport;

class InputOrderController extends Controller
{
    /**
     * 创建订单
     * @param Order $order
     * @param InputOrderFromCartRequest $request
     * @return array|mixed
     */
    public function NewOrderFromCart(Order $order, Request $request)
    {

        $order_concent = $request->getContent();
        Log::info('create_order:' . $order_concent, []);
        $order_info = json_decode($order_concent, true);

        if (!is_array($order_info)) {
            return $this->error('参数解析数据异常');
        }
        list($code, $message, $data) = $order->createOrder($order_info);
        if ($code == 1) {
            return $this->success($message, $data);
        }

        return $this->error($message, $data);

    }

    /**
     * 支付成功
     * @param InputOrderFromCartRequest $request
     * @return array|mixed
     */
    public function paySuccessOrder(Order $order, Request $request)
    {

        $v = Validator::make($request->all(), [
            "trade_no" => 'required',
            "pay_order_sn" => 'required',
            "type" => 'required',
            "tradeType" => 'required',
            "payTime" => 'required'
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());

        }
        $data = $request->all();
        $status = $order::payOrder($data);
        if ($status) {
            return $this->success('success');
        }

        return $this->error('失败，请重试');
    }


    /**
     * 支付成功
     * @param InputOrderFromCartRequest $request
     * @return array|mixed
     */
    public function payUpdateOrder(Order $order, Request $request)
    {
        $data = $request->all();
        $v = Validator::make($data, [
            "pay_order_sn" => 'required',
            "order_sn" => 'required',
            "type" => 'required',
            "tradeType" => 'required',
            "payment" => 'required',
            "time" => 'required',
            'pay_amount'=>'required'
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());

        }
        $status = $order::payUpdateOrder($data);
        if ($status) {
            return $this->success('success');
        }

        return $this->error('失败，请重试');
    }

    public function payUpdateAndSuccessOrder(Order $order, Request $request){
        $data = $request->all();
        $v = Validator::make($data, [
            "pay_order_sn" => 'required',
            "order_sn" => 'required',
            "type" => 'required',
            "tradeType" => 'required',
            "payment" => 'required',
            "pay_amount"=>'required',
            "time" => 'required',
            "trade_no" => 'required',
            "payTime" => 'required'
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        if (!$order::payUpdateOrder($data)) {
            return $this->error('失败，请重试');
        }
        if (!$order::payOrder($data)) {
            return $this->error('失败，请重试');
        }
        return $this->success('success');
    }

    public function addInvoice(Request $request)
    {

        $data = $request->all();
        $v = Validator::make($data, [
            "order_sn" => 'required',
            "title" => 'required',
            "type" => 'required',
            "number" =>'alpha_num|max:20',
            "email" => 'required|email'
        ], [
            'order_sn.required' => '订单号不能为空',
            "title.required" => '发票抬头不能为空',
            "type.required" => '开票类型不能为空',
            "number.alpha_num" => '纳税人识别号必须是字母和数字',
            "number.max" => '纳税人识别号最大20位',
            "email.required" => '邮箱不能为空',
            "email.email" => '必须是邮箱格式'
        ]);
        try {
            if ($data['type'] == 'company' && empty($data['number'])) {
                return $this->error('公司税号不能为空');
            }
            if (!isset($data['number'])) {
                $data['number'] = '';
            }
            if ($v->fails()) {
                $return['field'] = $v->getMessageBag()->keys()[0];
                return $this->error($v->errors()->first(), $return);

            }
            $orderinfo = Order::where('order_sn', $data['order_sn'])->first();
            if (empty($orderinfo)) {
                return $this->error('未找到订单信息');
            }
            $invioce = OmsInvoice::select('id')->where('order_sn', $data['order_sn'])->first();
            if ($invioce) {
                return $this->error('发票信息已存在');
            }


            if ($data['type'] != 'company') {
                $data['type'] = 'person';
            }
            $status = OmsInvoice::updateOrCreate(
                ['order_sn' => $data['order_sn']],
                ['order_sn' => $data['order_sn'], 'pos_id' => $orderinfo['pos_id'], 'total_free' => $orderinfo['pay_amount'], 'title' => $data['title'], 'type' => $data['type'], 'email' => $data['email'], 'number' => $data['number']]
            );
            Order::where('order_sn', $data['order_sn'])->update(['has_invoice' => 1]);
            if (!$status) {
                return $this->error('开票失败');
            }
            return $this->success('开票成功');
        } catch (\Exception $e) {

            return $this->error('开票失败');
        }
    }

    /**
     * 支付成功上报
     */
    public function paySuccessReport(Request $request)
    {
        $v = Validator::make($request->all(), [
            "actionField" => 'required'
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        try {
            $result = OrderPaySuccessReport::paySuccessReport($request->all());
            if ($result) {
                return $this->success('上报成功');
            } else {
                return $this->error('上报失败');
            }
        } catch (\Exception $e) {
            return $this->error('上报失败');
        }
    }
}
