<?php

namespace App\Services\Pay;

use App\Lib\Pay\WxPay\JsApiPay;
use App\Lib\Pay\WxPay\NativeNotifyCallBack;
use App\Lib\Pay\WxPay\WxData\WxPayCloseOrder;
use App\Lib\Pay\WxPay\WxData\WxPayOrderQuery;
use App\Lib\Pay\WxPay\WxData\WxPayRefund;
use App\Lib\Pay\WxPay\WxData\WxPayRefundQuery;
use App\Lib\Pay\WxPay\WxData\WxPayUnifiedOrder;
use App\Lib\Pay\WxPay\WxNativePay;
use App\Lib\Pay\WxPay\WxPayApi;
use App\Lib\Pay\WxPay\WxPayConfig;
use Illuminate\Support\Facades\Log;

class WxPayService
{

    public function __construct()
    {

    }

    public function Pay($params = [])
    {
        $params['total_amount'] = bcmul($params['total_amount'], 100, 0);
        if ($params['trade_type'] == 'NATIVE') {
            return $this->NATIVE($params);
        } elseif ($params['trade_type'] == 'MWEB') {
            return $this->MWEB($params);
        }
        return $this->PayInfo($params);
    }

    /**
     * [getparams 发起支付]
     * @Author   Peien
     * @DateTime 2020-03-20T13:21:20+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function PayInfo($params)
    {
        //根据传参不同请求不同的config    公众号支付 和小程序
        //$logHandler= new CLogFileHandler(storage_path()."/logs/wx/".$params['type'].'/'.date('Y-m-d').'.log');
        //$log = WxLog::Init($logHandler, 15);
        //①、获取用户openid
        $time = time();
        $startTime = date("YmdHis", $time);
        $endTime = date("YmdHis", $time + config('pay.TIME_EXPIRE'));
        try {
            $tools = new JsApiPay();

            $openId = $params['openid'];
            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($params['title']);
            //$input->SetAttach("test");
            $input->SetOut_trade_no($params['order_sn']);
            $input->SetTotal_fee($params['total_amount']);
            $input->SetTime_start($startTime);
            $input->SetTime_expire($endTime);
            //$input->SetGoods_tag("test");
            $input->SetTrade_type($this->getTradeType($params['trade_type'])); //TODO   获取type
//            $trade_type = 'jsapi';
            $input->SetOpenid($openId ?? '');
            if ($params['trade_type'] == 'minApp') {
                $trade_type = 'minApp';
                $input->SetNotify_url(env('NOTIFY_DOMAIN') . '/order/api/pay/minAppNotify?order_id=' . $params['order_id'] . '&type=Wx&trade_type=' . $params['trade_type']);
            }
            $input->SetNotify_url(env('NOTIFY_DOMAIN') . '/order/api/pay/notify?order_id=' . $params['order_id'] . '&type=Wx&trade_type=' . $params['trade_type']);
            if (!empty($params['notify_url'])) {
                $input->SetNotify_url($params['notify_url']);
            }
            $config = new WxPayConfig($trade_type);
            Log::info('微信支付信息参数=' . json_encode($input));
            $order = WxPayApi::unifiedOrder($config, $input);
            if (isset($order['result_code']) && $order['result_code'] == 'FAIL') {
                return ['code' => 0, 'msg' => $order['err_code_des'] ?? '订单发起支付失败'];
            }
            $jsApiParameters = $tools->GetJsApiParameters($order);
            return ['code' => 1, 'data' => json_decode($jsApiParameters, true)];
        } catch (Exception $e) {
            Log::ERROR(json_encode($e));
            return ['code' => 0, 'msg' => $e];
        }
    }

    public function NATIVE($params = [])
    {
        $notify = new WxNativePay();
        //$url1 = $notify->GetPrePayUrl("123456789");
        //模式二
        /**
         * 流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $input = new WxPayUnifiedOrder();
        $input->SetBody($params['title']);
        //$input->SetAttach("test");
        $input->SetOut_trade_no($params['order_sn']);
        $input->SetTotal_fee($params['total_amount']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + config('pay.TIME_EXPIRE')));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url(env('NOTIFY_DOMAIN') . '/order/api/pay/NativeNotify');
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id(time());
        $result = $notify->GetPayUrl($input);
        Log::Info("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            $qrCode = base64_encode(\QrCode::format('png')->size(150)->generate($result["code_url"]));
            return ['code' => 1, 'msg' => 'success', 'data' => "data:image/png;base64, {$qrCode}"];
        }
        return ['code' => 0, 'msg' => $result['return_msg']];
    }


    public function MWEB($params = [])
    {
        //根据传参不同请求不同的config    公众号支付 和小程序
        //$logHandler= new CLogFileHandler(storage_path()."/logs/wx/".$params['type'].'/'.date('Y-m-d').'.log');
        //$log = WxLog::Init($logHandler, 15);
        //①、获取用户openid
        $time = time();
        $startTime = date("YmdHis", $time);
        $endTime = date("YmdHis", $time + config('pay.TIME_EXPIRE'));
        $returnUrl = env('FRONT_URL') . '/payquery?sn=' . $params['order_sn'];
        $scene_info = '{"h5_info": {"type":"Wap","wap_url": "' . $returnUrl . '","wap_name": "' . config('pay.NAME') . '"}}';
        try {
            $tools = new JsApiPay();
            //②、统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody($params['title']);
            $input->SetOut_trade_no($params['order_sn']);
            $input->SetTotal_fee($params['total_amount']);
            $input->SetTime_start($startTime);
            $input->SetTime_expire($endTime);
            $input->getSceneInfo($scene_info);
            //$input->SetSpbill_create_ip($params['clientIp']);
            //$input->SetGoods_tag("test");
            $input->SetTrade_type('MWEB');
            //$input->SetOpenid($openId??'');
            $input->SetNotify_url(env('NOTIFY_DOMAIN') . '/order/api/pay/webNotify');
            $config = new WxPayConfig();
            Log::info('web微信支付信息参数=' . json_encode($input));
            $order = WxPayApi::unifiedOrder($config, $input);
            if (isset($order)) {
                if ($order['result_code'] == "SUCCESS" && $order['return_code'] == "SUCCESS") {
                    $order['mweb_url'] .= '&redirect_url=' . $returnUrl;
                    return ['code' => 1, 'data' => $order];
                }
            }
        } catch (Exception $e) {
            Log::ERROR(json_encode($e));
            return ['code' => 0, 'msg' => $e];
        }
    }

    /**
     * [query description]
     * @Author   Peien
     * @DateTime 2020-06-22T17:36:21+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function orderQuery($params)
    {
        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($params['order_sn']);
        $trade_type = 'jsapi';
        if ($params['trade_type'] == 'minApp') {
            $trade_type = 'minApp';
        }
        $config = new WxPayConfig($trade_type);
        $result = WxPayApi::orderQuery($config, $input);

        Log::info("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            if ($result['trade_state'] == 'SUCCESS') {
                $result['payTime'] = date('Y-m-d H:i:s');
                $result['trade_no'] = $result['transaction_id'];
                return ['code' => 1, 'msg' => 'ok', 'data' => $result];
            }
            return ['code' => 0, 'msg' => $result['trade_state_desc'] ?? ''];
        }
        return ['code' => 0, 'msg' => $result['return_msg'] ?? ''];
    }

    /**
     * [refund 退款]
     * @Author   Peien
     * @DateTime 2020-06-22T17:47:01+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function refund($params)
    {
        $this->refundQuery($params);
        $input = new WxPayRefund();
        $trade_type = 'jsapi';
        if ($params['trade_type'] == 'minApp') {
            $trade_type = 'minApp';
        }
        $out_trade_no = $params["order_sn"];
        $total_fee = bcmul($params["total_fee"], 100, 0);
        $refund_fee = bcmul($params["refund_fee"], 100, 0);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($out_trade_no . date('Ymdhis'));
        $config = new WxPayConfig($trade_type);
        $input->SetOp_user_id($config->GetMerchantId());
        if (!empty($params['notify_url'])) {
            $input->setNotify_url($params['notify_url']);
        }
        Log::info('微信退款方法入参:' . json_encode($params));
        Log::info('微信退款接口入参:' . json_encode([
                'out_trade_no' => $input->GetOut_trade_no(),
                'gettotal_fee' => $input->GetTotal_fee(),
                'getrefund_fee' => $input->GetRefund_fee(),
                'getout_refund_no' => $input->GetOut_refund_no(),
                'getop_user_id' => $input->GetOp_user_id(),
                'notify_url' => $params['notify_url'] ?? '',
            ]));
        $result = WxPayApi::refund($config, $input);
        Log::info('微信退款返回参数' . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            return ['code' => 1, 'msg' => 'ok', 'data' => $result];
        }

        return ['code' => 0, 'msg' => $result['err_code_des'] ?? ''];
    }


    /**
     * [refundQuery description]
     * @Author   Peien
     * @DateTime 2020-08-09T11:39:31+0800
     * @param    [type]                   $params [description]
     * @return   [type]                           [description]
     */
    public function refundQuery($params)
    {
        //查询订单信息
        //$orderQuery = $this->orderQuery($params);
        $input = new WxPayRefundQuery();
        $trade_type = 'jsapi';
        if ($params['trade_type'] == 'minApp') {
            $trade_type = 'minApp';
        }
        $out_refund_no = $params["order_sn"];
        $input = new WxPayRefundQuery();
        $input->SetOut_refund_no($out_refund_no);
        $config = new WxPayConfig($trade_type);
        Log::info(json_encode($input));
        $result = WxPayApi::refundQuery($config, $input);
        Log::info('微信退款返回参数' . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            return ['code' => 1, 'msg' => 'ok', 'data' => $result];
        }

        return ['code' => 0, 'msg' => $result['err_code_des'] ?? ''];


    }

    public function NativeNotify()
    {
        $config = new WxPayConfig();
        Log::DEBUG("begin notify!");
        $notify = new NativeNotifyCallBack();
        $notify->Handle($config, true);


    }

    public function getTradeType($param = '')
    {

        //'JSAPI--JSAPI支付（或小程序支付）、NATIVE--Native支付、APP--app支付，MWEB--H5支付';
        switch ($param) {
            case 'minApp':
                return 'JSAPI';
                break;
            default:
                # code...
                break;
        }
        return $param;
    }

    public function getCodeUrl($params = [])
    {
        $notify = new WxNativePay();
        //$url1 = $notify->GetPrePayUrl("123456789");
        //模式二
        /**
         * 流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $input = new WxPayUnifiedOrder();
        $input->SetBody($params['title']);
        //$input->SetAttach("test");
        $input->SetOut_trade_no($params['order_sn']);
        $input->SetTotal_fee($params['total_amount']);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + config('pay.TIME_EXPIRE')));
        //$input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/notify.php");
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id("123456789");
        $result = $notify->GetPayUrl($input);
        Log::DEBUG("query:" . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            $url2 = $result["code_url"];
            return ['code' => 1, 'msg' => 'success', 'data' => $url2];
        }
        return ['code' => 0, 'msg' => $result['return_msg']];
    }


    /**
     * [notify 支付回掉]
     * @Author   Peien
     * @DateTime 2020-07-28T15:33:20+0800
     * @return   [type]                   [description]
     */
    public function notify()
    {
        $config = new WxPayConfig();
        Log::DEBUG("begin notify");
        $notify = new PayNotifyCallBack();
        $notify->Handle($config, false);
    }


    public function getOpenid($href)
    {
        $tools = new JsApiPay();
        $url = $tools->GetOpenid($href);
        return $url;
    }

    public function closeOrder($params = [])
    {
        $input = new WxPayCloseOrder();
        $trade_type = 'jsapi';
        if ($params['trade_type'] == 'minApp') {
            $trade_type = 'minApp';
        }
        $out_refund_no = $params["order_sn"];
        $input->SetOut_trade_no($out_refund_no);
        $config = new WxPayConfig($trade_type);
        $result = WxPayApi::closeOrder($config, $input);
        Log::info('微信关单返回参数' . json_encode($result));
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS") {
            return ['code' => 1, 'msg' => 'ok', 'data' => $result];
        }

        return ['code' => 0, 'msg' => $result['err_code_des'] ?? ''];
    }


}
