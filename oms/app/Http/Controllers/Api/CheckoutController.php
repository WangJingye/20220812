<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\{DelRequest,Add2CartRequest,CheckoutRequest,PayRequest};
use App\Repositories\{OrderRepository, UserRepository, CheckoutRepository, AddressRepository};
use App\Exceptions\ApiPlaintextException;

class CheckoutController extends ApiController
{
    /**
     * @var \App\Services\Api\UserServices
     */
    public $userServices;
    /**
     * @var \App\Services\Api\CheckoutServices
     */
    public $checkoutServices;
    public $magentoApi;
    public function __construct(){
        $this->userServices = app('UserServices');
        $this->checkoutServices = app('CheckoutServices');
        $this->magentoApi = app('ApiRequestMagento');
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getCartInfo(Request $request){
        $uid = $this->getUid(1);
        $ids = $request->get('ids');
        $info = [];
        if(empty($uid) && $ids){
            //未登录并且传了商品SKU 则查询商品数据并计算购物车
            $cartItems = $this->checkoutServices->idsToCartItems($ids);
            //构建购物车信息
            $info = $this->checkoutServices->makeUpCart($cartItems);
        }elseif($uid){
            //已登录
            if($ids){
                //将前端缓存数据合并加入购物车
                $cartItems = $this->checkoutServices->idsToCartItems($ids);
                //合并购物车
                $cartItems = $this->checkoutServices->mergeCart($uid,$cartItems);
                //更新合并后的购物车
                $cartItems = CheckoutRepository::updateCart($uid,$cartItems);
                //构建购物车信息
                $info = $this->checkoutServices->makeUpCart($cartItems);
            }else{
                //直接获取购物车数据
                $cartItems = CheckoutRepository::getCart($uid);
                //构建购物车信息
                $info = $this->checkoutServices->makeUpCart($cartItems);
            }
        }
        return $this->success($info);
    }

    /**
     * 删除购物车中的商品
     * @param DelRequest $request
     * @return array
     * @throws \Exception
     */
    public function delCart(DelRequest $request){
        $uid = $this->getUid();
        $sku = $request->get('id');
        //删除购物车
        CheckoutRepository::delCart($uid,$sku);
        //直接获取购物车数据
        $cartItems = CheckoutRepository::getCart($uid);
        //构建购物车信息
        $info = $this->checkoutServices->makeUpCart($cartItems);
        return $this->success(['subTotal'=>array_get($info,'subTotal','0.00')]);
    }

    /**
     * 加入购物车
     * @param Add2CartRequest $request
     * @return array
     * @throws \Exception
     */
    public function addToCart(Add2CartRequest $request){
        $uid = $this->getUid();
        $sku = $request->get('id');
        $qty = $request->get('quantity');
        //获取购物车信息
        $oldCartItems = CheckoutRepository::getCart($uid);
        //获取增加后的商品数量
        $addItemQty = array_get($oldCartItems,$sku,0)+$qty;
        //获取商品最大数量
        $maxQty = CheckoutRepository::$maxQty;
        if($addItemQty > $maxQty){
            throw new ApiPlaintextException("商品最多只能购买{$maxQty}件");
        }
        //获取商品当前库存并检查
        $stock = $this->checkoutServices->getStock($sku);
        if($stock === false){
            throw new ApiPlaintextException('无效的商品');
        }elseif($addItemQty > $stock ){
            throw new ApiPlaintextException('商品库存不足');
        }
        //加入购物车
        $cartItems = CheckoutRepository::addCart($uid,$sku,$qty);
        //构建购物车信息
        $info = $this->checkoutServices->makeUpCart($cartItems,1);
        return $this->success($info);
    }

    /**
     * 更新购物车中的商品
     * @param Add2CartRequest $request
     * @return array
     * @throws \Exception
     */
    public function updateOption(Add2CartRequest $request){
        $uid = $this->getUid();
        $sku = $request->get('id');
        $qty = $request->get('quantity');
        //获取商品最大数量
        $maxQty = CheckoutRepository::$maxQty;
        if($qty > $maxQty){
            throw new ApiPlaintextException("商品最多只能购买{$maxQty}件");
        }
        //获取商品当前库存并检查
        $stock = $this->checkoutServices->getStock($sku);
        if($stock === false){
            throw new ApiPlaintextException('无效的商品');
        }elseif($qty > $stock ){
            throw new ApiPlaintextException('商品库存不足');
        }
        $cartItems = [$sku=>$qty];
        //更新合并后的购物车
        $cartItems = CheckoutRepository::updateCart($uid,$cartItems);
        //构建购物车信息
        $info = $this->checkoutServices->makeUpCart($cartItems,1);
        return $this->success($info);
    }

    /**
     * 结算
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function confirm(Request $request){
        $uid = $this->getUid();
        $ids = $request->get('ids');
        if($ids){
            //将前端缓存数据合并加入购物车
            $cartItems = $this->checkoutServices->idsToCartItems($ids);
        }else{
            //直接获取购物车数据
            $cartItems = CheckoutRepository::getCart($uid);
        }
        if($cartItems){
            //加入到magento购物车中
            $resp = $this->magentoApi->exec(['url'=>"V1/connext/carts/{$uid}/items/makeup",'method'=>'POST'],[
                'cartItems'=>$cartItems,
                //shippingMethod暂时写死 目前只有免运费
                'shippingMethod'=>'freeshipping_freeshipping',
            ]);
            $resp = json_decode($resp);
            if($resp->code == 0){
                throw new ApiPlaintextException($resp->message);
            }
            $info = $this->checkoutServices->fillCartFromMagento($resp->info);
        }else{
            //空购物车
            $info['list'] = [];
        }
        //构建购物车信息
        $info['address'] = (new \App\Services\Api\AddressServices)->getAddressListByUid($uid);
        return $this->success($info);
    }

    /**
     * 结算下单
     * @param CheckoutRequest $request
     * @return array
     * @throws \Exception
     */
    public function createOrder(CheckoutRequest $request){
        $uid = $this->getUid();
        //获取收货地址
        $addressId = $request->get('addressId');
        $address = AddressRepository::getAddress($addressId,$uid);
        if(empty($address)){
            throw new ApiPlaintextException('配送地址不存在');
        }
        $shippingMethod = $this->checkoutServices->getShippingMethod($request->get('shippingMethod'));
        $paymentMethod = $this->checkoutServices->getPaymentMethod($request->get('paymentMethod'));
        //生成流水号
        $serialsNumber = $this->checkoutServices->makeSerialsNumber();
        //调用magento接口下单
        $resp = $this->magentoApi->exec(['url'=>"V1/connext/carts/{$uid}/place-order",'method'=>'POST'],[
            'shippingInfo'=>[
                'address'=>[
                    'firstname'=>$address->firstname,
                    'lastname'=>$address->lastname,
                    'telephone'=>$address->telephone,
                    'sex'=>$address->sex,
                    'region'=>$address->region,
                    'city'=>$address->city,
                    'street'=>$address->street,
                    'area'=>$address->area,
                    'postcode'=>$address->postcode,
                    'customer_address_id'=>$addressId,
                ],
                'method'=>$shippingMethod
            ],
            'paymentMethod'=>$paymentMethod->magentoMethod,
            'serialsNumber'=>$serialsNumber,
        ]);
        $resp = json_decode($resp);
        if($resp->code == 0){
            throw new ApiPlaintextException($resp->message);
        }
        //获取主订单号
        $order_id = (string)(object_get($resp,'order.order_id'));
        $entity_id = object_get($resp,'order.entity_id');
        //下单成功后dispatch
        $this->dispatch(new \App\Jobs\CreateOrderAfter($entity_id));
        //创建订单成功后增加自动取消订单的延时队列任务
        $this->dispatch(new \App\Jobs\CancelOrder($order_id));
        //获取并判断是否有传发票
        $invoice = $this->checkoutServices->getInvoice();
        if($invoice){
            $this->dispatch(new \App\Jobs\SaveInvoice(array_merge($invoice,[
                'tid'=>$order_id,
                'status'=>'new'
            ])));
        }
        //获取magento返回值
        $cartItems = (array)$resp->cartItems;
        //去除已下单的商品
        $lastCartItems = $this->checkoutServices->diffCart($uid,$cartItems);
        //更新购物车商品
        CheckoutRepository::updateCart($uid,$lastCartItems);
        //组合主订单号+流水号
        $client_sn = "{$order_id}-{$serialsNumber}";
        $total_amount =(string)(sprintf("%.2f",object_get($resp,'order.grand_total'))*100);
        //返回支付连接
        $url = $this->checkoutServices->orderPay($client_sn,$total_amount,$paymentMethod,$order_id);
        if($url){
            return $this->success(['id'=>$order_id,'url'=>$url]);
        }throw new \Exception('支付异常');
    }

    /**
     * @param PayRequest $request
     * @return array
     * @throws \Exception
     */
    public function orderPay(PayRequest $request){
        $uid = $this->getUid();
        $order_id = $request->get('orderId');
        $order = OrderRepository::getDetail($order_id);
        $status = object_get($order,'status');
        if($status == 'TRADE_CLOSED_BY_TAOBAO'){
            throw new ApiPlaintextException('订单已取消');
        }elseif($status == 'WAIT_SELLER_SEND_GOODS'){
            throw new ApiPlaintextException('订单已支付');
        }elseif($status == 'WAIT_BUYER_PAY'){
            $paymentMethod = $this->checkoutServices->getPaymentMethod($request->get('paymentMethod'));
            //从DB中获取主订单对应的支付流水号
            $serialsNumber = CheckoutRepository::getSerialsNumberFromDB($order_id);
            //组合主订单号+流水号
            $old_client_sn = "{$order_id}-{$serialsNumber}";
            //查询订单支付状态
            $resp = $this->checkoutServices->queryPay($old_client_sn);
            //已支付过的不允许重复支付
            $order_status = object_get($resp,'biz_response.data.order_status');
            if($order_status == 'PAID'){
                throw new ApiPlaintextException('该订单已支付,请勿重复支付');
            }
            //生成流水号
            $serialsNumber = $this->checkoutServices->makeSerialsNumber();
            //保存流水号到DB
            CheckoutRepository::setSerialsNumberToDB($order_id,$serialsNumber);
            //组合主订单号+流水号
            $client_sn = "{$order_id}-{$serialsNumber}";
            //获取订单金额
            $total_amount = (string)(sprintf("%.2f",CheckoutRepository::getAmount($uid,$order_id))*100);
            //调用接口支付
            $url = $this->checkoutServices->orderPay($client_sn,$total_amount,$paymentMethod,$order_id);
            if($url){
                return $this->success(['id'=>$order_id,'url'=>$url]);
            }throw new \Exception('支付异常');
        }throw new \Exception('订单异常');
    }

}
