<?php
namespace App\Service\Rule\Code;
use App\Service\Rule\Code\Product\CodeProductDiscount;
use App\Service\Rule\Code\Product\CodeNpieceNdiscount;
use App\Service\Rule\Code\Order\CodeFullReductionOfOrder;
use App\Service\Rule\Code\Order\CodeOrderNDiscount;
use App\Service\Rule\Code\CodeGift;
//优惠码
//矩阵： 不能与会员折扣叠加，可以与优惠券，满减，折扣叠加
//
class Code 
{
    public $data = [];
    public function process($data){
        $this->data = $data;
        $input_coupon_code = $this->data['code'];
        if(!$input_coupon_code){
            return $this->data;
        }
        $rule = $this->getRuleByCode($input_coupon_code);
        if(!$rule){
            return $this->data;
        }
        if($rule['type'] == 'code_product_discount'){
            $this->data = (new CodeProductDiscount())->setData($this->data)->process($rule);
        }
        if($rule['type'] == 'code_n_piece_n_discount'){
            $this->data = (new CodeNpieceNdiscount())->setData($this->data)->process($rule);
        }
        if($rule['type'] == 'code_full_reduction_of_order'){
            $this->data = (new CodeFullReductionOfOrder())->setData($this->data)->process($rule);
        }
        if($rule['type'] == 'code_order_n_discount'){
            $this->data = (new CodeOrderNDiscount())->setData($this->data)->process($rule);
        }
        if($rule['type'] == 'code_gift'){
            $this->data = (new CodeGift())->setData($this->data)->process($rule);
        }
        return $this->data;
    }
    public function getRuleByCode($code){
        $rules = $this->data['rules'];
        foreach($rules as $rule){
            $code_stock = $rule['code_stock'];
            $code_stock_used = (int) $rule['code_stock_used'];
            $rest_stock = (int) bcsub($code_stock,$code_stock_used);
            if($rule['code_code'] == $code and $rest_stock >0 ){
                return $rule;
            }
        }
        return false; 
    }

}

