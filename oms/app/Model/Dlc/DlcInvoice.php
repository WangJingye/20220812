<?php namespace App\Model\Dlc;

use App\Lib\GuzzleHttp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTimeInterface;

class DlcInvoice extends Model
{
    //指定表名
    protected $table = 'dlc_invoice';
    protected $guarded = ['id'];

    protected $appends = ['content'];

    public function getContentAttribute(){
        return '商品明细';
    }
}
