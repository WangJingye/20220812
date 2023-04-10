<?php namespace App\Services\Checkout\Depend;


class Stock
{
    //扣除库存
    public function decreaseStock($data_obj){
        return $data_obj;
        $data = $data_obj->getData();
        $order_sn = $data['order_sn'];
        $channel_id = $data['channel'];
        $api = app('ApiRequestInner',['module'=>'goods']);
        $sku_qty = [];
        $goods_list = $data['goods_list'];
        foreach($goods_list as $item ){
            $sku_qty[] = [
                $item['sku'],$item['qty'],$order_sn,
            ];
        }
        $sku_json = json_encode($sku_qty);
        $input_data = [
            'sku_json'=>$sku_json,
            'channel_id'=>$channel_id,
            'increment'=>0,
        ];
        $resp = $api->request('outward/update/batchStock','POST',$input_data);
        $resp = json_decode($resp,true);
        if($resp['code'] == 1){
            return true;
        }
        //失败归还库存
        $flag = $this->restoreStock($data_obj);
        return false;
    }

    //归还库存
    public function restoreStock($data_obj){
        $data = $data_obj->getData();
        $order_sn = $data['order_sn'];
        $channel_id = $data['channel'];
        $api = app('ApiRequestInner',['module'=>'goods']);
        $sku_qty = [];
        $goods_list = $data['goods_list'];
        foreach($goods_list as $item ){
            $sku_qty[] = [
                $item['sku'],$item['qty'],$order_sn,
            ];
        }
        $sku_json = json_encode($sku_qty);
        $input_data = [
            'sku_json'=>$sku_json,
            'channel_id'=>$channel_id,
            'increment'=>1,
        ];
        $resp = $api->request('outward/update/batchStock','POST',$input_data);
        $resp = json_decode($resp,true);
        if($resp['code'] == 1){
            return true;
        }
        return false;
    }

}