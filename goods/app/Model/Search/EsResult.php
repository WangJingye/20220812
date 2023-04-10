<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/7
 * Time: 11:04
 */

namespace App\Model\Search;


class EsResult extends Model
{


    public static function verification($esResult)
    {
        //先判断命中的记录的数量，如果为0表示没有命中
        $hits = $esResult["hits"]["total"];
        //判断分片命中数, 总数和成功数是否相同
        $shardsTotal = $esResult["_shards"]["total"];
        $shardsSuccessful = $esResult["_shards"]["successful"];
        if($hits == 0)
            return false;
        if($shardsTotal !== $shardsSuccessful)
            return false;

        $es_product_list = $esResult["hits"]["hits"];
        $page_count = (int)ceil($esResult["hits"]["total"] / 10); //计算分页数量，每页10个
        $goldenType_golden = $esResult['aggregations']['golden']['doc_count'];
        $goldenType_18k = $esResult['aggregations']['18k']['doc_count'];
        $goldenType_silver = $esResult['aggregations']['silver']['doc_count'];
        $goldenType_pt = $esResult['aggregations']['pt']['doc_count'];
        $productType_diamond = $esResult['aggregations']['diamond']['doc_count'];
        $productType_pearl = $esResult['aggregations']['pearl']['doc_count'];
        $productType_gem = $esResult['aggregations']['gem']['doc_count'];

        $data['totalPage'] = $page_count;
        $data['es_product_list'] = $es_product_list;
        $data['goldenType_golden'] = $goldenType_golden;
        $data['goldenType_18k'] = $goldenType_18k;
        $data['goldenType_pt'] = $goldenType_pt;
        $data['goldenType_silver'] = $goldenType_silver;
        $data['productType_diamond'] = $productType_diamond;
        $data['productType_pearl'] = $productType_pearl;
        $data['productType_gem'] = $productType_gem;

         return $data;

    }


    /**
     * 处理ES搜索结果中的原始数据为Product Id集合
     * @param $verificationResult
     * @return array
     */
    public static function processProductId($verificationResult)
    {
        $product_id_list = [];
        foreach ($verificationResult['es_product_list'] as $es_product){
            $product_id_list[] = $es_product["_source"]["product_id"];
        }
        return $product_id_list;
    }

    /**
     * 处理ES搜索结果中的原始数据为Product Id集合
     * @param $verificationResult
     * @return array
     */
    public static function processProductDetail($verificationResult)
    {
        $product_detail_list = [];
        foreach ($verificationResult['es_product_list'] as $es_product){
            $product_detail_list[] = json_decode($es_product["_source"]["product_detail"] ,true);
        }
        return $product_detail_list;
    }

    /**
     * 处理检索出的商品金属分类
     * @param $verificationResult
     * @return array
     */
    public static function processFilterMetal($verificationResult)
    {
        $fillterMetal =  array (
            0 =>
                array (
                    'key' => 'golden',
                    'name' => '黄金',
                    'able' => false,
                ),
            1 =>
                array (
                    'key' => '18k',
                    'name' => '18K金',
                    'able' => false,
                ),
            2 =>
                array (
                    'key' => 'pt',
                    'name' => '铂金',
                    'able' => false,
                ),
            3 =>
                array (
                    'key' => 'silver',
                    'name' => '银',
                    'able' => false,
                )
        );

        if ($verificationResult['goldenType_golden'] > 0){
            $fillterMetal[0]['able'] = true;
        }
        if ($verificationResult['goldenType_18k'] > 0){
            $fillterMetal[1]['able'] = true;
        }
        if ($verificationResult['goldenType_pt'] > 0){
            $fillterMetal[2]['able'] = true;
        }
        if ($verificationResult['goldenType_silver'] > 0){
            $fillterMetal[3]['able'] = true;
        }

        return $fillterMetal;


    }

    /**
     * 处理检索商品的宝石分类
     * @param $verificationResult
     * @return array
     */
    public static function processFilterJewel($verificationResult)
    {
        $fillterJewel =
            array (
                0 =>
                    array (
                        'key' => 'diamond',
                        'name' => '钻石',
                        'able' => false,
                    ),
                1 =>
                    array (
                        'key' => 'pearl',
                        'name' => '珍珠',
                        'able' => false,
                    ),
                2 =>
                    array (
                        'key' => 'gem',
                        'name' => '彩宝',
                        'able' => false,
                    ),
            );
        if($verificationResult['productType_diamond'] >0){
            $fillterJewel[0]['able'] = true;
        }
        if($verificationResult['productType_pearl'] >0){
            $fillterJewel[1]['able'] = true;
        }
        if($verificationResult['productType_gem'] >0){
            $fillterJewel[2]['able'] = true;
        }
        return $fillterJewel;
    }
}