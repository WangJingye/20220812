<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/7/8
 * Time: 14:15
 */

namespace App\Http\Controllers\Api\Oms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\AfterOrderSale;
use Illuminate\Support\Facades\Log;
use App\Services\Api\Oms\AfterSalesOrdersServices;


class AfterSalesOrdersController extends Controller
{


    public function createdAfterOrder(Request $request)
    {
        $status = AfterOrderSale::createAfterOrder($request->all());
        if($status){
            return $this->success("success", []);
        }
        return $this->error('fail');

    }

    public function afterOrderPay(Request $request)
    {
        Log::info('afterOrderPay:header',$request->header());
        Log::info('afterOrderPay:content'.$request->getContent());
        Log::info('afterOrderPay:input',$request->all());
        $status = $request->get('orderStatus',0);
        if($status =='0000' || $status =='1003'){
            $status = AfterOrderSale::paySuccess($request->get('OriOrderNo'));
            if($status){
                return $this->success("success", []);
            }
            return $this->error('fail');
        }

        return $this->error('fail');
    }

}