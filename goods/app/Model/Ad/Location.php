<?php

namespace App\Model\Ad;
use Illuminate\Support\Facades\DB;

class Location extends Model
{
    //指定表名
    protected $table = 'tb_ad_location';
    protected $guarded = [];
    const UPDATED_AT = null;
    const CREATED_AT = null;
    public static $fields = [
        'status',
        'start_time',
        'end_time',
        'userid',
        'remark'
    ];

    public static function updateById($id,$data){
        $upSum = Location::where('id',$id)->update($data);
        return $upSum;
    }

}
