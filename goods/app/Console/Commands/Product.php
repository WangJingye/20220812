<?php

namespace App\Console\Commands;

use App\Model\Goods\Category;
use App\Model\Goods\Collection;
use App\Model\Goods\Pim;
use App\Model\Goods\ProductCat;
use App\Model\Goods\Sku;
use App\Model\Goods\Spec;
use App\Model\Goods\Spu;
use App\Model\Search\SearchES;
use App\Service\Goods\AdService;
use App\Service\Goods\ProductService;
use App\Service\Goods\SearchService;
use Illuminate\Console\Command;
use App\Model\Goods\ProductHelp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Lib\Oss;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:shell {method}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'product:shell';

//    static $allowMethod = [
//        'cacheAllProduct',
//        'cacheAllAds',
//    ];
    private $logger;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($logger=1)
    {
        parent::__construct();
        $this->logger = $logger;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $method = $this->argument('method');
        if(!$method) return false;
        if(method_exists($this,$method)){
            $this->$method();
        }
    }

    public function fiveMinute(){
        $this->cacheAllProduct();
        $this->cacheCatProductIdList();
        $this->cacheAllSkuSpuMap();
        $this->cacheAllLocAds();
        $this->cacheAllBlackList();
        $this->cacheAllSynonym();
        $this->cacheAllCats();
        $this->cacheProductSort();
    }

    public function cacheCatProductIdList(){
        $this->logger("cache  cat product ids list start");
        $pService = new ProductService();
        $ret = $pService->cacheCatProductIdList();
        $this->logger('cat product ids list'.json_encode($ret));
        $this->logger("cache  cat product ids list end");
    }

    public function cacheAllCat(){
        $this->logger("cache  cats  start");
        $pService = new ProductService();
        $ret = $pService->cacheAllCatInfo();
        $this->logger('cats:'.json_encode($ret));
        $this->logger("cache  cats end");
    }

    public function cacheAllSkuSpuMap(){
        $this->logger("cache  all sku spu map start");
        $pService = new ProductService();
        $ret = $pService->cacheAllSkuSpuMap();
        $pService->cacheAllSpuCodeMap();
        $this->logger('all sku spu map:'.json_encode($ret));
        $this->logger("cache  all sku spu map end");
    }

    public function cacheAllCollection(){
        $this->logger("cache  all Collection start");
        $pService = new ProductService();
        $pre_id = 0;
        while(true){
            $products = [];
            $skuModel = new Collection();
            $records = $skuModel->where('id','>',$pre_id)
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach($records as $record){
                $pre_id = max($pre_id,$record['id']);
                $product = $pService->cacheCollectionInfoById($record['id']);
                if($product) $products[] = $product;
            }
            $this->logger('all Collection:'.json_encode($products));
            \App\Model\Search\Product::updateProductsToES($products);
        }
        $this->logger("cache  all Collection end");
    }

    public function cacheAllLocAds(){
        $this->logger("cache  all ads start");
        $adService = new AdService();
        $ret = $adService->cacheAllLocAds();
        $this->logger('all ads:'.json_encode($ret));
        $this->logger("cache  all ads end");
    }

    public function cacheAllProduct(){
        $this->logger("cache  all Product start");
        $pService = new ProductService();
        $pre_id = 0;
        //获取所有指定产品的促销信息
        $promotionAddSku = self::getAllPromotionAddSku();
        while(true){
            $products = [];
            $spu = new Spu();
            $records = $spu->where('id','>',$pre_id)->where('status','!=',3)
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach($records as $record){
                //加入促销信息
                $addition['promotion_info'] = array_get($promotionAddSku,$record['id']);
                $pre_id = max($record['id'],$pre_id);
                $product = $pService->cacheProductInfoById($record['id'],$addition);
                if($product) $products[] = $product;
            }
            \App\Model\Search\Product::updateProductsToES($products);
        }
        $this->logger("cache  all Product end");

    }

    /**
     * 分类缓存用于dlc项目
     */
    public function cacheAllCats(){
        $this->logger("cache  all cats start");
        $index = $children = $brother = $status = $event = $detail = [];
        $index_key = config('app.name').':cats:index';
        $children_key = config('app.name').':cats:children';
        $brother_key = config('app.name').':cats:brother';
        $status_key = config('app.name').':cats:status';
        $event_key = config('app.name').':cats:event';
        $detail_key = config('app.name').':cats:detail';
        $result = Category::query()->orderBy('sort','desc')->get();
        if($result->count()){
            foreach($result as $item){
                $index[$item['id']] = $item['cat_name'];
                $children[$item['parent_cat_id']][] = $item['id'];
                $status[$item['id']] = $item['status'];
                if($item['cat_type']==2){
                    $event[$item['id']] = $item['cat_name'];
                }
                $detail[$item['id']] = json_encode($item);
            }
            foreach($children as $k=>$v){
                foreach($v as $child){
                    $brother[$child] = json_encode($v);
                }
                $children[$k] = json_encode($v);
            }
            $redis = ProductService::getRedis();
            $redis->hmset($index_key,$index);
            $redis->hmset($children_key,$children);
            $redis->hmset($brother_key,$brother);
            $redis->hmset($status_key,$status);
            if($event){
                $redis->hmset($event_key,$event);
            }
            $redis->hmset($detail_key,$detail);
            $this->logger("cache  all cats saved");
        }
        $this->logger("cache  all cats end");
    }

    public function cacheAllBlackList(){
        $res = SearchService::cacheAllBlackList();
        $this->logger(json_encode($res));
    }

    public function cacheAllSynonym(){
        $res = SearchService::cacheAllSynonym();
        $this->logger(json_encode($res));
    }

    public function cacheAllRedirect(){
        $res = SearchService::cacheAllRedirect();
        $this->logger(json_encode($res));
    }

    public function cacheAllRec(){
        $pService = new ProductService;
        $rec_names = ProductService::$recommendConfigDBName;
        foreach($rec_names as $rec_name){
            $pService->cacheRecInfoByFlag($rec_name);
        }
    }

    /**
     * 排序spuId,用于为您推荐
     */
    public function cacheProductSort(){
        $limit = 6;
        $asc_key = config('app.name').':recommend:sort_asc';
        $desc_key = config('app.name').':recommend:sort_desc';
        $ids_asc = Spu::query()->where('status',1)->where('can_search',1)
            ->orderBy('sort','asc')
            ->orderBy('sort','asc')
            ->limit($limit)->pluck('id')->toArray();
        $ids_desc = Spu::query()->where('status',1)->where('can_search',1)
            ->orderBy('sort','desc')
            ->orderBy('id','desc')
            ->limit($limit)->pluck('id')->toArray();
        $redis = ProductService::getRedis();
        if($ids_asc){
            $ids_asc = array_reduce($ids_asc,function($result,$item){
                $result[] = ['id'=>$item,'product_type'=>1];
                return $result;
            },[]);
            $redis->set($asc_key,json_encode($ids_asc));
        }
        if($ids_desc){
            $ids_desc = array_reduce($ids_desc,function($result,$item){
                $result[] = ['id'=>$item,'product_type'=>1];
                return $result;
            },[]);
            $redis->set($desc_key,json_encode($ids_desc));
        }
        $this->logger('cacheProductSort done');
    }

    //配合SEO 需要使用spu，不能使用自增ID了 为了改动最小，维护一个map
    public function cacheProductIdMap(){
        $pre_id = 0;
        $all_ids = [];
        $redis = ProductService::getRedis();

        $this->logger("开始缓存product id map");
        //商品的
        while(true){
            $spu = new Spu();
            $records = $spu->where('id','>',$pre_id)->where('status',1)
                ->limit(100)->orderBy('id','asc')->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach($records as $record){
                $pre_id = max($record['id'],$pre_id);
                $redis->hset(config('app.name').':product:id:map',$record['product_id'],$record['id']);
            }
        }
        $this->logger("结束缓存product id map");

        $pre_id = 0;
        $this->logger("开始缓存colle id map");
        //套装的
        while(true){
            $spu = new Collection();
            $records = $spu->where('id','>',$pre_id)->where('status',1)->orderBy('id','asc')
                ->limit(100)->get();
            if($records->isEmpty()) break;
            $records = $records->toArray();
            foreach($records as $record){
                $pre_id = max($record['id'],$pre_id);
                $redis->hset(config('app.name').':product:id:map',$record['colle_id'],$record['id'].'-2');
            }
        }
        $this->logger("结束缓存colle id map");

        return $all_ids;
    }

    public static function log($str){
        echo date('[Y-m-d H:i:s] ').$str.PHP_EOL;
    }

    public function logger($str){
        if($this->logger){
            echo date('[Y-m-d H:i:s] ').$str.PHP_EOL;
        }
    }

    public static function getAllPromotionAddSku(){
        $promotions = app('ApiRequestInner')->request('promotion/getAllByAddSku','POST');
        if($promotions['code']==1&&!empty($promotions['data'])){
            $data = [];
            foreach($promotions['data'] as $item){
                if($item['add_sku']){
                    $ids = explode(',',$item['add_sku']);
                    foreach($ids as $id){
                        $data[$id][] = $item['name'];
                    }
                }
            }
            if($data){
                foreach($data as $k=>$v){
                    $data[$k] = implode(';',$v);
                }
            }
        }return $data??[];
    }
}
