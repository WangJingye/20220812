<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;

class Recommend extends Model
{
    //指定表名
    protected $table = 'tb_recommend';
    protected $guarded = [];

    public static $fields = [
        'flag',
        'cat_id',
        'rec_desc',
    ];

    static $specMap = null;

    public static function updateById($id,$upData){
        $upSum = self::where('id',$id)->update($upData);
        return $upSum;
    }


}
