<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;

class Config extends Model
{
    //指定表名
    protected $table = 'config';
    protected $guarded = [];
    //链接外部数据库
    protected $connection = 'mysql_admin';

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

    public static function getConfigByName($name){
        $cfg = self::where('config_name',$name)->first();
        if(!$cfg) return [];
        $cfg['extension'] = json_decode($cfg['extension'],true);
        return object2Array($cfg);
    }

}
