<?php

namespace App\Model;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Lib\GuzzleHttp;
use App\Support\Sms;

class AfterOrderSale extends Model
{
    //售后
    protected $table = 'oms_after_sales';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function setRemarkAttribute($remark)
    {
        if ($remark) {
            $this->attributes['remark'] = json_encode($remark);
        }


    }

    public function getRemarkAttribute($remark)
    {
        return json_decode($remark, true);
    }

    public function afterOrderItem()
    {
        return $this->hasMany('App\Model\OrderItem', 'after_sale_id', 'id');
    }

    /**
     * 生成订单号
     */
    public function createAfterOrderNo($order_sn)
    {
        //这边我的想法是每天有一串退单号，这样减少退单id的位数
        $nums = Redis::incr('create_after_order_no' . 'ymd');

        return $order_sn . '-' . $nums;
    }


    /**
     * 下单
     */
    public static function createAfterOrder($data)
    {
        try {

            $redis = Redis::connection('default');
            $f = $redis->setnx("lock_createAfterOrder" . $data['order_id'], date("Y-m-d H:i:s"));
            if ($f != 1) {
                return false;
            }
            $redis->expire("lock_createAfterOrder" . $data['order_id'], 30);
            DB::beginTransaction();
            $self = new static();
            $order_status = Order::select('order_status', 'order_state')->where('id', $data['order_id'])->first();
            $data['remark'] = [['date' => date('Y-m-d H:i:s'), 'note' => $data['remark']]];
            $create_after = [
                'order_main_id' => $data['order_id'],
                'status' => 1,
                'payment_type' => $data['payment_type'],
                'trade_type' => $data['trade_type'] ?? 0,
                'pos_id' => $data['pos_id'],
                'total_pay_amount' => $data['total_pay_amount'],
                'after_sale_no' => $self->createAfterOrderNo($data['order_sn']),
                'express_type' => $data['express_type'],
                'express_no' => $data['express_no'],
                'return_type' => $data['return_type'],
                'return_amount' => $data['refund_amount'],
                'pay_amount' => $data['pay_amount'],
                'original_price' => $data['goods_amount'],
                'question_desc' => $data['question_desc'],
                'remark' => $data['remark'],
                'extra' => $order_status['order_status'] . ',' . $order_status['order_state']
            ];

            Log::info('after', $create_after);

            if ($main_after_order = $self->updateOrCreate(['order_main_id' => $data['order_id']], $create_after)) {
                $after_id = $main_after_order->id;
                if ($data['return_type'] == 1) {
                    OrderItem::where('order_main_id', $data['order_id'])
                        ->update(
                            [
                                'status' => 2,
                                'after_sale_id' => $after_id,

                            ]
                        );
                } else {
                    $after_arr = explode(',', $data['after_ids']);
                    OrderItem::whereIn('id', $after_arr)->update(

                        [
                            'status' => 2,
                            'after_sale_id' => $after_id,

                        ]
                    );
                }


                Order::where('id', $data['order_id'])->update(
                    [
                        'after_sale_id' => $after_id,
                        'order_status' => 8,
                    ]

                );
                Order::orderLog($data['order_id'], 8, "发起售后,退货中");
                $qimen = new QimenDeliveryOrder;
                $data['after_sale_no'] = $create_after['after_sale_no'];
                $status = $qimen->afterSaleExecute($data['order_id'], $data, 1);
                if ($status) {
                    DB::commit();
                    return true;
                }

                DB::rollBack();
                return false;
            }
            throw new Exception("发起售后失败", 0);
        } catch (Exception $e) {
            Log::error('createAfterOrder' . $e);
            DB::rollBack();
            return false;

        }

    }

    /**
     * 更新售后信息
     */
    public static function editAfterOrder($data)
    {

        $redis = Redis::connection('default');
        $f = $redis->setnx("lock_createAfterOrder" . $data['order_id'], date("Y-m-d H:i:s"));
        if ($f != 1) {
            return false;
        }
        $redis->expire("lock_createAfterOrder" . $data['order_id'], 30);
        $self = new static();
        $after_order = $self->where('order_main_id', $data['order_id'])->first();
        $remark = $after_order['remark'];
        $remark[] = ['date' => date('Y-m-d H:i:s'), 'note' => $data['remark']];

        $after_order->remark = $data['remark'];
        $after_order->return_amount = $data['return_amount'];
        $after_order->bank_name = $data['bank_name'];
        $after_order->name = $data['name'];
        $after_order->bank_no = $data['bank_no'];
        $after_order->account_name = $data['account_name'];
        $after_order->remark = $remark;
        $after_order->return_pay_type = $data['return_pay_type'];
        $after_order->status = $data['status'];

        DB::beginTransaction();
        if ($after_order->save()) {
            if ($data['status'] == 3) {
                $qimen = new QimenDeliveryOrder;
                $data['after_sale_no'] = $after_order->after_sale_no;
                $status = $qimen->afterSaleExecute($data['order_id'], $data, 2);
                if ($status === true) {
                    $status_arr = explode(',', $after_order->extra);
                    if (is_array($status_arr)) {
                        Order::where('id', $data['order_id'])->update(
                            [
                                'after_sale_id' => 0,
                                'order_status' => $status_arr[0],
                                'order_state' => $status_arr[1],
                            ]

                        );
                    }
                    OrderItem::where('order_main_id', $data['order_id'])->update(
                        [
                            'status' => 2,
                        ]
                    );
                    Order::orderLog($after_order['order_main_id'], 8, "取消售后");
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            Order::orderLog($after_order['order_main_id'], 8, "修改售后信息");
            DB::commit();
            return true;
        }
        DB::rollBack();
        return false;
    }


    /**
     *
     */
    public static function returnMoney($order_id, $type = 1, $data = [])
    {
        $redis = Redis::connection('default');
        $f = $redis->setnx("lock_returnMoney" . $order_id, date("Y-m-d H:i:s"));
        if ($f != 1) {
            return false;
        }
        $redis->expire("lock_returnMoney" . $order_id, 5);
        $self = new static();
        Log::info('returnMoney:request' . $type . $order_id, []);
        DB::beginTransaction();
        if ($type == 1) {
            $order = Order::where('id', $order_id)->first();
            if (!$order['pay_order_sn']) {
                $order['pay_order_sn'] = $order['order_sn'];
            }
            $sources = [2 => 'WeixinPay'];
            if ($order['payment_type'] == 10) {
                $sources = [10 => 'GoldPay'];
            } else if ($order['payment_type'] == 11) {
                $sources = [2 => 'WeixinPay', 10 => 'GoldPay'];
            }
            foreach ($sources as $paymentType => $source) {
                if ($paymentType == 2) {
                    $params = [
                        'order_sn' => $order['pay_order_sn'],
                        'refund_fee' => bcadd($order['pay_amount'], 0, 2),
                        'total_fee' => bcadd($order['pay_amount'], 0, 2),
                        'order_id' => $order['id'],
                        'openid' => $order['open_id'],
                        'type' => $paymentType,
                        'trade_type' => $order['trade_type'],
                        'ori_tran_date' => date('Ymd', strtotime($order['transaction_date']))
                    ];

                    $success = self::doRefund($params);
                    if (!$success) {
                        return false;
                    }
                } else {
                    $item = OrderItem::query()->where('order_main_id', $order['id'])->first();
                    $api = app('ApiRequestInner', ['module' => 'member']);
                    $params = [
                        'order_sn' => $order['order_sn'],
                        'order_title' => $item['name'],
                        'balance' => $order['gold_amount'],
                        'user_id' => $order['user_id'],
                    ];
                    $info = $api->request('refundBalance', 'POST', $params);
                    if (isset($info['code']) && $info['code'] == 0) {
                        return false;
                    }

                }
            }

            if ($order['payment_type'] != 3 && $order['payment_type'] != 5) {
                $order->order_status = 7;
                $order->return_pay_at = date('Y-m-d H:i:s');
            }

            if ($order['payment_type'] == 3) {
                $order->order_state = 11;
            }

            if ($order->save()) {
                if ($order['coupon_id']) {
                    Coupon::revertCoupon($order['user_id'], $order['coupon_id']);
                }
                Order::skuReturn($order->order_sn, $order->channel, $type = 1);
                DB::commit();
                if ($order['payment_type'] == 3) {
                    Order::orderLog($order->id, 5, "银联退款提交成功，等待退款回调");
                } else {
                    Order::orderLog($order->id, 8, "退款完成");
                }
                return true;
            }

            return false;
        } else {
            $order = Order::where('id', $order_id)->with('orderAfterSale')->first();
            $pay_order = $self->where('order_main_id', $order_id)->first();
            $remark = $order['orderAfterSale']['remark'];
            $remark[] = ['date' => date('Y-m-d H:i:s'), 'note' => $data['remark']];
            if ($order['payment_type'] == 5 && $data['return_pay_type'] == 4) {

                $pay_order->status = 1;
                $pay_order->return_amount = 0;
                $pay_order->return_pay_type = 4;
                $pay_order->remark = $remark;

                $order->order_status = 11;
                if ($order->save() && $pay_order->save()) {
//                    $success = Order::skuReturn($order->order_sn, $order->channel, $order['orderAfterSale']['return_type']);
//                    if (!$success) {
//                        DB::rollBack();
//                        return false;
//                    }
                    Order::orderLog($order->id, 11, "售后完成");
                    DB::commit();
                    return true;
                }
            }
            if ($order['payment_type'] == 5 && $data['return_pay_type'] > 1 && $data['return_pay_type'] < 4) {
                $pay_order->status = 1;
                $pay_order->return_amount = 0;
                $pay_order->return_pay_type = 4;
                $pay_order->remark = $remark;
                $pay_order->return_pay_at = date('Y-m-d H:i:s');
                $order->order_status = 7;
                $order->return_pay_at = date('Y-m-d H:i:s');
                if ($order['orderAfterSale']['return_type'] == 2) {
                    $order->order_state = 25;
                }
                if ($order->save() && $pay_order->save()) {
//                    $success = Order::skuReturn($order->order_sn, $order->channel, $order['orderAfterSale']['return_type']);
//                    if (!$success) {
//                        DB::rollBack();
//                        return false;
//                    }
                    Order::orderLog($order->id, 8, "退款完成");
//                    $sms = new Sms();
//                    $sub = new SubscribeShipped;
//                    $sms->send($order['mobile'], 11, $order['contact'], $order['order_sn']);
//                    $sub->refundMessage($order->id);
                    DB::commit();
                    return true;
                } else {
                    DB::rollBack();
                    return false;
                }
            }
            $params = [
                'refund_fee' => bcadd($order['orderAfterSale']['return_amount'], 0, 2),
                'total_fee' => bcadd($order['total_amount'], 0, 2),
                'order_sn' => $order['pay_order_sn'],
                'order_id' => $order['id'],
                'openid' => $order['open_id'],
                'type' => $order['payment_type'],
                'trade_type' => $order['trade_type'],
                'ori_tran_date' => date('Ymd', strtotime($order['transaction_date']))
            ];
            $success = self::doRefund($params);
            if (!$success) {
                return false;
            }
            if ($order['payment_type'] != 3) {
                $order->order_status = 7;
                $pay_order->return_pay_at = date('Y-m-d H:i:s');
                $order->return_pay_at = date('Y-m-d H:i:s');
            } else {
                $order->order_state = 11;
            }
            $order->remark = $order['remark'];
            if ($order['orderAfterSale']['return_type'] == 2) {
                $order->order_state = 25;
            }

            if ($order->save()) {
//                $success = Order::skuReturn($order->order_sn, $order->channel, $order['orderAfterSale']['return_type']);
//                if (!$success) {
//                    DB::rollBack();
//                    return false;
//                }
                $pay_order->save();
                DB::commit();
                if ($order['payment_type'] == 3) {
                    Order::orderLog($order->id, 5, "银联退款提交成功，等待退款回调");
                } else {
//                    $sms = new Sms();
//                    $sub = new SubscribeShipped;
//                    $sms->send($order['mobile'], 11, $order['contact'], $order['order_sn']);
//                    $sub->refundMessage($order->id);
                    Order::orderLog($order->id, 8, "退款完成");
                }

                return true;
            }
            return false;
        }

    }

    public static function doRefund($from_params)
    {
//        '  \'api/pay/refund\'=>ORDER.\'api/pay/refund\',//退款';
//        $url = config('api.map')['api/pay/refund'];
        $url = env('PAY_RETURN_URL') . '/order/api/pay/refund';
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params, ['verify' => false,
            'headers' => [
                'dlc-inner-invoke-from' => 'order'
            ],
        ]);
        Log::info('returnMoney:request', $http_params);
        Log::info('returnMoney:reponse:' . $content);
        $result = json_decode($content, true);

        if (!$result || !is_array($result)) {
            return false;
        }
        return $result['code'];
    }

    /**
     * 银联退款回调
     * @param $pay_order_sn
     */
    public static function paySuccess($pay_order_sn)
    {

        $order = Order::where('pay_order_sn', $pay_order_sn)->first();
        DB::beginTransaction();
        if ($order['order_status'] == 5 && $order['payment_type'] == 3) {
            $order->order_status = 7;
            $order->return_pay_at = date('Y-m-d H:i:s');
            AfterOrderSale::where('order_main_id', $order['id'])->update(
                [
                    'status' => 2,
                    'return_pay_at' => date('Y-m-d H:i:s')
                ]
            );
            if ($order->save()) {
                $sms = new Sms();
                $sms->send($order['mobile'], 11, $order['contact'], $order['order_sn']);
                Order::orderLog($order->id, 8, "银联退款完成");
                DB::commit();
                return true;
            }

            DB::rollBack();
            return false;

        }
        if ($order['order_status'] == 7) {
            return true;
        }
        return false;
    }


}
