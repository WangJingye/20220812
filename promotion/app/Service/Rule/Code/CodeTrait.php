<?php
namespace App\Service\Rule\Code;
//优惠码
//先检测优惠码是否有效
//
Trait CodeTrait  
{
    //检测code是否有效
    public function codeCheck($rule){
        $input_coupon_code = $this->data['code'];
        if(!$input_coupon_code){
            return false;
        }
        $code_stock = $rule['code_stock'];
        $code_stock_used = (int) $rule['code_stock_used'];
        $rest_stock = (int) bcsub($code_stock,$code_stock_used);
        if(strtolower($rule['code_code']) == strtolower($input_coupon_code) and $rest_stock >0 ){
            return true;
        }
        return false;
    }

}

