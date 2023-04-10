<?php namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class OmsOrderReturnApply extends Model
{
    //指定表名
    protected $table = 'oms_order_return_apply';
    protected $guarded = ['id'];
    const UPDATED_AT = null;

    protected $appends = ['status_name'];

    const Status_Name = [
        0=>'未审核',
        1=>'同意',
        2=>'拒绝',
    ];
    public function getStatusNameAttribute(){
        return self::Status_Name[$this->status];
    }
}
