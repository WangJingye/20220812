<?php
namespace App\Service;

use App\Service\Rule\Product\ProductDiscount;
use App\Service\Rule\Product\NpieceNdiscount;
use App\Service\Rule\Order\OrderNDiscount;
use App\Service\Rule\Order\FullReductionOfOrder;
use App\Service\Rule\Coupon\Coupon;
use App\Service\Rule\Coupon\CouponDiscount;
use App\Service\Rule\Coupon\ProductCoupon;
use App\Service\Rule\Coupon\CouponList;
use App\Service\Rule\Code\Code;
use App\Service\Rule\Gift;
use App\Service\Rule\ShipFeeTry\ShipFeeTry;
use App\Service\Rule\FreeTry\FreeTry;
use App\Service\Rule\Init;
use App\Service\Rule\Point\Earn;
use App\Service\Rule\Point\Auto;
use App\Service\Rule\Point\ManualPoints;
use App\Service\Rule\Point\LimitedPoint;
use App\Service\Rule\Total\Total;
use App\Model\Promotion\Gift as ModelGift;
use App\Model\Promotion\Cart;
use App\Service\ConditionFactory;
use App\Service\Rule\Code\Product\CodeProductDiscount;
use App\Service\Rule\Code\Product\CodeNpieceNdiscount;
use App\Service\Rule\Code\Order\CodeOrderNDiscount;
use App\Service\Rule\Code\Order\CodeFullReductionOfOrder;
use App\Service\Rule\Code\CodeGift;


class RuleFactory
{
    public $rule_instances = [];
    //根据类型，创建促销实例
    public function createRule($rule_type){
        $condition = ConditionFactory::createCondition($rule_type);
        switch ($rule_type){
            case 'product_discount':
                $obj = (new ProductDiscount());
                break;
            case 'n_piece_n_discount':
                $obj = (new NpieceNdiscount());
                break;
            case 'full_reduction_of_order':
                $obj = (new FullReductionOfOrder());
                break;
            case 'order_n_discount':
                $obj = (new OrderNDiscount());
                break;
            case 'coupon':
                $obj = (new Coupon());
                break;
            case 'coupon_discount':
                $obj = (new CouponDiscount());
                break;
            case 'product_coupon':
                $obj = (new ProductCoupon());
                break;
            case 'gift':
                $obj = (new Gift());
                break;
            case 'free_try':
                $obj = (new FreeTry());
                break;
            case 'ship_fee_try':
                $obj = (new ShipFeeTry());
                break;
            case 'code_product_discount':
                $obj = (new CodeProductDiscount());
                break;
            case 'code_n_piece_n_discount':
                $obj = (new CodeNpieceNdiscount());
                break;
            case 'code_full_reduction_of_order':
                $obj = (new CodeFullReductionOfOrder());
                break;
            case 'code_order_n_discount':
                $obj = (new CodeOrderNDiscount());
                break;
            case 'code_gift':
                $obj = (new CodeGift());
                break;
            default:
                return '';
        }
        $obj->setCondition($condition);
        return $obj;
    }

    public function getSingleton($rule_type){
        $rule_instances = $this->rule_instances;
        if(isset($rule_instances[$rule_type])){
            return $rule_instances[$rule_type];
        }
        $rule_obj = $this->createRule($rule_type);
        $this->rule_instances[$rule_type] = $rule_obj;
        return $rule_obj;
    }
}

