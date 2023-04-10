<?php
namespace App\Services\Cart;

//
class GetCartInfo
{
    public $ids='';
    public $selected_free_ids = '';
    public function init($ids,$selected_free_ids){
        $this->ids = $ids;
        $this->selected_free_ids = $selected_free_ids;
        return $this;
    }
    //前端传入的ids，并且是选中的
    public function getSelectedIds(){
        $ids = $this->ids;
        if(!$ids){
            return [];
        }
        $arr = explode('|',$ids);
        $selected_skus = [];
        foreach ($arr as $item){
            $product = explode(',',$item);
            $sku = $product[0];
//            $qty = $product[1];
            $selected = $product[2];
            if($selected){
                $selected_skus[] = $sku;
            }
        }
        return $selected_skus;
    }

    //前端传入的free_ids，并且是选中的
    public function getSelectedFreeIds(){
        $selected_free_ids = $this->selected_free_ids;
        if(!$selected_free_ids){
            return [];
        }
        return explode(',',$selected_free_ids);
    }
}
