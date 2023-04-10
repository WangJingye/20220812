<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\{DetailRequest,CancelRequest,SaveRefundInfoRequest};
use App\Repositories\{CheckoutRepository,
    Product\InnerProductRepository,
    OrderRepository};
use App\Exceptions\ApiPlaintextException;

class OrderController extends ApiController
{
    /**
     * @var \App\Services\Api\UserServices
     */
    public $userServices;
    public function __construct(){
        $this->userServices = app('UserServices');
    }

    /**
     * 获取订单列表
     * @param Request $request
     * @return array
     * @throws ApiPlaintextException
     */
    public function orderList(Request $request){
        $uid = $this->getUid();
        $status = $request->get('status');
        $size = $request->get('size',10);

        $list = [];
        $items_list = OrderRepository::getList($uid,$status,$size);
        $items = $items_list->items();
        $skus = array_column($items,'sku');
        $total = $items_list->total();
        //根据sku获取商品特殊属性
        $items_attr = collect((new InnerProductRepository())->getSkus($skus))->keyBy('sku');
        if($total){
            $order_status = collect(OrderRepository::$OrderStatus)->pluck('name','value')->toArray();
            $payment_method = OrderRepository::$PaymentMethod;
            foreach($items_list as $item){
                $item_attr = $items_attr->get($item->sku);
                if(!array_key_exists($item->order_id,$list)){
                    $list[$item->order_id] = [
                        'status'=>array_get($order_status,$item->order_status,'未知状态'),
                        'id'=>$item->order_id,
                        'time'=>$item->order_time,
                        'payWay'=>array_get($payment_method,$item->order_payment_method),
                        'amount'=>sprintf("%.2f",$item->order_grand_total),
                    ];
                }
                $list[$item->order_id]['goods'][] = [
                    'id'=>$item->sku,
                    'goodsId'=>$item_attr['parent_id'],
                    'name'=>$item->name,
                    'price'=>sprintf("%.2f",$item->price),
                    'cover'=>$item_attr['image'],
                    'color'=>[
                        'id'=>$item_attr['color'],
                        'name'=>$item_attr['color_value'],
                    ],
                    'gender'=>$item_attr['gender_value'],
                    'size'=>$item_attr['size_value'],
                    'quantity'=>intval($item->quantity),
                ];
            }
            $list = array_values($list);
        }
        $states = call_user_func(function(){
            $states = [];
            $order_status = collect(OrderRepository::$OrderStatus)->where('show',1);
            foreach($order_status as $value){
                $states[] = [
                    'text' => $value['name'],
                    'value' => $value['value'],
                ];
            }
            return $states;
        });
        return $this->success(compact('list','total','states'));
    }

    /**
     * @param DetailRequest $request
     * @return array
     * @throws \Exception
     */
    public function orderDetail(DetailRequest $request){
        $uid = $this->getUid();
        $order_id = $request->get('id');
        //获取订单信息
        $order = OrderRepository::getDetail($order_id,$uid);
        $order_status = $order_status = collect(OrderRepository::$OrderStatus)->pluck('name','value')->toArray();
        $payment_method = OrderRepository::$PaymentMethod;
        $result = [
            'amount'=>sprintf("%.2f",$order->grand_total),
            'time'=>$order->created_at,
            'payWay'=>array_get($payment_method,$order->payment_method),
            'shipping'=>sprintf("%.2f",$order->shipping_and_handling),
            'subTotal'=>sprintf("%.2f",$order->subtotal),
            'status'=>array_get($order_status,$order->status,'未知状态'),
        ];
        //获取订单商品
        $items = OrderRepository::getItems($order->entity_id);
        $skus = $items->map(function ($item) {
            return $item->sku;
        });
        //根据sku获取商品特殊属性
        $items_attr = collect((new InnerProductRepository())->getSkus($skus))->keyBy('sku');
        foreach($items as $item){
            $item_attr = $items_attr->get($item->sku);
            $result['goods'][] = [
                'id'=>$item->sku,
                'goodsId'=>$item_attr['parent_id'],
                'name'=>$item->name,
                'price'=>sprintf("%.2f",$item->price),
                'cover'=>$item_attr['image'],
                'color'=>[
                    'id'=>$item_attr['color'],
                    'name'=>$item_attr['color_value'],
                ],
                'size'=>$item_attr['size_value'],
                'quantity'=>intval($item->qty_ordered),
            ];
        }
        //获取订单地址
        $addresses = OrderRepository::getAddress($order->entity_id);
        foreach($addresses as $address){
            $result['address'][] = [
                'id'=>$address->entity_id,
                'username'=>$address->firstname,
                'mobile'=>$address->telephone,
                'province'=>$address->region,
                'city'=>$address->city,
                'county'=>$address->area,
                'detail'=>$address->street,
                'postcode'=>$address->postcode,
            ];
        }
        //有发票则显示发票
        $invoice = OrderRepository::getInvoice($order_id);
        if($invoice){
            $result['invoice'] = [
                'title'=>$invoice->title,
                'name'=>$invoice->name,
                'code'=>$invoice->code,
                'file'=>in_array($order->status,[
                    'WAIT_BUYER_CONFIRM_GOODS','TRADE_BUYER_SIGNED','TRADE_FINISHED'
                ])?$invoice->file:'',
            ];
        }
        //获取退货物流信息
        $refundInfo['status'] = 0;
        //如果订单为退款中 则尝试获取退货物流信息
        if($order->status == 'REFUNDING'){
            $returnsLogistics = OrderRepository::getReturnsLogistics($order_id);
            if($returnsLogistics){
                //获取到退货物流信息 则返回信息并且标记状态为2
                $refundInfo = [
                    'status' => 2,
                    'company' => $returnsLogistics->company_name,
                    'sid' => $returnsLogistics->sid,
                ];
            }else{
                //未获取到退货物流信息标记状态为1
                $refundInfo['status'] = 1;
            }
        }
        $result['refundInfo'] = $refundInfo;
        return $this->success($result);
    }

    /**
     * 取消订单
     * @param CancelRequest $request
     * @return array
     * @throws \Exception
     */
    public function cancelOrder(CancelRequest $request){
        $uid = $this->getUid();
        $order_id = $request->get('orderId');
        //根据用户和订单号获取订单entity_id
        $order = OrderRepository::getDetail($order_id,$uid);
        $entity_id = object_get($order,'entity_id');
        if(empty($entity_id)){
            throw new ApiPlaintextException('订单不存在');
        }
        $status = object_get($order,'status');
        if($status != 'WAIT_BUYER_PAY'){
            throw new ApiPlaintextException('订单不可取消');
        }
        //获取订单号随机数
        $serialsNumber = CheckoutRepository::getSerialsNumberFromDB($order_id);
        $client_sn = "{$order_id}-{$serialsNumber}";
        /** @var \App\Services\Api\CheckoutServices $checkoutServices */
        $checkoutServices = app('CheckoutServices');
        //查询支付状态
        $upay_order = $checkoutServices->queryPay($client_sn);
        //如果该笔订单已支付成功则不可取消
        if(is_object($upay_order) && object_get($upay_order,'biz_response.data.order_status') == 'PAID'){
            throw new ApiPlaintextException('订单已支付,无法取消');
        }
        //调用magento接口取消订单
        $magentoApi = app('ApiRequestMagento');
        $resp = $magentoApi->exec(['url'=>"V1/connext/orders/{$entity_id}/cancel",'method'=>'POST']);
        $resp = json_decode($resp);
        if($resp->code == 0){
            throw new ApiPlaintextException($resp->message);
        }
        //取消成功返回前端参数
        $order_status = collect(OrderRepository::$OrderStatus)->pluck('name','value')->toArray();
        //订单取消成功dispatch
        $this->dispatch(new \App\Jobs\CancelOrderAfter($entity_id));
        return $this->success([
            'status' => array_get($order_status,object_get($resp,'order.status'),'未知状态'),
        ]);
    }

    /**
     * 退货物流信息
     * @param SaveRefundInfoRequest $request
     * @return array
     * @throws ApiPlaintextException
     */
    public function saveRefundInfo(SaveRefundInfoRequest $request){
        $uid = $this->getUid();
        $order_id = $request->get('id');
        $company = $request->get('company');
        $sid = $request->get('sid');
        $data = [
            'tid'=>$order_id,
            'uid'=>$uid,
            'company_name'=>$company,
            'sid'=>$sid,
        ];
        OrderRepository::setReturnsLogistics($data);
        return $this->success();
    }

}
