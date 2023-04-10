<?php

namespace App\Console\Commands;

use App\Service\Goods\StockService;
use App\Model\Goods\ProductHelp;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Model\Goods\PushStockLog;
use App\Model\Goods\Sku;
use App\Model\Goods\Warehose;

use Illuminate\Support\Facades\DB;

class LoadFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:sku';

    protected $assign_ratio = '1:2:7';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {


        setlocale(LC_ALL, 'zh_CN');

        $excel_file_path = 'C:\Users\Dora.Chang\Desktop\Style到货反馈模板1015.csv';

        $content = file_get_contents($excel_file_path);


        $encode = mb_detect_encoding($content, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
        $content = mb_convert_encoding($content, 'UTF-8', $encode);
        $content_data = explode("\r\n", $content);

        unset($content_data[0]);
        $row = [];
        foreach ($content_data as $v) {
            $array = explode(',', $v);
            if (!empty($array) && count($array) > 1) {
                $row[] = $array;
            }
        }

        $arrived_at =$row[0][1];

        unset($row[0],$row[1]);
        foreach($row as $k=>$v){
            if(!$v[4]){
                $v[4] = 0;
            }
            if(!$v[6]){
                $v[6] = 0;
            }

            $diff_number = $v[2]-$v[4];
            $a =  [
                'is_auto'=>0,
                'sku'=>$v[0],
                'goods_name'=>$v[1],
                'branch'=>$v[3],
                'ready_number'=>$v[2],
                'actual_number'=>$v[4],
                'diff_number'=>$diff_number,
                'remark'=>$v[8],
                'warehose_at'=>$arrived_at
            ];
            Warehose::create(
                $a
            );
        }
    }


}




