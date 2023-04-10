<?php

namespace App\Model\Promotion;

use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    //指定表名
    protected $table = 'promotion_cart';
    protected $guarded = [];
    
    
    public function addTable(){
//         $model=$this ->leftJoin('rule_type','rule_type.key','=','promotion_cart.type');
        return $this;
    }
    
    public function addField($model){
        $field=[
            'promotion_cart.id',
            'promotion_cart.name',
            'promotion_cart.priority',
            'rule_type.label as type_label',
            DB::raw("if(`promotion_cart`.`status`=1,'开启','关闭') as status")
        ];
//         $model = $model->select(...$field);
        return $model;
    }
    
    

}
