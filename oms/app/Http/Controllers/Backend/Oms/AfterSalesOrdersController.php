<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/7/8
 * Time: 14:15
 */

namespace App\Http\Controllers\Backend\Oms;


use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use App\Model\AfterOrderSale;
use Illuminate\Support\Facades\Log;
use App\Services\Api\Oms\AfterSalesOrdersServices;


class AfterSalesOrdersController extends ApiController
{

    /**
     * 创建售后单
     * @param Request $request
     * @return array|mixed
     */
    public function createdAfterOrder(Request $request)
    {
        $data = $request->all();

        if ($data['action_type'] == 2) {
            $status = AfterOrderSale::editAfterOrder($request->all());
        } else {
            $status = AfterOrderSale::createAfterOrder($request->all());
        }

        if ($status) {
            return $this->success('success');
        } else {
            return $this->error('fail');
        }
    }

    public function refund(Request $request)
    {
        $status = AfterOrderSale::returnMoney($request->get('order_id'),$request->get('type'),$request->all());
        if ($status) {
            return $this->success('success');
        } else {
            return $this->error('fail');
        }

    }


}