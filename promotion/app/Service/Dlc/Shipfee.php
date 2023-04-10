<?php namespace App\Service\Dlc;

use Illuminate\Support\Facades\DB;
use App\Model\Promotion\Shipfee as ShipfeeModel;
use Illuminate\Support\Facades\Redis;
use function Matrix\add;

class Shipfee
{
    public function process($data){
        $address = array_get($data,'address');
        $total_amount = $data['total_discount']['total_amount'];
        if(count($data['cartItems'])){
            $fee = $this->getShipfeeByAddress($address,$total_amount);
            if($fee!=0){
                $data['total_discount']['total_ship_fee'] = sprintf('%0.2f',$fee);
                $data['total_discount']['total_amount'] = sprintf('%0.2f',bcadd($fee,$total_amount));
            }
        }
        return $data;
    }

    //根据地址获取邮费
    public function getShipfeeByAddress($address,$total_amount){
        $fee = 0;
        $json = Redis::hget(ShipfeeModel::CacheKey,$address);
        if(empty($json)){
            $json = Redis::hget(ShipfeeModel::CacheKey,ShipfeeModel::$default);
        }
        if($json){
            $obj = json_decode($json);
            if($obj->is_free!=1&&($obj->free_limit>$total_amount)){
                $fee = $obj->ship_fee;
            }
        }
        return $fee;
    }















}