<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\ProductHelp;

class CommonController extends Controller
{
    /**
     * 获取品牌系列副系列.
     */
    public function getBrandCollection(Request $request)
    {
        $return = [];
        $return['brand'] = [];
        $return['collection'] = [];
        $return['subCollection'] = [];

        $brandList = Spu::select('brand')->distinct()->whereNotNull('brand')->get()->toArray();
        if ($brandList) {
            $returnBrand = [];
            foreach ($brandList as $brandItem) {
                $returnBrand[] = $brandItem['brand'];
            }
            $return['brand'] = $returnBrand;
        }
        $collectionList = Spu::select('collection_name')->distinct()->whereNotNull('collection_name')->get()->toArray();
        if ($collectionList) {
            $returnCollection = [];
            foreach ($collectionList as $collectionItem) {
                $returnCollection[] = $collectionItem['collection_name'];
            }
            $return['collection'] = $returnCollection;
        }
        $subCollectionList = Spu::select('sub_collection_name')->distinct()->whereNotNull('sub_collection_name')->get()->toArray();
        if ($subCollectionList) {
            $returnSubCollection = [];
            foreach ($subCollectionList as $subCollectionItem) {
                $returnSubCollection[] = $subCollectionItem['sub_collection_name'];
            }
            $return['subCollection'] = $returnSubCollection;
        }

        return $this->success($return);
    }

    /**
     * 材质列表.
     */
    public function getProdTypelist(Request $request)
    {
        $prodTypeDesc = ProductHelp::$prodTypeDesc;

        return json_encode($prodTypeDesc);
    }

    /**
     * 用途列表.
     */
    public function getUsage(Request $request)
    {
        $usage = ProductHelp::$usage;

        return json_encode($usage);
    }

    /**
     * 品牌系列列表.
     */
    public function getBrandColl(Request $request)
    {
        $brandColl = ProductHelp::$brandColl;

        return json_encode($brandColl);
    }
}
