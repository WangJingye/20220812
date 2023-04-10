<?php namespace App\Services\Dlc;

use App\Repositories\CartRepository;

class CouponCode
{
    /**
     * @param $data
     */
    public static function exists(&$data,$uid):void{
        //如果优惠码无效则显示错误
        $coupon_code = array_get($data,'coupon_code');
        $code_applied = array_get($data,'promotion_data.code_applied');
        if(!empty($coupon_code) && empty($code_applied)){
            $data['coupon_code'] = '';
            $data['error_message'] = '优惠码不可用';
            if($uid){
                $checkoutInfo = CartRepository::getCheckoutInfo($uid);
                if(!empty($checkoutInfo['coupon_code'])){
                    $checkoutInfo['coupon_code'] = '';
                    CartRepository::setCheckoutInfo($uid,$checkoutInfo);
                }
            }
        }
    }


}
