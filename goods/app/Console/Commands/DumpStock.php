<?php

namespace App\Console\Commands;

use App\Model\Goods\Sku;
use App\Model\Goods\Warehose;
use App\Service\Goods\StockService;
use Illuminate\Console\Command;
use App\Model\Goods\ProductHelp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class DumpStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dumpStock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dumpStock';

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
        $data = Warehose::where('status', 0)->get()->toarray();
        foreach ($data as $v) {
            $exist = Sku::where('sku_id',$v['sku'])->value('id');
            if($exist){
                if ($v['actual_number'] > 0) {
                    StockService::assignStocks($v['sku'], $v['actual_number'], $v['branch']);
                }

                Warehose::select('id', $v['id'])->update([
                    'status' => 1
                ]);
            }

        }
    }

}
