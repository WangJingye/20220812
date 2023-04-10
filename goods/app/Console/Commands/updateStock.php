<?php

namespace App\Console\Commands;

use App\Model\Goods\Sku;
use App\Model\Goods\Warehose;
use App\Service\Goods\StockService;
use Illuminate\Console\Command;
use App\Model\Goods\ProductHelp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class updateStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updateStock';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $data = Warehose::get()->toarray();

        foreach ($data as $item){
            $sku = Sku::where('sku_id',$item['sku'])->value('id');
            if(!$sku){
                $stock = $item['actual_number']??0;
                $name = $item['goods_name']??'';
                $branch = $item['branch']??'';
                $csv[] = implode(",",[$item['sku'],$name,$branch,$stock]);
            }
        }


        $csv_str = implode("\n",$csv);
        $bom = pack('CCC', 0xef, 0xbb, 0xbf); // 和上面的对应
        $file_name = date('d-h-i').'no_sku_stock.csv';
        file_put_contents($file_name,$bom, FILE_APPEND);
        file_put_contents($file_name,$csv_str, FILE_APPEND);



//        $this->updateStock();
    }
    public function updateStock()
    {
        $redis =  Redis::connection('goods');
        $stock_prefix = 'dlc_stock_key';

        $data = Warehose::where('status',1)->get()->toarray();
        foreach ($data as $v){
            if ($v['actual_number'] > 0) {
                $stock_key = $stock_prefix . $v['sku'];
                $redis->hIncrBy($stock_key, 'stock', -1);//渠道库存
//                $redis->hIncrBy($stock_key, 'lock_channel' . $v['id'], 0);//锁定渠道库存
            }
        }
    }


    //同时更新多个记录，参数，表名，数组（别忘了在一开始use DB;）
    public function updateBatch($tableName = "css_products_info", $multipleData = array()){




    }
}
