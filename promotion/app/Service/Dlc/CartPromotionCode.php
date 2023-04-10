<?php namespace App\Service\Dlc;

class CartPromotionCode
{
    /**
     * 检查优惠码是否可用
     * @return bool
     * @throws \Exception
     */
    public static function checkAndGetCode(){
        $id = request()->get('id');
        $input_code = request()->get('code_code');
        if(empty($input_code)){
            throw new \Exception('优惠码必填');
        }
        //转为小写
        $input_code = strtolower($input_code);
        //判断是此优惠码是否已存在(不考虑禁用的)
        $model = \App\Model\Promotion\Cart::query()
            ->where('code_code',$input_code)
            ->where('status','<>',3);
        if($id){
            $model->where('id','<>',$id);
        }
        if($model->count()){
            throw new \Exception('优惠码已存在');
        }return $input_code;
    }


}