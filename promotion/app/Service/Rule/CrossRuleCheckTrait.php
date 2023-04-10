<?php
namespace App\Service\Rule;
//后台叠加： 检测是否可以叠加 
Trait CrossRuleCheckTrait  
{
    //会员叠加处理，会员折扣的时候检测，看之前是否有应用了满减，优惠券，如果有需要检测之前应用的满减，优惠券设置了叠加会员折扣才能应用会员折扣
    public function crossRuleCheckForMember($item,$type='cut'){
        if(!isset($item['applied_rule_ids'])){
            return true;//没有促销，忽略叠加问题
        }
        if(!isset($item['applied_cut']) and !isset($item['applied_coupon']) ){
            return true;//没有满减，优惠券，忽略叠加问题
        }
        $product_discount_rule_ids = [];
        $applied_rules = $item['applied_rule_ids'];
        foreach($applied_rules as $apply){
            if($apply['type'] == $type){
                $product_discount_rule_ids[] = $apply['rule_id'];
            }
        }
        foreach($product_discount_rule_ids as $rule_id){
            $rule = $this->getRuleByRuleId($rule_id);
            $appied_rule_addRules_ids_arr = explode(',',$rule['addrules']);
            if(!in_array('m',$appied_rule_addRules_ids_arr)){
                return false;//上面的applied_rules_ids的addrules(叠加规则）里面存在不包括会员折扣的
            }
        }
        return true;
    }
    //会员叠加处理，看当前的rule是否有选择了可以会员叠加
    public function crossMemberCheckPure($rule){
        $curr_rule_cross_ids = $rule['addrules'];
        $curr_rule_cross_ids_arr = explode(',',$curr_rule_cross_ids);
        if(in_array('m',$curr_rule_cross_ids_arr)){
            return true;//当前这个rule,后台选择了叠加会员折扣
        }
        return false;
    }
    //会员叠加处理，满减，优惠券，赠品的时候检测，看之前是否应用了会员折扣，
    public function crossMemberCheck($rule,$item,$type='member'){
        if(!isset($item['applied_member'])){
            return true;//没有会员折扣，忽略叠加问题
        }
        $curr_rule_cross_ids = $rule['addrules'];
        $curr_rule_cross_ids_arr = explode(',',$curr_rule_cross_ids);
        if(in_array('m',$curr_rule_cross_ids_arr)){
            return true;//当前这个rule,后台选择了叠加会员折扣
        }
        return false;
    }
    //赠品是否可以跟悦享钱叠加
    public function crossPointCheck($rule,$item,$type='point'){
        if(!isset($item['usedPoint'])){
            return true;//没有使用悦享钱，忽略叠加问题
        }
        $curr_rule_cross_ids = $rule['addrules'];
        $curr_rule_cross_ids_arr = explode(',',$curr_rule_cross_ids);
        if(in_array('p',$curr_rule_cross_ids_arr)){
            return true;//当前这个rule,后台选择了叠加悦享钱折扣
        }
        return false;
    }
    //促销规则叠加处理
    //product_discount,
    public function crossRuleCheck($rule,$item,$type='product_discount'){
        if(!isset($item['applied_rule_ids'])){
            return true;//没有促销，忽略叠加问题
        }
        $product_discount_rule_ids = [];
        $applied_rules = $item['applied_rule_ids'];
        foreach($applied_rules as $apply){
            if($apply['type'] == $type){
                $product_discount_rule_ids[] = $apply['rule_id'];
            }
        }
        if(!count($product_discount_rule_ids)){
            return true;//没有商品折扣，忽略叠加问题
        }
        $curr_rule_cross_ids = $rule['addrules'];
        $curr_rule_cross_ids_arr = explode(',',$curr_rule_cross_ids);
        $curr_rule_id = $rule['id'];
        $can_cross = true;
        //当前rule跟之前应用的所有rule都能够叠加，可以是当前rule叠加了之前的rule,也可以是之前的rule叠加了当前的rule
        foreach($product_discount_rule_ids as $rule_id){
            $each_item_can_cross = false;
            if(in_array($rule_id,$curr_rule_cross_ids_arr)){
                //当前rule的addrules（叠加规则）包括上面applied_rules_ids里面的rule_id
                $each_item_can_cross = true;
            }
            if($each_item_can_cross == false){
                $applied_rule = $this->getRuleByRuleId($rule_id);
                $appied_rule_addRules_ids_arr = explode(',',$applied_rule['addrules']);
                if(in_array($curr_rule_id,$appied_rule_addRules_ids_arr)){
                    //上面的applied_rules_ids的addrules(叠加规则）里面包括当前的rule_id
                    $each_item_can_cross = true;
                }
            }
            if($each_item_can_cross == false){//当前rule跟之前应用的applied_rule_ids的某个rule不能叠加
                $can_cross = false;
                break;
            }
        }
        if($can_cross == true){
            return true;
        }
        return false;//不能叠加使用
    }
    
    public function getRuleByRuleId($rule_id){
        $rules = $this->data['rules'];
        foreach($rules as $rule){
            if($rule['id'] == $rule_id){
                return $rule;
            }
        }
    }
    
    //叠加处理
    public function crossRuleCouponCheck($rule,$item){
        $type = 'product_discount';
        $product_check = $this->crossRuleCheck($rule, $item,$type);
        if(!$product_check){
            return false;
        }
        $type = 'cut';
        $cut_check = $this->crossRuleCheck($rule, $item,$type);
        if(!$cut_check){
            return false;
        }
        return true;
    }
    
    //叠加处理
    public function crossRuleGiftCheck($rule,$item){
        $type_arr = ['product_discount','cut','coupon','code'];
        foreach($type_arr as $type){
            $product_check = $this->crossRuleCheck($rule, $item,$type);
            if(!$product_check){
                return false;
            }
        }
        return true;
    }
    
    //叠加处理
    public function crossRuleCodeCheck($rule,$item){
        $type_arr = ['product_discount','cut','coupon',];
        foreach($type_arr as $type){
            $product_check = $this->crossRuleCheck($rule, $item,$type);
            if(!$product_check){
                return false;
            }
        }
        return true;
    }
    
    
}

