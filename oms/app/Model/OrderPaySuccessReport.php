<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderPaySuccessReport extends Model
{
    protected $table = 'oms_order_pay_success_report';

    /**
	 * paySuccessReport
     */
    public static function paySuccessReport($data)
    {
    	$insertArr = [];
    	if (!empty($data['actionField'])) {
    		if (is_array($data['actionField'])) {
    			$actionField = $data['actionField'];
    		} else {
	    		$actionField = json_decode($data['actionField'], true);
    		}
    		if (!empty($actionField)) {
    			$insertArr = [
    				'step'								=> !empty($actionField['step']) ? $actionField['step'] : '',
    				'currencyCode'						=> !empty($actionField['currencyCode']) ? $actionField['currencyCode'] : '',
    				'order_sn'							=> !empty($actionField['id']) ? $actionField['id'] : '',
    				'affiliation'						=> !empty($actionField['affiliation']) ? $actionField['affiliation'] : '',
    				'revenue'							=> !empty($actionField['revenue']) ? $actionField['revenue'] : 0,
    				'revenueHt'							=> !empty($actionField['revenueHt']) ? $actionField['revenueHt'] : 0,
    				'tax'								=> !empty($actionField['tax']) ? $actionField['tax'] : 0,
    				'shipping'							=> !empty($actionField['shipping']) ? $actionField['shipping'] : 0,
    				'transactionGift'					=> !empty($actionField['transactionGift']) ? $actionField['transactionGift'] : '',
    				'transactionShippingService'		=> !empty($actionField['transactionShippingService']) ? $actionField['transactionShippingService'] : '',
    				'transactionShippingFree'			=> !empty($actionField['transactionShippingFree']) ? $actionField['transactionShippingFree'] : '',
    				'transactionPayment'				=> !empty($actionField['transactionPayment']) ? $actionField['transactionPayment'] : '',
    				'coupon'							=> !empty($actionField['coupon']) ? $actionField['coupon'] : '',
    				'coupon_id'							=> !empty($actionField['coupon_id']) ? $actionField['coupon_id'] : '',
    				'created_at'						=> date('Y-m-d H:i:s')
				];
    		}
    	}
    	DB::beginTransaction();
 		try {
 			if (!empty($insertArr)) {
 				self::insert($insertArr);
 			}
 			DB::commit();
 			return true;
 		} catch (\Exception $e) {
 			DB::rollback();
 			Log::info('App\Model\OrderPaySuccessReport\paySuccessReport:'.$e);
 			return false;
 		}
    }
}
