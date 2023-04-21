<?php

namespace App\Model\Goods;
use App\Model\Common\YouShu;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Gold extends Model
{
    //指定表名
    protected $table = 'tb_gold';
    protected $guarded = [];

}
