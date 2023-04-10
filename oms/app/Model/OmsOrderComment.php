<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class OmsOrderComment extends Model
{
    use \jdavidbakr\ReplaceableModel\ReplaceableModel;
    //指定表名
    protected $table = 'oms_order_comment';
    protected $primaryKey = 'order_sn';
    protected $guarded = [];
    const UPDATED_AT = null;

}
