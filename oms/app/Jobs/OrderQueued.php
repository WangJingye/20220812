<?php namespace App\Jobs;

use App\Model\OrderItem;
use App\Model\Sku;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;
use App\Model\Order;
use App\Services\DLCOms\Request as Oms;
use App\Model\SubscribeShipped;
use App\Services\Dlc\SalesServices;
use App\Services\Dlc\OmsSync;

class OrderQueued implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $method;
    protected $params;

    /**
     * Create a new job instance.
     * @param $method
     * @param $params
     * @param $delay
     */
    public function __construct($method,$params,$delay = null){
        $this->method = $method;
        $this->params = $params;
        if($delay){
            $this->delay($delay);
        }
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(){
        $res = call_user_func([$this,$this->method],$this->params);
        echo $res;
        Log::info('OrderQueued',[
            'method'=>$this->method,
            'params'=>$this->params,
            'res'=>$res,
        ]);
    }

    /**
     * 失败队列处理(已达到最大尝试次数)
     */
    public function failed(){
        //
    }

    public function pendingRemind($params){
        $result = call_user_func(function() use($params){
            $orderId = $params['orderId'];
            $order = Order::query()->find($orderId);
            if($order){
                if(!in_array($order->channel,[0,4]) && $order->payment_type!=5){
                    if($order->order_status==1){
                        (new SubscribeShipped)->pendingMessage($orderId);
                        return true;
                    }return '订单已支付';
                }return '订单支付方式或渠道不正确';
            }return '无此订单';
        });
        return $result===true?'执行成功':$result;
    }

    /**
     * @param $params
     * @return bool|string
     * @throws \Exception
     */
    public function syncToOms($params){
        $orderId = $params['orderId'];
        $result = OmsSync::run($orderId);
        return $result===true?'执行成功':$result;
    }

    /**
     * 累加spu销量
     * @param $params
     * @return mixed|string
     */
    public function salesVolume($params){
        $result = call_user_func(function() use($params){
            $orderId = $params['orderId'];
            $order = Order::query()->where('id',$orderId)->with(['orderDataItem'])->first()->toArray();
            if($order){
                $params = [];
                foreach ($order['order_data_item'] as $item) {
                    $params[$item['sku']] = $item['qty'];
                }
                app('ApiRequestInner')->request('addSalesVolume','POST',[
                    'params'=>$params
                ]);
                return true;
            }return '无此订单';
        });
        return $result===true?'执行成功':$result;
    }

    /**
     * 到货提醒
     * @param $params
     * @return mixed|string
     */
    public function arrivalReminder($params){
        $result = call_user_func(function() use($params){
            $skus = $params['skus'];
            $date = date('Y年m月d日');
            $subscribe = new SubscribeShipped;
            $resp = app('ApiRequestInner')->request('outward/product/getProductInfoBySkuIds','POST',[
                'sku_ids'=>implode(',',$skus),
                'type'=>'mini',
                'from'=>1,
            ]);
            if($resp['code']!=1){
                throw new \Exception('获取商品错误');
            }
            $products = $resp['data'];
            $goods = [];
            foreach($products as $sku => $product){
                if($product['stock']>0){
                    $goods[$sku] = [
                        'spu'=>$product['id'],
                        'name'=>$product['product_name'],
                        'spec'=>$product['sku']['spec_desc'],
                        'amount'=>$product['ori_price'],
                        'date'=>$date,
                    ];
                }
            }
            foreach($skus as $sku){
                $reminders = SalesServices::getArrivalReminders($sku);
                if($reminders && count($reminders)){
                    foreach($reminders as $open_id=>$data){
                        $data = json_decode($data,true);
                        $state = array_get($data,'state');
                        $template_id = array_get($data,'template_id');
                        if(array_key_exists($sku,$goods)){
                            $subscribe->arrivalMessage($goods[$sku],$open_id,$template_id,$state);
                        }SalesServices::hdelArrivalReminder($sku,$open_id);
                    }
                }
            }return true;
        });
        return $result===true?'执行成功':$result;
    }
}
