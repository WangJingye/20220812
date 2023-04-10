<?php

namespace App\Model\Goods;
use Illuminate\Support\Facades\DB;

class Spec extends Model
{
    //指定表名
    protected $table = 'tb_spec';
    protected $guarded = [];

    static $specMap = null;

    const SPEC_NAME_MAP = [
//        'color'=>'色号',
        'capacity_ml'=>'规格1',
        'capacity_g'=>'规格2',
    ];

    public static function batchGetSpecBySpecTypes($types = []){
        if(!$types) $types = array_keys(self::SPEC_NAME_MAP);
        $str = implode("",$types);
        if(isset($specMap[$str])&& !is_null(self::$specMap[$str])) return self::$specMap[$str];
        $ret = [];
        $spec = empty($types)?Spec::all():Spec::whereIn('spec_type',$types)->get();
        $records = $spec->toArray();
        if(empty($records)) return [];
        foreach($records as $record){
            $ret[$record['spec_type']][$record['spec_code']] = [
                'spec_code'=>$record['spec_code'],
                'spec_unit'=>$record['spec_unit'],
                'spec_property'=>$record['spec_property'],
                'spec_desc'=>$record['spec_desc']
            ];
        }
        return $ret??[];
    }

    public static function insertSpec($spec_code,$spec_type,$spec_property = '',$spec_desc = ''){
        $ins_spec = [
            'spec_code'=>$spec_code,
            'spec_unit'=>($spec_type == 'color')?'':(trim($spec_type,'capacity_')),
            'spec_property'=>$spec_property?:$spec_code,
            'spec_type'=>$spec_type,
            'spec_desc'=>$spec_desc?:$spec_code,
        ];

        Spec::firstOrCreate(
            ['spec_type'=>$spec_type,'spec_code'=>$spec_code],
            $ins_spec
        );
    }

}
