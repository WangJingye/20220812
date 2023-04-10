<?php namespace App\Services\Dlc;

use App\Model\{Order,OrderItem};

class DataService
{
    /**
     * @param $data
     * @return bool|string
     */
    public static function ship_import($data){
        $i=1;
        try {
            foreach($data as $item){
                $order_sn = trim($item[0]);
                $delivery_mode = 'SF';
                $express_no = trim($item[1]);
                Order::statusChangeShip($order_sn,$delivery_mode,$express_no);
                $i++;
            }return true;
        }catch (\Exception $e){
            return $e->getMessage().',行数:'.$i;
        }
    }

    public static function stock_import($data){
        try{
            $sku_qty = [];
            foreach($data as $item){
                $sku = trim($item[0]);
                $stock = intval(trim($item[1])?:0);
                $sku_qty[$sku] = $stock;
            }
            if($sku_qty){
                OmsServices::LvmhSiteSyncGoodsStock($sku_qty,1);
            }return true;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
