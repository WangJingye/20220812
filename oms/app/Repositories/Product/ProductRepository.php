<?php namespace App\Repositories\Product;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Product\Stock\StockRepository;
use App\Exceptions\ApiPlaintextException;
use App\Repositories\Product\Plp\FilterRepository;
use App\Repositories\Product\Plp\SortRepository;
use App\Repositories\Product\Plp\LayerRepository;
use App\Repositories\Product\Redis\KeyRepository;

class ProductRepository
{
    public $product_flat_table = 'catalog_product_flat_1';
    public $product_entity_table = 'catalog_product_entity';
    public $product_super_link_table = 'catalog_product_super_link';
    
    public $category_product_table = 'catalog_category_product';//分类下商品
    public $category_flat_table = 'catalog_category_flat_store_1';
    
    
    public function getProductFlat($product_id){
        $redis_key = KeyRepository::getKey('productFlat');
        $redis_data = Redis::HGET($redis_key,$product_id);
        if($redis_data){
            return json_decode($redis_data,true);
        }
        //db 
        $config_product = DB::table($this->product_flat_table)
                ->where('entity_id',$product_id)
                ->get();
        if($config_product->count()){
            $config_product = $config_product->first();
        }else{
            throw new ApiPlaintextException("pid not exists");
        }
        $data = [];
        foreach ($config_product as $key=>$v){
            $data[$key] = $v;
        }
        return $data;
    }
    
    public function getSuperLink($product_id){
        $redis_key = KeyRepository::getKey('productSuperLink');
        $redis_data = Redis::HGET($redis_key,$product_id);
        if($redis_data){
            $data = json_decode($redis_data,true);
            return $data[$product_id];
        }
        //db
        $config_product_child_ids = DB::table($this->product_super_link_table)
                    ->select('product_id','parent_id')
                    ->where('parent_id',$product_id)
                    ->get();
        $config_product_child_ids_arr = [];
        foreach($config_product_child_ids as $item){
            $config_product_child_ids_arr[] = $item->product_id;
        }
        return $config_product_child_ids_arr;
    }
    
    public function getProductFlatByPids($pids=[]){
        $redis_key = KeyRepository::getKey('productFlat');
        $redis_data = Redis::HMGET($redis_key,$pids);
        if($redis_data){
            $data = [];
            foreach($redis_data as $json){
                if(!$json){
                    continue;
                }
                $data[] = json_decode($json,true);
            }
            if(count($data)){
                return $data;
            }
        }
        //db
        $child_product = DB::table($this->product_flat_table)
                    ->whereIn('entity_id',$pids)
                    ->get();
        $data = [];
        foreach ($child_product as $item){
            $product = [];
            foreach($item as $field=>$v){
                $product[$field] = $v;
            }
            $data[] = $product;
        }
        return $data;
    }
    
    /**
     * 获取pdp商品详情  前端需要的数据
     * @param unknown $product_id
     * @param unknown $color_id
     */
    public function getPdp($product_id,$input_color_id){
        $config_product = $this->getProductFlat($product_id);
        $config_product_child_ids_arr = [];
        $config_product_child_ids_arr = $this->getSuperLink($product_id);
        $stock_pids = $config_product_child_ids_arr;
        $stock_pids[] = $product_id;
        $stock_data = (new StockRepository())->getStock($stock_pids,$product_id);
        $child_product = $this->getProductFlatByPids($config_product_child_ids_arr);
        //商品数据根据 color 分组
        $product_data = [];
        foreach($child_product as $item){
            $color_id = $item['color'];
            $product_data[$color_id][] = $item;
        }
        //组装数据
        $colors = [];//相同款号的不同颜色
        $sizes = [];//相同颜色的不同尺码
        $curr_color_item = false;
        foreach($product_data as $color_id=>$color_item){
            $color_qty = 0;
            foreach($color_item as $item){
                $product_id = $item['entity_id'];
                $child_qty = $stock_data['items'][$product_id]??0;
                $color_qty += $child_qty;
                if($color_id == $input_color_id){//当前color下的sizes
                    $sizes[] = [
                        'id'=>$item['sku'],//simple product sku 
                        'name'=>$item['size_value'],
                        'stock'=>$child_qty,
                    ];
                    if(!$curr_color_item){
                        $curr_color_item = $item;
                    }
                }
            }
            $color_name = $item['color_name']?$item['color_name']:$item['color_value'];
            $colors[] = [
                'id'=>$color_id,
                'name'=>$color_name,
                'value'=>'',
                'style'=>$this->getStyle($item['site_sku']),//货号, 取site_sku的前面两部分 D5K2Y-101-4
                'cover'=>$this->getCovor($item['kv']),//每个sku都保存color pic , 主图
                'stock'=>$color_qty,//这个颜色下所有尺码的库存
            ];
            
        }//end foreach 
        $images = $this->getkv($curr_color_item['kv']);
        $category = [
            'id'=>$config_product['ot_product_type'],
            'name'=>$config_product['ot_product_type_value'],
        ];
        $gender = [
            'id'=>$config_product['gender'],
            'name'=>$config_product['gender_value'],
        ];
        $data = [
            'id'=>$config_product['entity_id'],
            'name'=>$config_product['name'],
            'price'=>$config_product['price'],
            'images'=>$images,
            'color'=>$input_color_id,//当前颜色
            'colors'=>$colors,
            'category'=>$category,
            'gender'=>$gender,
            'sizes'=>$sizes,
            'detail'=>$config_product['description'],
        ];
        return $data;
    }
    public function getkv($kv){
        $kv_json = json_decode($kv,true);
        $return = $kv_json['kv']??[];
        $data = [];
        foreach($return as $item){
            $data[] = str_replace('-','_',$item);
        }
        return $data;
    }
    public function getCovor($kv){//主图
        $kv_json = json_decode($kv,true);
        $return = $kv_json['cover']??'';
        return str_replace('-','_',$return);
    }
    public function getHoverCovor($kv){//主图
        $kv_json = json_decode($kv,true);
        $return = $kv_json['hoverColor']??'';
        return str_replace('-','_',$return);
    }
    public function getStyle($site_sku){
        if(!$site_sku){
            return '';
        }
        $arr = explode('-',$site_sku);
        $part1 = $arr[0]??'';
        $part2 = $arr[1]??'';
        return $part1.'-'.$part2;
    }
    
    public function getCatPids($cid){
        $cid = (int) $cid;
        $redis_key = KeyRepository::getKey('productCategoryProduct');
        $redis_data = Redis::HGET($redis_key,$cid);
        if($redis_data){
            return json_decode($redis_data,true);
        }
        //db
        $products = DB::table($this->category_product_table)
                    ->join($this->product_entity_table,$this->category_product_table . '.product_id','=',$this->product_entity_table . '.entity_id')
                    ->where('type_id','configurable')
                    ->where('category_id',$cid)
                    ->select($this->category_product_table .'.*')
                    ->orderBy('position','desc')
                    ->get();
        $product_ids = [];
        foreach($products as $item){
            $product_ids[] = $item->product_id;
        }
        return $product_ids;
    }
    
    public function getSuperLinkByPids($pids=[]){
        $redis_key = KeyRepository::getKey('productSuperLink');
        $redis_data = Redis::HMGET($redis_key,$pids);
        if($redis_data){
            $config_product_child_ids_arr = [];
            $config_relation = [];
            foreach ($redis_data as $json){
                if(!$json){
                    continue;
                }
                $json = json_decode($json,true);
                foreach ($json as $parent_id =>$childs){
                    foreach ($childs as $item){
                        $config_product_child_ids_arr[] = $item;
                    }
                    $config_relation[$parent_id] = $childs;
                }
            }
            if(count($config_product_child_ids_arr)){
                return [$config_product_child_ids_arr,$config_relation];
            }
        }
        //db
        $product_ids = $pids;
        $config_product_child_ids = DB::table($this->product_super_link_table)
                    ->select('product_id','parent_id')
                    ->whereIn('parent_id',$pids)
                    ->get();
        $config_product_child_ids_arr = [];
        $config_relation = [];
        foreach($config_product_child_ids as $item){
            $config_product_child_ids_arr[] = $item->product_id;
            $parent_id = $item->parent_id;
            $config_relation[$parent_id][] = $item->product_id;
        }
        return [$config_product_child_ids_arr,$config_relation];
    }
    
    public function getPlp($pids,$where=[],$sort='',$limit=24,$curr_page=1){
        $product_ids = $pids;
        $super_link = $pids?$this->getSuperLinkByPids($pids):[[],[]];
        $config_product_child_ids_arr = $super_link[0];
        $config_relation = $super_link[1];
        $stock_pids = array_merge($config_product_child_ids_arr,$product_ids);
        $stock_data = (new StockRepository())->getAllStock($stock_pids,$config_relation);
        $child_product = $config_product_child_ids_arr?($this->getProductFlatByPids($config_product_child_ids_arr)):[];
        $child_products = [];
        foreach($child_product as $item){
            $product_id = $item['entity_id'];
            $child_products[$product_id] = $item;
        }
        //商品数据根据config, color 分组
        $product_data = [];
        foreach($config_relation as $parent_id=>$items){
            foreach($items as $_pid){
                if(!isset($child_products[$_pid])){
                    continue;
                }
                $child_product = $child_products[$_pid];
                $color_id = $child_product['color'];
                $product_data[$parent_id][$color_id][] = $child_product;
            }
        }
        //filter 
        $product_data = (new FilterRepository())->filter($product_data,$stock_data,$where);
        //layer
        $layer_data = (new LayerRepository())->layer($product_data);
        //sort 
        $product_data_sort = [];
        if(count($product_data)){
            $product_data_sort = (new SortRepository())->sort($product_data,$child_products,$sort);
        }
        //page,limit,offset 
        $total_count = count($product_data_sort);
        $offset_start = (($curr_page-1) * $limit) +1;
        $offset_end = $curr_page * $limit;
        //config 商品数据，包括多个颜色
        $list = [];
        $k=1;
        foreach($product_data_sort as $product_data){//config
            if($k < $offset_start or $k > $offset_end){
                $k++;
                continue;
            }
            $k++;
            foreach($product_data as $parent_id=>$color_item){//颜色
                $config_min_price = PHP_INT_MAX;
                //每个颜色的商品数据，包括多个尺码
                $colors = [];
                foreach($color_item as $color_id=>$item){
                    $sizes = [];
                    $color_stock = 0;
                    foreach($item as $_item){//计算这个颜色下的所有库存
                        $_pid = $_item['entity_id'];
                        $color_stock += $stock_data[$_pid]['qty'];
                        $sizes[] = [
                            'id'=>$_pid,
                            'name'=>$_item['size_value'],
                            'stock'=>$stock_data[$_pid]['qty'],
                        ];
                        if($_item['price'] < $config_min_price){
                            $config_min_price = $_item['price'];
                        }
                    }
                    $colors[] = [
                        'id'=>$color_id,
                        'name'=>$_item['color_value'],
                        'cover'=>$this->getCovor($_item['kv']),//主图
                        'stock'=>$color_stock,//这个颜色下的所有库存
                        'sizes'=>$sizes,//这个颜色下的所有尺码
                    ];
                }
                $category = [
                    'id'=>$_item['ot_product_type'],//商品类型
                    'name'=>$_item['ot_product_type_value'],
                ];
                $gender = [
                    'id'=>$_item['gender'],
                    'name'=>$_item['gender_value'],
                ];
                $list[] = [
                    'id'=>$parent_id,
                    'name'=>$_item['name'],
                    'cover'=>$this->getCovor($_item['kv']),//封面图
                    'price'=>$_item['price'],
                    'minPrice'=>$config_min_price,
                    'hoverCover'=>$this->getHoverCovor($_item['kv']),//鼠标悬浮图
                    'colors'=>$colors,
                    'category'=>$category,
                    'gender'=>$gender,
                    //                 'sizes'=>$sizes,
                    'detail'=>$_item['description'],
                ];
            }//end foreach 
        }//end foreach

        $out_genders = [];
        $out_colors = [];
        $out_sizes = [];
        foreach($layer_data['genders'] as $gender_id=>$gender_name){
            $out_genders[] = [
                'id'=>$gender_id,
                'name'=>$gender_name,
            ];
        }
        foreach($layer_data['colors'] as $color_id=>$color_value){
            $out_colors[] = [
                'id'=>$color_id,
                'value'=>$color_value,
            ];
        }
        foreach($layer_data['sizes'] as $size_id=>$size_value){
            $out_sizes[] = [
                'id'=>$size_id,
                'name'=>$size_value,
            ];
        }
        $out_prices = $layer_data['price'];
        
        $data = [
            'list'=>$list,
            'genders'=>$out_genders,
            'colors'=>$out_colors,
            'sizes'=>$out_sizes,
            'prices'=>$out_prices,
            'total'=>$total_count,
            'limit'=>$limit,
            'offset'=>'',
        ];
        return $data;
    }
    
    
    public function getProductInfo($product_id){
        $config_product = DB::table($this->product_flat_table)
                        ->where('entity_id',$product_id)
                        ->get();
        if($config_product->count()){
            $config_product = $config_product->first();
        }else{
            throw new ApiPlaintextException("pid not exists");
        }
        $config_product_child_ids = DB::table($this->product_super_link_table)
                                    ->select('product_id','parent_id')
                                    ->where('parent_id',$product_id)
                                    ->get();
        $config_product_child_ids_arr = [];
        foreach($config_product_child_ids as $item){
            $config_product_child_ids_arr[] = $item->product_id;
        }
        $stock_pids = $config_product_child_ids_arr;
        $stock_pids[] = $product_id;
        $stock_data = (new StockRepository())->getStock($stock_pids,$product_id);
        $child_product = DB::table($this->product_flat_table)
                        ->whereIn('entity_id',$config_product_child_ids_arr)
                        ->get();
        return ['products'=>$child_product,'stock'=>$stock_data];
    }
    //在售商品
    public function getAllOnSalesProducts($page_no,$page_size){
        $skip = ($page_no-1) * $page_size;
        $products = DB::table($this->product_flat_table)
                        ->select('entity_id','name','price')
                        ->where('visibility',4)
                        ->skip($skip)
                        ->take($page_size)
                        ->get();
        $total_count = DB::table($this->product_flat_table)
                    ->select('entity_id')
                    ->where('visibility',4)
                    ->count();
        return ['total'=>$total_count,'data'=>$products];
    }
    //所有商品
    public function getAllProducts($page_no,$page_size){
        $skip = ($page_no-1) * $page_size;
        $products = DB::table($this->product_entity_table)
                        ->select('entity_id')
                        ->where('type_id','configurable')//需要加索引
                        ->skip($skip)
                        ->take($page_size)
                        ->get();
        $total_count = DB::table($this->product_entity_table)
                        ->select('entity_id')
                        ->count();
        return ['total'=>$total_count,'data'=>$products];
        return $products;
    }
}








