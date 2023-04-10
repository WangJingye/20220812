<?php

namespace App\Model\Promotion;


use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Illuminate\Support\Facades\DB;
use App\Model\Promotion\Cart;
use App\Lib\Http;

//互斥
class RuleSaveValidation  {
    public $rule = '';
    public $all_rules = '';
    public $conflict_rules = [];
    //过期，不能保存；禁用，不能保存，
    public function statusCheck($rule_id){
        $rule= Cart::where('id',$rule_id)->get()->toArray();
        $rule = $rule[0];
        if(time() > strtotime($rule['end_time'])  //过期
            or $rule['status'] == 3              //禁用
            ){ 
            return false;
        }
        return true;
    }
    //过期，不能保存；禁用，不能保存，激活不能保存
    //for save 
    public function statusCheckForSave($rule_id){
        $rule= Cart::where('id',$rule_id)->get()->toArray();
        $rule = $rule[0];
        if(time() > strtotime($rule['end_time'])  //过期
            or $rule['status'] == 3              //禁用
            or $rule['status'] == 2              //激活
            ){
                return false;
        }
        return true;
    }
    //所有没有过期，未禁用的规则
    public function getAllRuleNotExpire(){
        if($this->all_rules){
            return $this->all_rules;
        }
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','<>',3],['end_time','>',$curr_time],//未过期的，未禁用的
        ];
        $rules = Cart::where($where)->whereIn('type',['product_discount','n_piece_n_discount','order_n_discount','full_reduction_of_order','gift'])
                    ->get()->toArray();
        $this->all_rules = $rules;
        return $this->all_rules;
    }
    //商品折扣
    public function _productCheck($rule_id){
        $rule = $this->getRuleById($rule_id);
        $all_rules = $this->getAllRuleNotExpire();
        $curr_cids_arr = explode(',',$rule['cids']);
        $curr_add_sku_arr = explode(',',$rule['add_sku']);
        foreach($all_rules as $rule_item){
            if($rule_item['id'] == $rule_id){
                continue;
            }
            if(!is_time_cross($rule['start_time'],$rule['end_time'],$rule_item['start_time'],$rule_item['end_time'])){
                continue;
            }
            if($rule_item['type'] == 'product_discount' or $rule_item['type'] == 'n_piece_n_discount'){
                $cids = explode(',',$rule_item['cids']);
                $includes_style_number = explode(',',$rule_item['add_sku']);
//                 if(count($cids) == 0){
//                     return false;
//                 }
                $cross_cids = array_intersect($cids, $curr_cids_arr);
                if(count($cross_cids) and $rule['cids'] ){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
                $cross_add_sku = array_intersect($includes_style_number, $curr_add_sku_arr);
                if(count($cross_add_sku) and $rule['add_sku']){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
            }
        }
        return true;
    }
    //满减
    public function _cutCheck($rule_id){
        $rule = $this->getRuleById($rule_id);
        $all_rules = $this->getAllRuleNotExpire();
        $curr_cids_arr = explode(',',$rule['cids']);
        $curr_add_sku_arr = explode(',',$rule['add_sku']);
        foreach($all_rules as $rule_item){
            if($rule_item['id'] == $rule_id){
                continue;
            }
            if(!is_time_cross($rule['start_time'],$rule['end_time'],$rule_item['start_time'],$rule_item['end_time'])){
                continue;
            }
            if($rule_item['type'] == 'order_n_discount' or $rule_item['type'] == 'full_reduction_of_order'){
                $cids = explode(',',$rule_item['cids']);
                $includes_style_number = explode(',',$rule_item['add_sku']);
//                 if(count($cids) == 0){
//                     return false;
//                 }
                $cross_cids = array_intersect($cids, $curr_cids_arr);
                if(count($cross_cids) and $rule['cids'] ){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
                $cross_add_sku = array_intersect($includes_style_number, $curr_add_sku_arr);
                if(count($cross_add_sku) and $rule['add_sku']){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
            }
        }
        return true;
    }
    //赠品
    public function _giftCheck($rule_id){
        $rule = $this->getRuleById($rule_id);
        $all_rules = $this->getAllRuleNotExpire();
        $curr_cids_arr = explode(',',$rule['cids']);
        $curr_add_sku_arr = explode(',',$rule['add_sku']);
        foreach($all_rules as $rule_item){
            if($rule_item['id'] == $rule_id){
                continue;
            }
            if(!is_time_cross($rule['start_time'],$rule['end_time'],$rule_item['start_time'],$rule_item['end_time'])){
                continue;
            }
            if($rule_item['type'] == 'gift' ){
                $cids = explode(',',$rule_item['cids']);
                $includes_style_number = explode(',',$rule_item['add_sku']);
//                 if(count($cids) == 0){
//                     return false;
//                 }
                $cross_cids = array_intersect($cids, $curr_cids_arr);
                if(count($cross_cids) and $rule['cids'] ){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
                $cross_add_sku = array_intersect($includes_style_number, $curr_add_sku_arr);
                if(count($cross_add_sku) and $rule['add_sku']){
                    $this->conflict_rules[] = $rule_item;
                    continue;
                }
            }
        }
        return true;
    }
    //优惠券，优惠码不做处理
    //同一个商品不能同时有满折，或者满减的促销
    public function check($rule_id){
        $rule = $this->getRuleById($rule_id);
        $type = $rule['type'];
        if($rule['type'] == 'coupon'  
            or substr($rule['type'],0,4) == 'code' 
            ){
            return true;
        }
        if(( $type == 'product_discount' or $type == 'n_piece_n_discount' )  ){
                $this->_productCheck($rule_id);
        }
        if(( $type == 'order_n_discount' or $type == 'full_reduction_of_order' ) ){
                $this->_cutCheck($rule_id);
        }
        if(( $type == 'gift' ) ){
//                $this->_giftCheck($rule_id);
        }
        if(count($this->conflict_rules)){
            $result = [];
            foreach ($this->conflict_rules as $item){
                $result[] = 'id:'.$item['id'] .',name:'.$item['name'].',系列:'.$item['cids'];
            }
            return implode('|',$result);
        }
        return true;
    }
    public function getRuleById($rule_id){
        if($this->rule){
            return $this->rule;
        }
        $rule = [
            'id'=>request('id'),
            'type'=>request('type'),
            'cids'=>request('cats')?implode(',',array_keys(request('cats'))):'',
            'add_sku'=>request('add_sku'),
            'start_time'=>request('start_time'),
            'end_time'=>request('end_time'),
        ];
        log_json('api', 'rule', print_r($rule,true));
        $this->rule = $rule;
        return $this->rule;
    }
    
}
