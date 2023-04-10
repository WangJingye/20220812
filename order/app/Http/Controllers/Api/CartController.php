<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\{DelRequest,Add2CartRequest};
use App\Services\Api\{CartServices};
use App\Repositories\{CartRepository};
use App\Exceptions\ApiPlaintextException;
use App\Services\Cart\{GetCartInfo};

class CartController extends ApiController
{
    protected $cartServices;
    protected $from;

    /**
     * CartController constructor.
     * @throws ApiPlaintextException
     */
    public function __construct(){
        $this->cartServices = new CartServices;
        $this->from = $this->getFrom();
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function getCartInfo(Request $request){
        $uid = $this->getUid();
        //直接获取购物车数据
        $checkoutItems = CartRepository::getCart($uid);
        //构建购物车信息
        $info = $this->cartServices->makeUpCart($uid,$checkoutItems,$this->from);
        $info['maxQty'] = CartRepository::$maxQty;
        return $this->success($info);
    }

    /**
     * @param Request $request
     * @return array|mixed
     * @throws \Exception
     */
    public function checkStock(Request $request){
        $uid = $this->getUid(1);
        $ids = $request->get('ids');
        if(empty($uid) && $ids){
            $checkoutItems = $ids?$this->cartServices->idsToCartItems($ids):[];
        }elseif($uid){
            $checkoutItems = CartRepository::getCart($uid);
        }else{
            throw new ApiPlaintextException('购物车为空');
        }
        if($this->cartServices->checkStock($uid,$checkoutItems,$this->from)){
            return $this->success();
        }throw new ApiPlaintextException('您加购的商品库存不足');
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
        CartRepository::delCart($uid,$sku);
        //删除选中的,checkout_info
        $this->cartServices->delCheckoutInfoSku($uid,$sku);
        //直接获取购物车数据
        $checkoutItems = CartRepository::getCart($uid);
        //构建购物车信息
        $info = $this->cartServices->makeUpCart($uid,$checkoutItems,$this->from);
        return $this->success($info);
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
        $oldCartItems = CartRepository::getCart($uid);
        //获取增加后的商品数量
        $addItemQty = array_get($oldCartItems,$sku,0)+$qty;
        //获取商品最大数量
        $maxQty = CartRepository::$maxQty;
        if($addItemQty > $maxQty){
            throw new ApiPlaintextException("商品最多只能购买{$maxQty}件");
        }
        //加入购物车
        CartRepository::addCart($uid,$sku,$qty);
        $this->cartServices->addCheckoutInfoSku($uid,$sku);
        return $this->success([]);
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
        $delete_sku = $request->get('delete_sku');
        $qty = $request->get('quantity');
        //获取商品最大数量
        $maxQty = CartRepository::$maxQty;
        if($qty > $maxQty){
            throw new ApiPlaintextException("商品最多只能购买{$maxQty}件");
        }
        if($delete_sku && $sku==$delete_sku){
            //选择当前规格
            CartRepository::updateCart($uid,[$sku=>$qty]);
        }elseif(($sku && empty($delete_sku))){
            //+-
            CartRepository::updateCart($uid,[$sku=>$qty]);
        }elseif($sku && $delete_sku){
            //更换规格
            CartRepository::replaceCart($uid,$sku,$qty,$delete_sku);
            $this->cartServices->replaceCheckoutInfoSku($uid,$delete_sku,$sku);
        }
        $checkoutItems = CartRepository::getCart($uid);
        //构建购物车信息
        $info = $this->cartServices->makeUpCart($uid,$checkoutItems,$this->from);
        return $this->success($info);
    }

    /**
     * 保存购物车选中商品
     * @param Request $request
     * @return array|mixed
     * @throws \Exception
     */
    public function updateSelect(Request $request){
        $uid = $this->getUid();
        $coupon_id = $request->get('coupon_id')??'';
        $coupon_code = $request->get('coupon_code')??'';
        $skus = $request->get('skus')??[];
        $free_skus = $request->get('free_skus')??[];
        $flag = $request->get('flag')??1;

        $data = [
            'uid'=>$uid,
            'coupon_id'=>$coupon_id,
            'coupon_code'=>$coupon_code,
            'skus'=>$skus,
            'free_skus'=>$free_skus,
            'flag'=>$flag,
        ];
        $this->cartServices->updateSelect($data);
        //构建购物车信息
        $checkoutItems = CartRepository::getCart($uid);
        $info = $this->cartServices->makeUpCart($uid,$checkoutItems,$this->from);
        return $this->success($info);
    }

}
