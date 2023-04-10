<?php
namespace App\Service\Condition;
use App\Service\Rule\CrossRuleCheckTrait;

class Condition
{
    public $data = [];
    use CrossRuleCheckTrait;

    public function check($rule, $item){
        $condition = $this->conditionCheck($rule, $item);
        if($condition == false){
            return false;
        }
        if($this->allCrossRuleCheck($rule,$item) == false){
            return false;
        }
        return true;
    }
    public function allCrossRuleCheck($rule,$item){
        $type_arr = ['product_discount','cut','coupon','gift','freetry','code'];
        foreach($type_arr as $type){
            $check = $this->crossRuleCheck($rule, $item,$type);
            if(!$check){
                return false;
            }
        }
        return true;
    }
    public function setData($data){
        $this->data = $data;
    }
    
    public function conditionCheck($rule,$item){
        if(strlen(trim($item['mid'])) < 1){
            $item['mid'] = 'no_mid';//判断如果没有分类，就硬编码一个不存在的
        }
        $item_cids = explode(',',$item['mid']);
        $item_style_number = $item['styleNumber'];
        $cids = explode(',',$rule['cids']);
        $excludes_style_number = explode(',',$rule['exclude_sku']);
        $includes_style_number = explode(',',$rule['add_sku']);
        if(isset($item['applied_shipfeetry']) and $item['applied_shipfeetry']){//付邮试用不参与任何促销
            return false;
        }
        if($item['usedPoint']){
            return false;
        }
        if(in_array($item_style_number, $excludes_style_number)){
            return false;
        }
        //增加全场
        if(array_get($rule,'is_whole')==1){
            return true;
        }
        if(array_intersect($item_cids,$cids) or in_array($item_style_number,$includes_style_number)){
            return true;
        }
        return false;
    }
}

