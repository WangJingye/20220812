<?php namespace App\Services\Dlc;

use App\Services\DLCOms\Request as Oms;
use App\Model\{Order,OrderItem,Sku};

class OmsSync
{
    /**
     * @param $orderId
     * @return bool|string
     * @throws \Exception
     */
    public static function run($orderId){
        //hardcode
        $virtual_prod_prefix = 'VIRTUAL_';
        $unreal_prod_prefix = 'UNREAL_';
        $order = Order::query()
            ->with('orderDataItem')
            ->with('orderInvoice')
            ->find($orderId);
        if($order->order_status == 3 && $order->is_sync==0){
            //先推送给OMS
//            $oms = new Oms;
//            $data = [
//                'order_bn'=>$order->order_sn,
//                'shop_id'=>config('dlc.dlc_oms_shop_id'),
//                'createtime'=>strtotime($order->created_at),
//                'paytime'=>strtotime($order->payment_at),
//                'pay_bn'=>\App\Http\Controllers\Api\Pos\salesExcelController::$payment_list[$order->payment_type],
//                'pmt_order'=>$order->total_discount?:0,
//                'pay'=>$order->total_amount,
//                'trade_no'=>$order->payment_id,
//                'cost_shipping'=>$order->total_ship_fee,
//                'address_id'=>"{$order->province}-{$order->city}-{$order->district}",
//                'order_refer_source'=>'minishop',
//                'is_pay_trial'=>($order->order_type==2)?1:0, //是否付邮试用订单
//                'is_card'=>false,
//                'is_letter'=>false,
//                'is_w_card'=>false,
//                'is_wechat'=>false,
//                'wechat_openid'=>$order->open_id,
//                'is_tax'=>$order->orderInvoice?'true':'false',
//            ];
//            //商品信息
//            $products = [];
//            //原始订单价格总和 用于付邮试用
//            $ori_total_item_price = 0;
//            foreach($order->orderDataItem as $item){
//                $sku = $item->sku;
//                if((strpos($sku,$virtual_prod_prefix) === 0)){
//                    //需要去掉虚拟商品的前缀
//                    $sku = substr($sku,strlen($virtual_prod_prefix));
//                }
//                if((strpos($sku,$unreal_prod_prefix) === 0)){
//                    //如果是非真实SKU则不同步给OMS
//                    continue;
//                }
//
//                if($item->main_sku && (strpos($item->main_sku,$unreal_prod_prefix) === 0)){
//                    $sale_price = $item->product_amount_total;
//                    $pmt_price = 0.0000;
//                }else{
//                    $sale_price = $item->order_amount_total;
//                    $pmt_price = $item->product_amount_total-$item->order_amount_total;
//                }
//                $_item = [
//                    'bn'=>$sku,
//                    'num'=>$item->qty,
//                    'name'=>$item->name,
//                    'price'=>$item->original_price,//商品单价
//                    'sale_price'=>$sale_price,//商品成交金额
//                    'pmt_price'=>$pmt_price,
//                ];
//
//
//                if($order->order_type==2){
//                    //如果是付邮试用则价格标记未0
//                    $_item['sale_price'] = 0;
//                }
//                $products[] = $_item;
//                $ori_total_item_price = bcadd($ori_total_item_price,$item->order_amount_total);
//            }
//            if($order->order_type==2){
//                $data['pmt_order'] = $ori_total_item_price;
//            }
//            $data['products'] = $products;
//            //收货人信息
//            $data['consignee'] = [
//                'addr'=>"{$order->province}{$order->city}{$order->district}{$order->address}",//收货地址
//                'name'=>$order->contact,
//                'zip'=>$order->zip_code,//邮编
//                'mobile'=>$order->mobile,
//            ];
//            //发票信息
//            if($order->orderInvoice){
//                $data['invoice'] = [
//                    'tax_title'=>$order->orderInvoice->title,//发票抬头
//                    'taxpayer_identity_number'=>$order->orderInvoice->number,//纳税人识别号
//                    'invoice_contact'=>$order->orderInvoice->mobile,
//                    'is_einvoice'=>'true',//是否是电子发票
//                ];
//            }
            //会员信息 全渠道会员才需要
//                if($order->pos_id){
//                    $resp = app('ApiRequestInner')->request('user/getByOpenId','GET',[
//                        'open_id'=>$order->open_id
//                    ]);
//                    $user = $resp['data'];
//                    $data['account'] = [
//                        'name'=>array_get($user,'name'),
//                        'mobile'=>array_get($user,'phone'),
//                        'member_code'=>array_get($user,'pos_id'),
//                        'openid'=>array_get($user,'open_id'),
//                        'unionid'=>array_get($user,'union_id'),
//                    ];
//                }
            try{
//                $orderAddResp = $oms->orderAdd($data);
                if(1){
                    //推送成功后解锁库存
                    $order_skus = OrderItem::where('order_sn', $order->order_sn)->select('sku', 'qty')->get()->toArray();
                    $stock_sku = [];
                    foreach ($order_skus as $v) {
                        $stock_sku[] = array_values($v);
                    }
                    $sku_model = new Sku;
                    $deal_sku = $sku_model->batchUnlockSku(json_encode($stock_sku), $order->channel, 1);
                    if ($deal_sku['code'] != 1) {
                        return "库存解锁失败";
                    }
                    $order->update([
                        'is_sync'=>1,
                        'is_exception'=>0,
                        'exception_msg'=>'',
                        'sync_time'=>date('Y-m-d H:i:s')
                    ]);
                    return true;
                }
//                else{
//                    throw new \Exception($orderAddResp);
//                }
            }catch (\Exception $e){
                //推送异常则标记未异常订单
                $order->update(['is_exception'=>1,'exception_msg'=>$e->getMessage()]);
                throw new \Exception($e->getMessage());
            }
        }return "订单状态不正确或已经同步";
    }



















}