<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Order;
use App\Lib\GuzzleHttp;
use App\Support\Sms;
use App\Model\SubscribeShipped;
use Illuminate\Support\Facades\Log;

class OvertimeOrder extends Command
{
    protected $signature = 'run:overtimeOrder';

    protected $description = '订单过期取消';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $order = new Order;
//            $sub = new SubscribeShipped;
            //更新失效订单
            $over_date = date("Y-m-d H:i:s", bcsub(time(),config('wms.oms_cancel_time')));

            $data = $order::select('id', 'order_sn', 'pay_order_sn', 'pay_order_sn', 'channel', 'payment_type', 'mobile', 'contact', 'coupon_id', 'user_id', 'transaction_date','order_type')->where('order_status', 1)->where('channel', '!=', 0)->where('channel', '!=', 4)->where('payment_type', '!=', 5)->where('created_at', '<', $over_date)->limit(100)->get()->toArray();

            foreach ($data as $v) {
                $status = $this->closePayOrder($v);
                if ($status == 1) {
                    Order::cancleOrder($v['order_sn'], $v['id'], $v['payment_type'], $v['channel'], 2, $v['mobile'], $v['contact'], $v['user_id'], $v['coupon_id'],$v['order_type']);
                }
            }

//            $over_start = config('wms.oms_over_time');
//            $over_end = $over_start+1;
//            $over_start = date("Y-m-d H:i:s", strtotime("-{$over_start} minutes"));
//            $over_end = date("Y-m-d H:i:s", strtotime("-{$over_end} minutes"));
//
//            $data = $order::select('id', 'order_sn', 'pay_order_sn', 'pay_order_sn', 'channel', 'payment_type', 'mobile', 'contact', 'coupon_id', 'user_id', 'transaction_date','created_at')->where('channel', '!=', 0)->where('channel', '!=', 4)->where('payment_type', '!=', 5)->where('order_status', 1)->where('created_at', '>', $over_end)->where('created_at', '<=', $over_start)->limit(100)->get()->toArray();
//
//            foreach ($data as $v) {
//                Log::info('notice_order_pay',['order_sn'=>$v['order_sn'],'created_at'=>$v['created_at']]);
//                $sub->pendingMessage($v['id']);
//            }

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    //关闭超时未支付订单的支付信息
    public function closePayOrder($data)
    {

        $from_params = [
            'order_sn' => $data['pay_order_sn'],
            'type' => $data['payment_type'],
            'trade_type' => $data['payment_type'],
            'ori_tran_date' => date('Ymd', strtotime($data['transaction_date'])),
        ];
        $url = config('api.map')['api/pay/closeOrder'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('closePayOrder:request', $http_params);
        Log::info('closePayOrder:reponse:' . $content);
        $result = json_decode($content, true);
        if (!$result || !is_array($result)) {
            return 0;
        }
        return $result['code'];


    }

}