<?php

namespace App\Model\Promotion;


use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Illuminate\Support\Facades\DB;

class Coupon extends Model
{
    //指定表名
    protected $table = 'coupon';
    protected $guarded = [];
    const FORMAT='ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const MAX_GENERATE_ATTEMPTS = 10;
    public  $timestamps = false;
    

    public function addTable(){
        $model=$this ->leftJoin('coupon_tag','coupon_tag.key','=','coupon.coupon_tag_key');
        return $model;
    }
    
    public function addField($model){
        $field=[
            'coupon.*',
            'coupon_tag.label',
        ];
        $model = $model->select(...$field);
        return $model;
    }

    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
    
    public function generatePool($length,$size) {
      
        $result=[];
        for ($i = 0; $i < $size; $i++) {
            $attempt = 0;
            do {
                if ($attempt >= self::MAX_GENERATE_ATTEMPTS) {
                    //throw new \Exception('We cannot create the requested Coupon Qty. Please check your settings and try again.');
                    return false;
                }
                $code = $this->generateCode($length,$size);
                ++$attempt;
            } while ($this->exists($code));
            $result[]=$code;
        }  
        
        if(count($result)==$size && $size==count(array_unique($result))){
            return $result;
        }else{
            //throw new \Exception('We cannot create the requested Coupon Qty. Please check your settings and try again.');
            return false;
        }
      
    }
    
    function exists($code){
        if($this->where('code','=',$code)->first()){
            return true;
        }else{
            return false;
        }
   
    }
    
    protected function generateCode($length,$size){
        $format = self::FORMAT;
        $charset=str_split($format);
        $splitChar='';
        $split= 4;
        $charsetSize = count($charset);
        $length=$this->getLength($length,$size);
        $code="";
        for ($i = 0; $i < $length; ++$i) {
            $char = $charset[\App\Lib\Random::getRandomNumber(0, $charsetSize - 1)];
            if (($split > 0) && (($i % $split) === 0) && ($i !== 0)) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }
        
        return $code;
    }
    
    protected function getLength($length,$size)
    {
        $maxProbability = 0.25;
        $format = self::FORMAT;
        $charset=str_split($format);
        $chars = count($charset);
        $maxCodes = pow($chars, $length);
        $probability = $size / $maxCodes;
        
        if ($probability > $maxProbability) {
            do {
                $length++;
                $maxCodes = pow($chars, $length);
                $probability = $size / $maxCodes;
            } while ($probability > $maxProbability);
            return $length;
        }
        return $length;
    }
}
