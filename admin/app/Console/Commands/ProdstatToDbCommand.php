<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\ProdstatToDbController;

class ProdstatToDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prodstat:todb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将Redis里面存储的搜索、产品浏览等排行从Redis中写入DB';

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
            $prod = new ProdstatToDbController();
            try {
                $prod->AddCartToDB();
//                $prod->ProdFavoriteToDB();
                $prod->ProdShareToDB();
//                $prod->ProdViewByProdTypeToDB();
                $prod->catViewToDB();
                $prod->ProdViewToDB();
                $prod->SearchKeywordToDB();
            } catch (\Exception $exception) {
                echo $exception;
            }
    }
}
