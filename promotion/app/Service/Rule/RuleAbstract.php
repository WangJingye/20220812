<?php
namespace App\Service\Rule;

use App\Service\Condition\Condition;

Class RuleAbstract implements RuleInterface
{
    public $cartItems=[];
    public $condition;
    public $rule;//当前rule
    public $rules = [];
    public $data = [];
   
    
    function __construct(){

    }
    
    public function getData(){
        return $this->data;
    }
    public function setData($data){
        $this->data = $data;
        $this->condition->setData($data);//同时把data传递给condition
        return $this;
    }
    public function setCondition($condition){
        $this->condition = $condition;
        return $this;
    }
    public function setRule($rule){
        $this->rule = $rule;
        return $this;
    }
    
    function getCartItems(){
        return $this->cartItems;
    }
    
    function setCartItems($cartItems){
        $this->cartItems = $cartItems;
    }
    
    public function getRules(){
        return $this->rules;
    }
    public function setRules($rules){
        $this->rules = $rules;
    }
    
    protected function getMatchAllRules(){}
    
    protected function getBestRule(){}
    
    protected function getPriorityRule(){}
    
    protected function getContent($rule){
        return $rule->content;
    }
    
    function collect(){
        $this->deal($this->rule);
        return $this->data;
    }
    
    function checkRule($rule){
        return $this->condition->setRule($rule)->setCartItems($this->cartItems)->check();
    }
}



