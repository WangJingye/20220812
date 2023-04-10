<?php namespace App\Jobs;

use App\Model\Goods\Sku;
use App\Model\Dlc\DlcOmsSyncLog;
use App\Service\Goods\{ProductService,StockService};
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class ProductQueued implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $method;
    protected $params;

    /**
     * Create a new job instance.
     * @param $method
     * @param $params
     */
    public function __construct($method,$params){
        $this->method = $method;
        $this->params = $params;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle(){
        $res = call_user_func([$this,$this->method],$this->params);
        echo $res;
        Log::info('ProductQueued',[
            'method'=>$this->method,
            'params'=>$this->params,
            'res'=>$res,
        ]);
    }

    /**
     * 失败队列处理(已达到最大尝试次数)
     */
    public function failed(){
        //
    }

    /**
     * 根据sku刷新商品缓存
     * @param $params
     * @return mixed|string
     */
    public function cacheProductInfoBySkuIds($params){
        $result = call_user_func(function() use($params){
            $spuIds = Sku::query()->whereIn('sku_id',$params['skuIds'])->pluck('product_idx');
            $pService = new ProductService;
            foreach($spuIds as $spuId){
                $pService->cacheProductInfoById($spuId);
            }
        });
        return $result===true?'执行成功':$result;
    }


}
