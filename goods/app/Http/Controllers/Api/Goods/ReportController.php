<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Model\Goods\ProductHelp;

class ReportController extends Controller
{

    /**
     * 浏览量上报.
     */
    public function pdtView(Request $request)
    {
        $prodId = $request->product_id;

        $ProductHelp = new ProductHelp();
        $view_date = date("Ymd");
        $pipe = $ProductHelp->redisModel->redis->multi(\Redis::PIPELINE);
        //统计浏览量
        $pipe->zincrby(config('redis.prodView') .'_' .$view_date, 1, $prodId);
        $pipe->exec();
        unset($pipe);

        return $this->success([]);
    }

    /**
     * 分享上报.
     */
    public function pdtShare(Request $request)
    {
        $prodId = $request->product_id;
        $ProductHelp = new ProductHelp();
        $share_date = date("Ymd");
        $ProductHelp->redisModel->_zincrby(config('redis.prodShareStatistics') .'_' . $share_date , 1, $prodId);

        return $this->success([]);
    }

    //分类浏览
    public function catView(Request $request)
    {
        $cat_id = $request->cat_id;
        $ProductHelp = new ProductHelp();
        $share_date = date("Ymd");
        $ProductHelp->redisModel->_zincrby(config('redis.catView') .'_' . $share_date , 1, $cat_id);

        return $this->success([]);
    }

    //加购点击
    public function addCart(Request $request)
    {
        $product_id = $request->product_id;
        $ProductHelp = new ProductHelp();
        $view_date = date("Ymd");
        $ProductHelp->redisModel->_zincrby("Ranking_AddCart_".$view_date, 1 ,$product_id);
        return $this->success([]);
    }

}
