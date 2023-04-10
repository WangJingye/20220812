<?php namespace App\Services\Api;

use App\Exceptions\ApiPlaintextException;
use App\Repositories\{CheckoutRepository, Product\InnerProductRepository};

class CheckoutServices
{
    /**
     * 获取支付方式
     * @param null $key
     * @return \App\Services\Upay\PaymentMethod\PaymentMethod
     * @throws \Exception
     */
    public function getPaymentMethod($key = null){
        $paymentMethod = [
            //PC端支付宝(二维码)
            1=>'PcAliPay',
            //PC端微信(二维码)
            2=>'PcWxPay',
            //H5支付宝(跳转)
            3=>'HAliPay',
            //H5微信(跳转)
            4=>'HWxPay',
            //微信公众号
            5=>'WapPay',
        ];
        $paymentMethodClassName = array_get($paymentMethod,intval($key));
        $className = "App\\Services\\UPay\\PaymentMethod\\".$paymentMethodClassName;
        if(class_exists($className)){
            return new $className();
        }throw new \Exception('错误的支付方式');
    }

    /**
     * 获取配送方式
     * @param null $key
     * @return array
     */
    public function getShippingMethod($key = null){
        $result = [
            1=>'freeshipping_freeshipping',
            2=>'flatrate_flatrate',
        ];
        if($key){
            $result = array_get($result,$key);
        }
        return $result;
    }

    /**
     * 转成二维码图片数据流
     * @param $str
     * @return string
     */
    public function getQrcode($str){
        $qrCode = base64_encode(\QrCode::format('png')->size(150)->generate($str));
        return "data:image/png;base64, {$qrCode}";
    }

    /**
     * 支付下单
     * @param $client_sn
     * @param $total_amount
     * @param \App\Services\Upay\PaymentMethod\PaymentMethod $paymentMethod
     * @param $order_id
     * @return bool|string
     * @throws \Exception
     */
    public function orderPay(
        $client_sn,
        $total_amount,
        \App\Services\Upay\PaymentMethod\PaymentMethod $paymentMethod,
        $order_id
    ){
        //调用UPay支付
        /** @var \App\Services\UPay\UPay $upay */
        $upay = app('Upay');
        $data = [
            'client_sn'=>$client_sn,
            'total_amount'=>$total_amount,
            'notify_url'=>env_url('upay/notify'),
            'payway_title'=>$paymentMethod->magentoMethod,
        ];
        if($paymentMethod->payWay){
            //payway:1.支付宝,3.微信
            //sub_payway:2.二维码支付,3.wap支付,4.小程序支付,5.APP支付,6.H5支付
            $data['payway'] = $paymentMethod->payWay;
            $data['sub_payway'] = $paymentMethod->subPayWay;
            $resp = $upay->precreate($data);
            if($paymentMethod->subPayWay=='2'){
                //返回二维码
                $resp = $this->getQrcode(object_get($resp,'biz_response.data.qr_code'));
            }
        }else{
            $data['return_url'] = web_url('myaccount/orderdetail')."?id={$order_id}";
            //公众号支付接口调用
            $resp = $upay->wap_api_pro($data);
        }
        return $resp ?? false;
    }

    /**
     * 查询收钱吧支付状态
     * @param $client_sn
     * @return bool|string
     * @throws \Exception
     */
    public function queryPay($client_sn){
        //调用UPay支付
        /** @var \App\Services\UPay\UPay $upay */
        $upay = app('Upay');
        return $upay->query($client_sn);
    }

    /**
     * 购物车信息构建
     * @param $items
     * @param int $type 1:info 对象, 0:list 数组
     * @return array
     */
    public function makeUpCart($items,$type = 0){
        $skus = array_keys($items);
        $subtotal = 0;
        $result = [
            'shipping' => '0.00',
            'subTotal' => &$subtotal,
            'amount' => &$subtotal,
        ];
        $list = [];
        if($skus){
            //获取商品属性
            $items_attr = collect((new InnerProductRepository())->getSkus($skus))->keyBy('sku');
            foreach($skus as $sku){
                $item_attr = $items_attr->get($sku);
                if(empty($item_attr))continue;
                $qty = intval(array_get($items,$item_attr['sku']));
                $row_total = $item_attr['price']*$qty;
                $subtotal+=$row_total;
                $stock = intval($item_attr['stock']);
                $line = [
                    'id' => $item_attr['sku'],
                    'goodsId' => $item_attr['parent_id'],
                    'name' => $item_attr['name'],
                    'price' => sprintf("%.2f",$item_attr['price']),
                    'images' => $item_attr['kv'],
                    'color'=>[
                        'id'=>$item_attr['color'],
                        'name'=>$item_attr['color_value'],
                        'value'=>$item_attr['color_value'],
                    ],
                    'size'=>[
                        'id'=>$item_attr['sku'],
                        'name'=>$item_attr['size_value'],
                        'stock'=>$stock,
                    ],
                    'quantity' => $qty,
                    'total' => sprintf("%.2f",$row_total),
                ];
                if($stock){
                    //有库存 添加到头部
                    array_unshift($list,$line);
                }else{
                    //无库存 追加到尾部
                    array_push($list,$line);
                }
            }
        }
        $result[$type?'info':'list'] = $type?array_first($list):$list;
        $subtotal = sprintf("%.2f",$subtotal);
        return $result;
    }

    /**
     * ids: SKU1,QTY|SKU2,QTY 转化成数组
     * @param $ids
     * @return mixed
     */
    public function idsToCartItems($ids){
        //拆解前端传过来的ids
        return array_reduce(explode('|',$ids),function($result,$item){
            $product = explode(',',$item);
            $result[$product[0]] = intval($product[1]);
            return $result;
        });
    }

    public function fillCartFromMagento($cart){
        $new_cart = [
            'shipping' => sprintf("%.2f",object_get($cart,'shipping',0)),
            'subTotal' => sprintf("%.2f",object_get($cart,'subTotal',0)),
            'amount' => sprintf("%.2f",object_get($cart,'amount',0)),
        ];
        $list = object_get($cart,'list',[]);
        $skus = array_column($list,'sku');
        //获取商品属性
        $items_attr = collect((new InnerProductRepository())->getSkus($skus))->keyBy('sku');
        foreach ($list as $item){
            $item_attr = $items_attr->get($item->sku);
            $new_cart['list'][] = [
                'id' => $item_attr['sku'],
                'goodsId' => $item_attr['parent_id'],
                'name' => $item_attr['name'],
                'price' => sprintf("%.2f",$item_attr['price']),
                'images' => $item_attr['kv'],
                'color'=>[
                    'id'=>$item_attr['color'],
                    'name'=>$item_attr['color_value'],
                    'value'=>$item_attr['color_value'],
                ],
                'size'=>[
                    'id'=>$item_attr['sku'],
                    'name'=>$item_attr['size_value'],
                    'stock'=>intval($item_attr['stock']),
                ],
                'quantity' => $item->qty,
                'total' => sprintf("%.2f",$item->total),
            ];
        }
        return $new_cart;
    }

    /**
     * 合并购物车
     * @param $uid
     * @param $items
     * @return mixed
     */
    public function mergeCart($uid,$items){
        $old_items = CheckoutRepository::getCart($uid);
        $maxQty = CheckoutRepository::$maxQty;
        foreach($old_items as $sku=>$qty){
            if(array_key_exists($sku,$items)){
                $sum = $items[$sku]+$qty;
                $sum = $sum>$maxQty?$maxQty:$sum;
                $items[$sku] = intval($sum);
            }else{
                $items[$sku] = intval($qty);
            }
        }
        return $items;
    }

    /**
     * 在原购物车中去除已购买的商品集合并且返回剩余商品集合
     * @param $uid
     * @param $items
     * @return mixed
     */
    public function diffCart($uid,$items){
        $old_items = CheckoutRepository::getCart($uid);
        foreach($old_items as $sku=>$qty){
            if(array_key_exists($sku,$items)){
                $diff = $qty-$items[$sku];
                $old_items[$sku] = intval($diff);
            }
        }
        return $old_items;
    }

    /**
     * 返回15位纯数字流水号
     * @return string
     */
    public function makeSerialsNumber(){
        $date = date('mdHis');
        $inc = sprintf("%04d", CheckoutRepository::getInc());
        $rand = rand(100,999);
        return "{$date}{$inc}{$rand}";
    }

    /**
     * 获取下单时填写的发票
     * @return array|bool
     * @throws ApiPlaintextException
     */
    public function getInvoice(){
        $invoice = request()->get('invoice');
        if($invoice){
            $invoice_title = array_get($invoice,'title');
            $invoice_name = array_get($invoice,'name');
            if($invoice_title && $invoice_name){
                $code = array_get($invoice,'code');
                if($code && !in_array(strlen($code),[15,17,18,20])){
                    throw new ApiPlaintextException('纳税人识别号格式不正确,应为15,17,18,20位');
                }
                return array_filter([
                    'title'=>$invoice_title,
                    'name'=>$invoice_name,
                    'code'=>$code,
                ]);
            }
        }
        return false;
    }

    /**
     * 生成18位随机数编号
     * @return string
     */
    public function getRandomNumber(){
        $rand = '10'.rand(100,999).time().rand(100,999);
        return "{$rand}";
    }

    /**
     * 获取商品库存
     * @param $sku
     * @return bool|int
     */
    public function getStock($sku){
        $items_attr = (new InnerProductRepository())->getSkus([$sku]);
        $item = array_first($items_attr);
        if($item){
            return $item['stock'];
        }return false;//无此商品
    }


}
