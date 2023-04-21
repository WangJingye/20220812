<?php

namespace App\Services\Order;

use App\Model\GoldOrder;
use App\Services\Pay\AliPayService;
use App\Services\Pay\ChinaPayService;
use App\Services\Pay\UnionPayService;
use App\Services\Pay\WxPayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Swoole\Exception;

class GoldOrderService
{
    public $WxPayService;
    public $AliPayService;
    public $UnionPayService;
    public $ChinaPayService;
    public $pendingOrderKey;

    public function __construct()
    {
        // //付款方式 1微信支付js api、2web支付宝支付 Alipay 、3花呗支付Huabai、4银联支付UnionPay、5货到付款Codpay 6miniapp微信小程序支付， 7native网页 微信扫码 ，8 h5 mweb微信 9pc 支付宝
        $this->WxPayService = new WxPayService();
        $this->AliPayService = new  AliPayService();
        $this->UnionPayService = new UnionPayService();
        $this->ChinaPayService = new ChinaPayService();
    }

    public function pay($data)
    {
        $goldInfo = $this->sendRequest('getGoldInfo', 'goods', ['id' => $data['id']]);

        if ($goldInfo['status'] != 1) {
            throw new \Exception('当前记录有误');
        }
        $userInfo = $this->sendRequest('getUserInfoByOpenid', 'member', ['openid' => $data['openid']]);
        //redis记录预付信息
        $orderSn = $this->createOrderNo();
        $redis = Redis::connection();
        $redis->set('bgo:' . $orderSn, $userInfo['id'] . ',' . $goldInfo['id']);
        $redis->expire('bgo:' . $orderSn, 8000);
        $payType = $this->getPayType($data['payment_method'], $data['trade_type']);

        $params = [
            'title' => config('pay.GOLD_NAME'),//支付显示标题
            'total_amount' => $goldInfo['price'], //订单金额  分为单位
            'order_sn' => $orderSn,//订单号
            'order_id' => $orderSn,//订单号
            'openid' => $data['openid'], //小程序支付 必传
            'desc' => '储值卡充值',//描述
            'trade_type' => $payType['trade_type'] ?? '',//交易类型  JSAPI--JSAPI支付   、minApp 小程序支付、NATIVE --Native支付、APP--app支付，MWEB--H5支付，支付宝   TradeWapPay 手机网站支付   -HuabeiPay  花呗    TradePcPay pc支付
            'type' => $payType['type'],//支付方式Ali,Union,Wx,China
            'hb_fq_num' => $data['hb_fp_num'] ?? 0,
            'redis_time' => time(),
            'notify_url' => env('NOTIFY_DOMAIN') . '/order/api/goldOrder/notify',
        ];
        Log::info('pay 支付参数 ', [$params]);
        $Services = $params['type'] . 'PayService';
        //微信再次发起支付 特殊处理
        $result = $this->$Services->Pay($params);
        Log::info('请求支付后返回参数', [$result]);
        if ($result['code'] == 0) {
            throw new \Exception($result['msg']);
        }
        return $result['data'];
    }

    public function notify($result)
    {
        try {
            //订单查询
            $params = [
                'order_sn' => $result['out_trade_no'],
                'trade_type' => $result['trade_type']
            ];

            $queryResult = $this->WxPayService->orderQuery($params);
            if ($queryResult['code'] == 0) {
                throw new Exception($queryResult['msg']);
            }

            $key = Redis::get('bgo:' . $result['out_trade_no']);
            if (empty($key)) {
                throw new Exception('out_trade_no不存在');
            }
            $arr = explode(',', $key);
            $goldInfo = $this->sendRequest('getGoldInfo', 'goods', ['id' => $arr[1]]);
            //判断金额,TODO
            if ($result['total_fee'] != $goldInfo['price'] * 100) {
                throw new Exception('金额不符，充值失败');
            }
            $userInfo = $this->sendRequest('getUserInfoByUserId', 'member', ['id' => $arr[0]]);
            $data = [
                'order_sn' => $result['out_trade_no'],
                'order_title' => $goldInfo['gold_name'],
                'user_id' => $arr[0],
                'gold_id' => $arr[1],
                'amount' => $goldInfo['price'],
                'pay_method' => 2,
                'pay_code' => $result['result_code'],
                'transaction_id' => $result['transaction_id'],
                'pay_time' => date('Y-m-d H:i:s'),
                'status' => 2,
                'gold_info' => json_encode($goldInfo),
                'mobile' => $userInfo['phone'],
                'pos_id' => $userInfo['pos_id'],
            ];
            DB::beginTransaction();
            $goldOrder = new GoldOrder($data);
            $goldOrder->save();
            $this->sendRequest('addBalance', 'member', [
                'order_sn' => $data['order_sn'],
                'order_title' => $goldInfo['gold_name'],
                'user_id' => $arr[0],
                'gold_id' => $arr[1],
                'pay_method' => $data['pay_method'],
                'pay_amount' => $result['total_fee'] / 100,
            ]);
            DB::commit();
            return 'success';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function getTradeTypeDB($params)
    {
        ////1微信支付js api、2web支付宝支付 Alipay 、3花呗支付Huabai、4银联支付UnionPay、5货到付款Codpay 6minapp微信小程序支付， 7native网页 微信扫码 ，8 h5 mweb微信 9pc 支付宝

        $trade_type = [
            '', 'jsapi', 'Alipay', 'Huabai', 'UnionPay', 'Codpay', 'minApp', 'NATIVE', 'MWEB', 'AlipayPC',
        ];
        if ($params) return $trade_type[$params];
        return $trade_type;
    }

    public function getTypeDB($params)
    {
        $type = [
            0 => '',
            1 => 'AliPay',
            2 => 'WeixinPay',
            3 => 'UnionPay',
            4 => 'HuabeiPay',
            10 => 'GoldPay',
            11 => 'MultiPay'
        ];
        if ($params) return $type[$params];
        return $type;
    }

    public function getBalanceInfo($id)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('getBalanceInfo', 'POST', ['id' => $id]);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('member接口user/getBalanceInfo错误');
        }
    }

    public function setBalanceInvoice($params)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('setBalanceInvoice', 'POST', $params);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('member接口user/setBalanceInvoice错误');
        }
    }

    public function addBalance($params)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('addBalance', 'POST', $params);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('member接口user/addBalance错误');
        }
    }

    public function refundBalanceCard($params)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('refundBalanceCard', 'POST', $params);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('member接口user/refundBalance错误');
        }
    }

    public function getGoldInfo($id)
    {

        $api = app('ApiRequestInner', ['module' => 'goods']);
        $info = $api->request('getGoldInfo', 'POST', ['id' => $id]);

        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('goods接口gold/detail错误');
        }
    }

    public function getUserInfoByOpenid($openid)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('getUserInfoByOpenid', 'POST', ['openid' => $openid]);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('goods接口gold/detail错误');
        }
    }

    public function getUserInfoByUserId($user_id)
    {
        $api = app('ApiRequestInner', ['module' => 'member']);
        $info = $api->request('getUserInfoByUserId', 'POST', ['id' => $user_id]);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception('goods接口gold/detail错误');
        }
    }

    public function createOrderNo($channel_id = 1, $is_test = 2)
    {
        return date('ymdHis') . $is_test . $channel_id . str_pad(rand(0, 999999), 6, STR_PAD_LEFT);
    }

    public function getPayType($type = '', $trade_type = '')
    {
        $result = [];
        //1AliPay,2 WeixinPay,3 UnionPay 4HuabeiPay 5 Offline
        //支付方式: AliPay, WeixinPay, UnionPay,HuabeiPay,Offline
        //微信支付必填（JSAPI--JSAPI支付 、minApp 小程序支付、NATIVE --Native支付、MWEB--H5支付）
        if ($type == 'AliPay' or $type == 'HuabeiPay' or $type == 1 or $type == 4) {
            $result['type'] = 'Ali';
            if ($trade_type == 'NATIVE') {
                $result['trade_type'] = 'TradePcPay';
            } elseif ($trade_type == 'MWEB') {
                $result['trade_type'] = 'TradeWapPay';
            }
        } elseif ($type == 'WeixinPay' or $type == 2) {
            $result['type'] = 'Wx';
            $result['trade_type'] = $trade_type;
        } elseif ($type == 'UnionPay' or $type == 3) {
            $result['type'] = 'China';
        } elseif ($type == 'MultiPay' or $type == 11) {
            $result['type'] = 'Wx';
            $result['trade_type'] = $trade_type;
        } elseif ($type == 10) {
            $result['type'] = 'Gold';
            $result['trade_type'] = $trade_type;
        }
        return $result;
    }

    public function refund($params)
    {
        $balanceInfo = $this->sendRequest('getBalanceInfo', 'member', ['id' => $params['id']]);
        $goldOrder = GoldOrder::query()->where('order_sn', $balanceInfo['order_sn'])->first();
        $order = json_decode(json_encode($goldOrder), true);
        //可退余额大于0
        $refundAmount = $params['refund_amount'];
        if ($refundAmount > 0) {
            $params = [
                'total_fee' => $order['amount'],//订单总金额
                'refund_fee' => $refundAmount,//退款金额
                'order_sn' => $order['order_sn'],
                'trade_type' => 'minApp',
                'type' => $order['pay_method'],
                'OriOrderNo' => $order['order_sn'],
                'OriTranDate' => $order['pay_time'],
            ];
            $payType = $this->getPayType($order['pay_method'], 'minApp');
            $Services = $payType['type'] . 'PayService';
            $queryResult = $this->$Services->orderQuery($params);
            Log::info('refund 储值卡查询结果', $queryResult);
            if (!isset($queryResult) || $queryResult['code'] == 0) {
                Log::info('付款成功异常储值卡记录order_sn=' . $params['order_sn']);
                throw new \Exception($queryResult['msg']);
            }
            Log::info('refund 储值卡退款参数=', [$params]);
            $result = $this->$Services->refund($params);
            Log::info('refund 储值卡退款返回结果', [$result]);
            if (!isset($result['code']) || $result['code'] == 0) {
                throw new \Exception($result['msg']);
            }
        } else {
            $refundAmount = 0;
        }
        $goldOrder->refund_amount = $refundAmount;
        $goldOrder->refund_time = date('Y-m-d H:i:s');
        $goldOrder->status = 3;
        $goldOrder->save();
        $this->sendRequest('refundBalanceCard', 'member', [
            'id' => $balanceInfo['id'],
            'order_sn' => $order['order_sn'],
            'order_title' => $order['order_title']
        ]);
    }

    public function applyRefund($params)
    {

        $balanceInfo = $this->sendRequest('getBalanceInfo', 'member', ['id' => $params['id']]);
        if ($balanceInfo['status'] == 0 || $balanceInfo['status'] == 3) {
            throw new \Exception('当前记录有误');
        }
        $data = [
            'order_sn' => $balanceInfo['order_sn'],
            'content' => $params['content'],
        ];
        $this->sendRequest('applyRefund', 'member', $data);
    }

    public function wxSuccess()
    {
        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    /**
     * 发送请求
     * @param $method
     * @param $module
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function sendRequest($method, $module, $data)
    {
        $api = app('ApiRequestInner', ['module' => $module]);
        $info = $api->request($method, 'POST', $data);
        if (isset($info['code']) && $info['code'] == 1) {
            return $info['data'];
        } else {
            throw new Exception($info['message'] ?? '');
        }
    }

    public function wxError($msg)
    {
        return '<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[' . $msg . ']]></return_msg></xml>';
    }
}
