<?php namespace App\Http\Controllers\Api;

use App\Model\GoldOrder;
use App\Services\Order\GoldOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GoldOrderController extends ApiController
{
    /**
     * @var \App\Services\Order\GoldOrderService
     */
    public $goldOrderService;

    public function __construct()
    {
        $this->goldOrderService = new GoldOrderService();
    }


    /**
     * 购买储值卡
     */
    public function pay(Request $request)
    {
        try {
            $params = $request->all();
            return $this->success($this->goldOrderService->pay($params));
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    /**
     * 购买储值卡回调
     */
    public function notify(Request $request)
    {
        ////获取返回的xml
        $xml = file_get_contents("php://input");
        Log::info('WxNotify支付回调开始xml=' . $xml);
        //将xml转化为json格式
        $jsonxml = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        //转成数组
        $result = json_decode($jsonxml, true);
        if ($result['result_code'] != 'SUCCESS' || $result['return_code'] != 'SUCCESS') {
            return $this->goldOrderService->wxError('false');
        }
        $result = $this->goldOrderService->notify($result);
        if ($result == 'success') {
            return $this->goldOrderService->wxSuccess();
        } else {
            return $this->goldOrderService->wxError($result);
        }
    }

    /**
     * 退款
     */
    public function refund(Request $request)
    {
        try {
            $params = $request->all();
            $this->goldOrderService->refund($params);
            return $this->success([], '已退款');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function getGoldOrderInfo(Request $request)
    {
        try {
            $params = $request->all();
            $order = GoldOrder::query()->where('order_sn', $params['order_sn'])->first();
            return $this->success($order, 'success');
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

}
