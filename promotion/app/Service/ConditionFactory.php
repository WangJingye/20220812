<?php
namespace App\Service;

use App\Service\Condition\ProductDiscount;
use App\Service\Condition\NpieceNdiscount;
use App\Service\Condition\OrderNDiscount;
use App\Service\Condition\FullReductionOfOrder;
use App\Service\Condition\Coupon;
use App\Service\Condition\CouponDiscount;
use App\Service\Condition\ProductCoupon;
use App\Service\Condition\Gift;
use App\Service\Condition\FreeTry;
use App\Service\Condition\ShipFeeTry;
use App\Service\Condition\Code\CodeProductDiscount;
use App\Service\Condition\Code\CodeNpieceNdiscount;
use App\Service\Condition\Code\CodeOrderNDiscount;
use App\Service\Condition\Code\CodeFullReductionOfOrder;
use App\Service\Condition\Code\CodeGift;


class ConditionFactory
{
    //根据类型，创建促销条件实例
    public static function createCondition($rule_type){
        switch ($rule_type){
            case 'product_discount':
                return (new ProductDiscount());
                break;
            case 'n_piece_n_discount':
                return (new NpieceNdiscount());
                break;
            case 'full_reduction_of_order':
                return (new FullReductionOfOrder());
                break;
            case 'order_n_discount':
                return (new OrderNDiscount());
                break;
            case 'coupon':
                return (new Coupon());
                break;
            case 'coupon_discount':
                return (new CouponDiscount());
                break;
            case 'product_coupon':
                return (new ProductCoupon());
                break;
            case 'gift':
                return (new Gift());
                break;
            case 'free_try':
                return (new FreeTry());
                break;
            case 'ship_fee_try':
                return (new ShipFeeTry());
                break;
            case 'code_product_discount':
                return (new CodeProductDiscount());
                break;
            case 'code_n_piece_n_discount':
                return (new CodeNpieceNdiscount());
                break;
            case 'code_full_reduction_of_order':
                return (new CodeFullReductionOfOrder());
                break;
            case 'code_order_n_discount':
                return (new CodeOrderNDiscount());
                break;
            case 'code_gift':
                return (new CodeGift());
                break;
            default:
                return '';
        }
    }
}

