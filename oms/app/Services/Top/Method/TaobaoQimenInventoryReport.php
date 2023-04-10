<?php

namespace App\Services\Top\Method;

use App\Model\OrderItem;
use App\Model\Sku;
use App\Services\Top\TopAbstract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Model\Order;
use App\Support\Sms;
use Illuminate\Support\Facades\Redis;

/**
 *
 *
 * @author auto create
 * @since 1.0, 2018.08.16
 */
class TaobaoQimenInventoryReport extends TopAbstract
{


    public function execute(): array
    {

        $data = $this->request->params;
        Log::info(
            "qmapi.request",
            $data
        );

        $form_data = [];
        foreach($data['items']['item'] as $v){
            if($v['inventoryType']=='CC'){
                $status =3;
            }else{
                $status =2;
            }
                $value = [
                    'is_auto'=>1,
                    'status'=>$status,
                    'sku'=>$v['itemCode'],
                    'branch'=>$v['batchCode'],
                    'actual_number'=>$v['quantity'],
                    'remark'=>$data['checkOrderCode'],
                ];
            $form_data[] = $value;
        }
        $sku = new Sku;
        $splitNum = 50;
        foreach(array_chunk($form_data, $splitNum) as $values) {
            $status = $sku->addSkuStock($values);
        }

        if($status){
            return [0, 'success', []];
        }
        return [50, 'fail', []];

    }
}