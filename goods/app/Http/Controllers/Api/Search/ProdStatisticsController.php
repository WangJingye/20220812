<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/11/1
 * Time: 11:34
 */

namespace App\Http\Controllers\Api\Search;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;
use App\Model\Search\Product;

class ProdStatisticsController extends Controller
{


    public function prodViewCount(Request $request)
    {
        //链接到负责统计各种View的Redis库5
        $view_date = $request->get("date");
        $prod_view = config('redis.prodView') .'_' .$view_date;
        $Statistics_Redis       = Redis::connection('statistics');
        $prod_view_top10_prodIdList        = $Statistics_Redis->ZREVRANGE($prod_view,0 ,9);
        $prod_view_top10_withscores        = $Statistics_Redis->ZREVRANGE($prod_view,0 ,9,"withscores");
        $prod_view_top10_detail_list = Product::getProductById($prod_view_top10_prodIdList);
        $result = $this->proccessProductDetail($prod_view_top10_detail_list, $prod_view_top10_withscores);
        echo(json_encode($result)); exit;
    }

    public function prodViewByProdType(Request $request)
    {
        //链接到负责统计各种View的Redis库5
        $view_date = $request->get("date");
        $prod_view_by_prod_type = config('redis.prodStatisticsByProdType') .'_' .$view_date;
        $Statistics_Redis       = Redis::connection('statistics');
        $prod_view_top10_prodIdList        = $Statistics_Redis->ZREVRANGE($prod_view_by_prod_type,0 ,9);
        $prod_view_top10_withscores        = $Statistics_Redis->ZREVRANGE($prod_view_by_prod_type,0 ,9,"withscores");
        $prod_view_top10_detail_list = Product::getProductById($prod_view_top10_prodIdList);
        $result = $this->proccessProductDetail($prod_view_top10_detail_list, $prod_view_top10_withscores);
        echo(json_encode($result)); exit;
    }

    public function prodAddcartCount(Request $request)
    {
        //链接到负责统计各种View的Redis库6
        $view_date = $request->get("date");
        $ranking_add_cart ="Ranking_AddCart_".$view_date;
        $Statistics_Redis       = Redis::connection('statistics');
        //按照分值从大到小取10条记录出来。
        $add_cart_top10_prodIdList        = $Statistics_Redis->ZREVRANGE($ranking_add_cart,0 ,9);
        $add_cart_top10_withscores        = $Statistics_Redis->ZREVRANGE($ranking_add_cart,0 ,9,"withscores");
        $add_cart_top10_detail_list = Product::getProductById($add_cart_top10_prodIdList);
        $result = $this->proccessProductDetail($add_cart_top10_detail_list, $add_cart_top10_withscores);
        echo(json_encode($result)); exit;
    }

    public function prodShareCount(Request $request)
    {
        //链接到负责统计商品分享次数的Redis6库
        $share_date = $request->get("date");
        $prod_share =config('redis.prodShareStatistics') .'_' . $share_date;
        $Statistics_Redis    = Redis::connection('statistics');
        $prod_share_top10_prodIdList      = $Statistics_Redis->ZREVRANGE($prod_share,0 ,9);
        $prod_share_top10_withscores      = $Statistics_Redis->ZREVRANGE($prod_share,0 ,9,"withscores");
        $prod_share_top10_detail_lsit = Product::getProductById($prod_share_top10_prodIdList);
        $result= $this->proccessProductDetail($prod_share_top10_detail_lsit, $prod_share_top10_withscores);
        echo(json_encode($result)); exit;
    }


    public function prodFavoriteCount(Request $request)
    {
        $paginate = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $start = ($page -1 ) * $paginate;
        $end = ($page -1 ) * $paginate + $paginate -1 ;
        $Statistics_Redis       = Redis::connection('statistics');
        $favorite_day = $request->get("date");
        $rank_favorite ="Ranking_Favorite_" . $favorite_day;
        $favorite_top10_prodIdList         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end);
        $favorite_top10_withscores         = $Statistics_Redis->ZREVRANGE($rank_favorite,$start ,$end,"withscores");
        $favorite_top10_detail_list = Product::getProductById($favorite_top10_prodIdList);
        $result = $this->proccessProductDetail($favorite_top10_detail_list, $favorite_top10_withscores);
        echo(json_encode($result)); exit;
    }
    public function proccessProductDetail($prod_detail_list ,$prod_withscores_list)
    {
        $prod_detail_new = [];
        foreach ($prod_detail_list['list'] as $key => $prod_detail){
            $prod_detail_new[$key]['pdtId'] = $prod_detail['pdtId'];
            $prod_detail_new[$key]['name'] = $prod_detail['name'];
            $prod_detail_new[$key]['series'] = $prod_detail['series'];
            $prod_detail_new[$key]['picUrl'] = $prod_detail['picUrl'];
            $prod_detail_new[$key]['scores'] = $prod_withscores_list[$prod_detail['pdtId']];
        }

        return $prod_detail_new;
    }

}