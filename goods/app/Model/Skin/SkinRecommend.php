<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/1
 * Time: 13:07
 */

namespace App\Model\Skin;

use App\Model\Skin\SkinReportData as SkinReportData;
use App\Model\Skin\SkinFaqProduct as SkinFaqProduct;
use App\Model\Skin\SkinProductList as SkinProductList;
use App\Model\Skin\SkinStepInformation as SkinStepInformation;

class SkinRecommend
{

    //所有问答的集合，从这里取数据返回给前端整合到推荐商品里
    public static $answer_list = [
        "11" => "20岁以下",
        "12" => "20-30",
        "13" => "30-40",
        "14" => "40-50",
        "15" => "50以上",
        "21" => "从不敏感",
        "22" => "偶尔敏感",
        "23" => "换季敏感",
        "24" => "频繁敏感",
        "25" => "极其敏感",
        "31" => "干性",
        "32" => "油性",
        "33" => "中性",
        "34" => "混合偏干",
        "35" => "混合偏油",
        "41" => "保湿-基础护理",
        "42" => "抗老-紧致",
        "43" => "抗老-淡纹",
        "44" => "美白",
        "45" => "控油"
    ];

    //处理肌肤测试接口推荐的商品
     public function getSkinProduct($fiveSkinData)
    {
        $skinProductList = [];
        //asort配合下面的方式可以取出第一位的，arsort会错。
        asort($fiveSkinData);
        list($key,$value) = array(array_keys($fiveSkinData,end($fiveSkinData))[0], end($fiveSkinData));
        $skinProductId = SkinProductList::$skin_product_list[$key][$value];
        if(!is_array($skinProductId)){
            $skinProductList[] = $skinProductId;
        }
        else{
            $skinProductList[] = $skinProductId[array_rand($skinProductId)];
        }
        return $skinProductList;
    }


    public function getRecProduct($productIdList)
    {
        $productInfo = [];
        foreach ($productIdList as $productId){
            $productInfo[] = SkinProductList::$all_recProduct_list[$productId];
        }
        return $productInfo;
    }
    //处理用户回答的问题，从编号返回具体问答
    public function getAnswer($answer)
    {
        $answer_list = [];

        foreach ($answer as $value){
            $answer_list[] = self::$answer_list[$value];
        }
        return $answer_list;
    }

    //通过Q4和Q2的问答，推荐商品
    public function getQ4Product($answer)
    {

        if ($answer['q2'] == 25) {
            $product = SkinFaqProduct::$q2_yes_product_list[$answer['q4']];
        }
        else{
            $product = SkinFaqProduct::$q2_none_product_list[$answer['q4']];
        }
        return $product;
    }

    //处理护肤步骤，区分是否敏感肌
    public function skincareRitual($answer)
    {

        if($answer['q2'] == 25){
            $skincare_ritual = SkinStepInformation::$q2_yes_step_list[$answer['q4']];
        }
        else{
            $skincare_ritual = SkinStepInformation::$q2_none_step_list[$answer['q4']];
        }

        return $skincare_ritual;
    }

    /**
     * 通过对输入的问答进行键值排序，计算出数值最高的几个。
     * @param $fiveSkinData
     * @return array
     */
    public function problemFields($fiveSkinData)
    {
        $problemFields = [];
        arsort($fiveSkinData);
        $answer_value = array_values($fiveSkinData);
        if ($answer_value[0] < 2){
            return $problemFields;
        }
        else{
            $answer_keys = array_keys($fiveSkinData);
            //上面两行先把数组排序后的键名键值取出来。
            $problemFields[] = $answer_keys[0];
            if ($answer_value[0] === $answer_value[1]){
                $problemFields[] = $answer_keys[1];
            }
            if($answer_value[0]  === $answer_value[2]){
                $problemFields[] = $answer_keys[2];
            }
            if ($answer_value[0] === $answer_value[3]){
                $problemFields[] = $answer_keys[3];
            }
            if ($answer_value[0] === $answer_value[4]){
                $problemFields[] = $answer_keys[4];
            }
            return $problemFields;
        }

    }
}