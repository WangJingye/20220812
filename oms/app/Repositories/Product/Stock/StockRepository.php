<?php namespace App\Repositories\Product\Stock;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Product\Redis\KeyRepository;

class StockRepository
{
    public $stock_table = 'cataloginventory_stock_item';
    
    public function cancelOrder($pid_qty=[]){
        $stock_key = KeyRepository::getKey('productStock');
        $lock_key = KeyRepository::getKey('productStockLock');
        foreach($pid_qty as $pid=>$qty){
            Redis::HINCRBY($lock_key,$pid,-$qty);
            Redis::HINCRBY($stock_key,$pid,$qty);
        }
    }
    //@TODO,高并发情况，更新库存的同时下单的情况
    public function placeOrder($pid_qty=[]){
        $stock_key = KeyRepository::getKey('productStock');
        $lock_key = KeyRepository::getKey('productStockLock');
        foreach($pid_qty as $pid=>$qty){
            Redis::HINCRBY($lock_key,$pid,$qty);
            Redis::HINCRBY($stock_key,$pid,-$qty);
        }
    }
    public function paidOrder($pid_qty=[]){
        $lock_key = KeyRepository::getKey('productStockLock');
        foreach($pid_qty as $pid=>$qty){
            Redis::HINCRBY($lock_key,$pid,-$qty);
        }
    }
    
    //更新库存的时候，保存到redis
    public function saveStockToRedis($sku_qty=[]){
        $skus = array_keys($sku_qty);
        $map_sku_entity_key = KeyRepository::getKey('productMapSkuEntity');
        $stock_key = KeyRepository::getKey('productStock');
        $lock_key = KeyRepository::getKey('productStockLock');
        foreach ($sku_qty as $sku=>$qty){
            $entity_id = Redis::HGET($map_sku_entity_key,$sku);
            $lock_qty = (int) Redis::HGET($lock_key,$entity_id);
            $saleble_qty = $qty - $lock_qty;//@TODO,负数？
            if($saleble_qty < 0){
                $saleble_qty = 0;
            }
            Redis::HSET($stock_key,$entity_id,$saleble_qty);
        }
    }
    
    //TODO,商品disable 不存在flat，这时更新库存会失败（sku_entity_id映射关系没有找到）
    public function getStockData($product_ids=[]){
        if(!count($product_ids)){
            return [];
        }
        $stock_key = KeyRepository::getKey('productStock');
        $redis_data = Redis::HMGET($stock_key,$product_ids);
        $stock_data = array_combine($product_ids, $redis_data);
        $data = [];
        foreach ($stock_data as $entity_id=>$qty){
            $data[] = ['product_id'=>$entity_id,'qty'=>$qty];
        }
        if(count($data)){
            return $data;
        }
        //db
        $stock_item = DB::table($this->stock_table)
                ->whereIn('product_id',$product_ids)
                ->get();
        $data = [];
        foreach ($stock_item as $item){
            $stock = [];
            foreach($item as $field=>$v){
                $stock[$field] = $v;
            }
            $data[] = $stock;
        }
        return $data;
    }
    
    //@TODO,magento out stock 没有了 
    public function getAllStock($product_ids,$config_relation){
        $stock_item = $this->getStockData($product_ids);
        $stock = [];
        foreach($stock_item as $item){
            $product_id = $item['product_id'];
            $qty = (int) $item['qty'];
//             $status = (int) $item->is_in_stock;
//             if(!$status){
//                 $qty = 0;
//             }
//             $stock[$product_id] = ['status'=>$status,'qty'=>$qty];
            $stock[$product_id] = ['qty'=>$qty];
        }
        //计算config的stock 
        foreach($config_relation as $parent_id=>$item){
//             if(!$stock[$parent_id]['status']){//本来就设置为out stock 
//                 continue;
//             }
            $config_qty = 0;
            $config_status = 0;
            foreach($item as $_item){
                $config_qty +=  $stock[$_item]['qty'];
            }
            if($config_qty){
                $config_status = 1;
            }
            $stock[$parent_id] = ['status'=>$config_status,'qty'=>$config_qty];
        }
        return $stock;
    }
    
    public function getStock($product_ids,$config_id){
        $stock_item = $this->getStockData($product_ids);
        $stock = [];
        $config_total_stock = 0;
        foreach($stock_item as $item){
//             if(!$item->is_in_stock){
//                 continue;
//             }
            $product_id = $item['product_id'];
            $stock[$product_id] = (int) $item['qty'];
            $config_total_stock += (int) $item['qty'];
        }
        if(!isset($stock[$config_id])){
            $config_total_stock = 0;
        }
        return [
          'total'=>$config_total_stock,
          'items'=>$stock,
        ];
    }
    
    public function getSkusStock($product_ids=[]){
        $stock_item = DB::table($this->stock_table)
                    ->whereIn('product_id',$product_ids)
                    ->get();
        $stock_item = $this->getStockData($product_ids);
        $stock = [];
        foreach ($stock_item as $item){
            $product_id = $item['product_id'];
            $stock[$product_id] = (int)$item['qty'];
        }
        return $stock;
    }
}
