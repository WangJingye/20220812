<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/3
 * Time: 18:04
 */

namespace App\Model\Search;
use App\Model\Search\SearchES;

class Catalog extends Model
{

    /**
     * 匹配分类目录对应产品
     * @param $request_info
     * @return mixed
     */
    public static function matchCatalog($request_info)
    {
        //先从ES中获取所匹配到的商品所有信息，可能为0可能多个
        //$matchResult = SearchES::SearchFromESByMultiMatch($request_info);
        $matchResult = SearchES::SearchCatalogFromESByFilter($request_info);
        if ($matchResult) {
            //拿到ES中匹配到的商品的所有数据,包括筛选
            $verificationResult = EsResult::verification($matchResult);
            if(!$verificationResult){
                //如果经过处理后发现没哟匹配到返回false
                return false;
            }
            //从ES的返回结果中整理出需要的商品ID列表
            $product['list'] = EsResult::processProductDetail($verificationResult);
            //从ES的返回结果中整理出需要的金属特性
            $product['fillterMetal'] = EsResult::processFilterMetal($verificationResult);
            //从ES的返回结果中整理出需要的宝石特性
            $product['fillterJewel'] = EsResult::processFilterJewel($verificationResult);
            //将搜索结果总的页数返回给前端
            $product['totalPage'] = $verificationResult['totalPage'];
            //将ES中匹配到额数据清洗成商品ID列表并返回
            return $product;
        } else {
            $result = false;
        }

        return $result;
    }



}