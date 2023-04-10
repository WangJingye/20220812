<?php namespace App\Dlc\Coupon\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Model\Promotion\Cart;

class CouponController extends ApiController
{
    /**
     * 获取促销详情
     * @param Request $request
     * @return array
     */
    public function getDetail(Request $request){
        try{
            $id = $request->get('id');
            if($id){
                $detail = Cart::find($id);
                return $this->success($detail);
            }throw new \Exception('促销不存在');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 获取新人券详情
     * @param Request $request
     * @return array
     */
    public function getNewDetail(Request $request){
        try{
            $currDate = date('Y-m-d H:i:s');
            $detail = Cart::query()->where('is_new',1)
                ->where('status',2)
                ->where('start_time','<=',$currDate)
                ->where('end_time','>=',$currDate)
                ->orderBy('id','desc')
                ->first();
            if($detail){
                return $this->success($detail);
            }throw new \Exception('促销不存在');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 领取优惠券 批量处理
     * @param Request $request
     * @return array
     */
    public function couponUsePluck(Request $request){
        try{
            $ids_pluck = $request->get('ids_pluck');
            if($ids_pluck){
                $carts = Cart::whereIn('id',array_keys($ids_pluck))->get();
                foreach($carts as $cart){
                    $used = Arr::get($ids_pluck,$cart->id);
                    if($used){
                        $cart->increment('coupon_stock_used',$used);
                    }
                }
            }
            return $this->success();
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 领取优惠券（单个）
     * @param Request $request
     * @return array
     */
    public function couponUse(Request $request){
        try{
            $id = $request->get('id');
            if($id){
                Cart::find($id)->increment('coupon_stock_used');
                return $this->success();
            }throw new \Exception('促销不存在');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

}
