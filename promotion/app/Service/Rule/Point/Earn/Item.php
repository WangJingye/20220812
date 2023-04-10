<?php
namespace App\Service\Rule\Point\Earn;
use App\Service\Rule\Point\PointAbstract;

//悦享钱，计算每个item可以赚取的悦享钱
//item的小数点
//  先区分计价和定价的悦享钱
//  每个item根据比重分配到item上
//  每个item去掉小数点，总的赚取悦享钱  -  每个item去掉小数点后的赚取悦享钱之后  = 差的赚取悦享钱
//  item按照item差排序
//     小数点后面的数字
//  把差的赚取悦享钱 加到每个item上，直到加完为止

class Item extends PointAbstract
{
    public $fix_items = [];
    public $fix_total_point = 0;
    public $fix_total_amount = 0;
    public $non_fix_items = [];
    public $non_fix_total_point = 0;
    public $non_fix_total_amount = 0;
    public $return_items = [];
    public function __construct($data){
        $this->fix_items = $data['fix_items']??[];
        $this->fix_total_amount = $data['fix_total_amount']??0;
        $this->fix_total_point = $data['fix_total_point']??0;
        $this->non_fix_items = $data['non_fix_items']??[];
        $this->non_fix_total_point = $data['non_fix_total_point']??0;
        $this->non_fix_total_amount = $data['non_fix_total_amount']??0;
    }

    public function process(){
        $this->processItems($this->fix_items, $this->fix_total_point,$this->fix_total_amount);
        $this->processItems($this->non_fix_items, $this->non_fix_total_point,$this->non_fix_total_amount);
        return $this->return_items;
    }
    //处理每个items
    public function processItems($items,$total_point,$total_amount){
        if(!$total_amount or !$total_point){
            return ;
        }
        $new_items = [];
        $total_earn_int_points = 0;//去掉小数点之后的总的赚取悦享钱
        //根据比重分配悦享钱到每个items
        foreach($items as $item){
            $item_earn_points = bcdiv(bcmul($item['final_price'],$total_point), $total_amount);
            $item['earn_raw_points'] = $item_earn_points;//原始赚取的悦享钱
            $item['earn_int_points'] = floor($item['earn_raw_points']);//去掉小数点的悦享钱
            $total_earn_int_points += $item['earn_int_points'];
            $item['earn_diff_points'] = bcsub($item['earn_raw_points'],$item['earn_int_points']) * 1000;//差异的悦享钱
            $new_items[] = $item;
        }
        $total_diff_points = $total_point - $total_earn_int_points;
        $new_items = $this->sortPrice($new_items);
        //把差异部分分配到每个items上
        foreach($new_items as $item){
            $item['earn_points'] = $item['earn_int_points'];
            if($total_diff_points > 0){
                $item['earn_points'] = $item['earn_points'] + 1;
            }
            $total_diff_points--;
            $cart_item_id = $item['cart_item_id'];
            $this->return_items[$cart_item_id] = $item['earn_points'];
        }
    }

    //根据差异部分从高到低排序
    public function sortPrice($items){
        $sort_key = [];
        foreach($items as $item){
            $sort_key[] = $item['earn_diff_points'];
        }
        array_multisort($sort_key, SORT_DESC, $items);
        return $items;
    }
}

