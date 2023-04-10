<?php

namespace App\Console\Commands;

use App\Model\Goods\Oms;
use App\Model\Goods\Sku;
use App\Model\Goods\OmsOrderItem;
use App\Model\Goods\Spu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Service\Goods\StockService;

class ExportStock extends Command
{

    protected $signature = 'exportStock';

    protected $description = 'exportStock';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $goods_name = Spu::pluck('product_name', 'id');
        $skus_arr = Sku::select('sku_id','product_idx')->get()->toArray();
        $skus = [];
        $goods = [];
        foreach ($skus_arr as $item) {
            $skus[] = $item['sku_id'];
            $goods[$item['sku_id']]=$item['product_idx'];
        }
        $stocks = StockService::getStockAll($skus);
        $stock_arr = $stocks[2];
        $csv[] ="sku,商品名,库存数量,安全库存,备注";
        foreach ($stock_arr as $sku => $item) {
            if(empty($item)){
                $secure = $item['secure'] ?? 0;
                if(isset($goods[$sku]) && isset($goods_name[$goods[$sku]])){

                    $name = $goods_name[$goods[$sku]];
                }else{
                    $name = '';
                }
                $csv[] = implode(",", [$sku,$name, 0, $secure,'']);
              continue;
            }
            if($item['is_share'] == 1){
                $stock = $item['stock'] ?? 0;
            }else{
                $stock = $item['channel1'] +$item['channel2']+$item['channel3'];
            }

            $secure = $item['secure'] ?? 0;
            if(isset($goods[$sku]) && isset($goods_name[$goods[$sku]])){

                $name = $goods_name[$goods[$sku]];
            }else{
                $name = '';
            }
            $csv[] = implode(",", [$sku,$name, $stock, $secure,'']);
        }
        $data = Oms::select('id','order_state')->whereIn('order_state',[1,5,7])->where('id','>=','200114')->get();
        $desc = '';
        foreach($data as $v){
            $items = OmsOrderItem::select('sku','collections','name')->where('order_main_id',$v->id)->get()->toarray();
            foreach($items as $item){
                if($v['order_state'] == 1){
                    $desc = '未支付';
                }
                if($v['order_state'] == 5){
                    $desc = '待审核';
                }
                if($v['order_state'] == 7){
                    $desc = '已审核等待仓库待发货';
                }

                if ((empty($v['collections']) || $v['collections'] == '[]')) {

                    $csv[] = implode(",", [$item['sku'],$item['name'], 1, 0,$desc]);
                }
                if ($v['type']==3) {
                    $csv[] = implode(",", [$item['sku'],$item['name'], 1, 0,$desc]);
                }

            }
        }

        $csv_str = implode("\n", $csv);
        $bom = pack('CCC', 0xef, 0xbb, 0xbf); // 和上面的对应
        $file_name = date('Ymd').'stock.csv';
        file_put_contents($file_name,$bom, FILE_APPEND);
        file_put_contents($file_name,$csv_str, FILE_APPEND);

    }


    public function getStock()
    {
        {
            $skus_arr = Sku::select('sku_id')->get()->toArray();
            $skus = [];
            foreach ($skus_arr as $item) {
                $skus[] = $item['sku_id'];
            }
            $stocks = StockService::getStockAll($skus);
            $stock_arr = $stocks[2];
            $csv = [];
            foreach ($stock_arr as $sku => $item) {
                $stock = $item['stock'] ?? 0;
                $secure = $item['secure'] ?? 0;
                $csv[] = implode(",", [$sku, $stock, $secure]);
            }


            $csv_str = implode("\n", $csv);
            file_put_contents(date('dhi') . 'export_stock.csv', $csv_str);
        }
    }

}
