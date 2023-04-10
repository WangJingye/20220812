<?php namespace App\Repositories\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Product\Stock\StockRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\Redis\KeyRepository;

//订单/购物车模块调用接口
class InnerProductRepository extends ProductRepository
{
    
    public function getChildsBySkus($skus=[]){
        $redis_key = KeyRepository::getKey('productMapSkuEntity');
        $redis_data = Redis::HMGET($redis_key,$skus);
        $pids = [];
        foreach ($redis_data as $item){
            $pids[] = $item;
        }
        $data = $this->getProductFlatByPids($pids);
        if(count($data)){
            return $data;
        }
        //db 
        $child_product = DB::table($this->product_flat_table)
                    ->whereIn('sku',$skus)
                    ->get();
        $data = [];
        foreach($child_product as $item){
            $product = [];
            foreach($item as $k=>$v){
                $product[$k] = $v;
            }
            $data[] = $product;
        }
        return $data;
    }
    
    public function getProductBelongToConfig($pids){
        $redis_key = KeyRepository::getKey('productBelongtoConfig');
        $redis_data = Redis::HMGET($redis_key,$pids);
        $data = [];
        foreach ($redis_data as $item){
            if(!$item){
                continue;
            }
            $json = json_decode($item,true);
            $product_id = $json[1];
            $parent_id = $json[0];
            $data[$product_id] = $parent_id; 
        }
        if(count($data)){
            return $data;
        }
        //db
        $super_link = DB::table($this->product_super_link_table)
                ->select('product_id','parent_id')
                ->whereIn('product_id',$pids)
                ->get();
        $super_link_data = [];
        foreach ($super_link as $item){
            $sku_id = $item->product_id;
            $parent_id = $item->parent_id;
            $super_link_data[$sku_id] = $parent_id;
        }
        return $super_link_data;
    }
    
    public function getSkus($skus=[]){
        $child_product = $this->getChildsBySkus($skus);
        $child_pids = [];
        foreach ($child_product as $item){
            $child_pids[] = $item['entity_id'];
        }
        $super_link_data = $this->getProductBelongToConfig($child_pids);
        $stock_data = (new StockRepository())->getSkusStock($child_pids);
        $data = [];
        foreach ($child_product as $item){
            $sku_id = $item['entity_id'];
            $data[] = [
                'entity_id'=>$sku_id,
                'name'=>$item['name'],
                'sku'=>$item['sku'],
                'price'=>$item['price'],
                'color'=>$item['color'],
                'color_value'=>$item['color_value'],
                'size'=>$item['size'],
                'size_value'=>$item['size_value'],
                'image'=>$this->getCovor($item['kv']),
                'kv'=>$this->getkv($item['kv']),
                'gender'=>$item['gender'],
                'gender_value'=>$item['gender_value'],
                'stock'=>$stock_data[$sku_id],
                'parent_id'=>$super_link_data[$sku_id],
                'ot_product_type_value'=>$item['ot_product_type_value'],
            ];
        }
        return $data;
    }
    
    //保存sku销量, 只调用一次
    //['$sku_entity_id'=>$qty,]
    //@TODO,脚本一次性把所有的商品销量重置，或者同时保存订单id防止重复
    public function saveSkuSales($pid_qty=[]){
        $child_pids = array_keys($pid_qty);
        $super_link_data = $this->getProductBelongToConfig($child_pids);
        $redis_key = KeyRepository::getKey('productSales');
        foreach($pid_qty as $pid=>$qty){
            $config_id = $super_link_data[$pid];
            Redis::HINCRBY($redis_key,$config_id,$qty);
        }
        return true;
    }
    
    
    public function cancelOrderUpdateStock($pid_qty=[]){
        (new StockRepository())->cancelOrder($pid_qty);
        return true;
    }
    public function placeOrderUpdateStock($pid_qty=[]){
        (new StockRepository())->placeOrder($pid_qty);
        return true;
    }
    public function paidOrderUpdateStock($pid_qty=[]){
        (new StockRepository())->paidOrder($pid_qty);
        return true;
    }
}








