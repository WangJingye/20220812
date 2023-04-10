<?php

namespace App\Http\Controllers\Api;

use App\Model\ProdViewStatistics;
use App\Model\CatViewStatistics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Helpers\Api\AdminCurlLog;
use App\Model\SearchKeywordsDetail;
use App\Model\ProdAddCartStatistics;
use App\Model\ProdFavoriteStatistics;
use App\Model\ProdShareStatistics;
use App\Model\ProdViewByTypeStatistics;
use Illuminate\Support\Facades\DB;

class ProdstatToDbController extends Controller
{
    private $ref_date;

    function __construct($date = null)
    {
        $this->ref_date = isset($date) ? $date : date("Ymd",strtotime("-1 day"));
        parent::__construct();
    }


    /**
     * 将Redis中存储的搜索关键词按天存入DB中
     * @return string
     */
    public function SearchKeywordToDB()
    {
        try {
            $Statistics_Redis = Redis::connection('statistics');
            $rank_keyword = config('app.name').":search:hot:keywords:" . $this->ref_date;

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $search_keywords_top10_withscores = $Statistics_Redis->ZREVRANGE($rank_keyword, 0, -1, "withscores");

            if ($search_keywords_top10_withscores) {

                foreach ($search_keywords_top10_withscores as $keyword => $search_time) {

                    $data = [];

                    $data = [
                        'keywords' => $keyword,
                        'search_time' => $search_time,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'keywords' => $keyword,
                        'ref_date' => $this->ref_date
                    ];

                    SearchKeywordsDetail::updateOrCreate($where, $data);

                }

                return 'success';
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }

    }


    /**
     * 将每天被加入购物车的产品按天更新到DB
     */
    public function AddCartToDB()
    {
        try {
            $ranking_add_cart = "Ranking_AddCart_" . $this->ref_date;
            $Statistics_Redis = Redis::connection('sts');

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $add_cart_top10_withscores = $Statistics_Redis->ZREVRANGE($ranking_add_cart, 0, -1, "withscores");

            if ($add_cart_top10_withscores) {
                foreach ($add_cart_top10_withscores as $prodId => $day_add_times) {

                    $data = [];

                    $data = [
                        'pdtId' => $prodId,
                        'day_add_times' => $day_add_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'pdtId' => $prodId,
                        'ref_date' => $this->ref_date
                    ];

                    ProdAddCartStatistics::updateOrCreate($where, $data);

                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }

    }

    /**
     * 将被收藏产品按天更新到DB
     */
    public function ProdFavoriteToDB()
    {
        try {
            $rank_favorite ="Ranking_Favorite_" . $this->ref_date;
            $Statistics_Redis = Redis::connection('statistics');

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $rank_favorite_withscores = $Statistics_Redis->ZREVRANGE($rank_favorite, 0, -1, "withscores");

            if ($rank_favorite_withscores) {
                foreach ($rank_favorite_withscores as $prodId => $day_favorite_times) {

                    $data = [];

                    $data = [
                        'pdtId' => $prodId,
                        'day_favorite_times' => $day_favorite_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'pdtId' => $prodId,
                        'ref_date' => $this->ref_date
                    ];

                    ProdFavoriteStatistics::updateOrCreate($where, $data);

                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }


    /**
     * 将产品被分享信息更新到DB
     */
    public function ProdShareToDB()
    {
        try {
            $prod_share =config('redis.prodShareStatistics') .'_' . $this->ref_date;
            $Statistics_Redis    = Redis::connection('sts');

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $prod_share_top10_withscores      = $Statistics_Redis->ZREVRANGE($prod_share,0 ,-1,"withscores");
            if ($prod_share_top10_withscores) {
                foreach ($prod_share_top10_withscores as $prodId => $day_share_times) {

                    $data = [];

                    $data = [
                        'pdtId' => $prodId,
                        'day_share_times' => $day_share_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'pdtId' => $prodId,
                        'ref_date' => $this->ref_date
                    ];

                    ProdShareStatistics::updateOrCreate($where, $data);
                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }


    /**
     * 将被浏览产品从Redis按天更新到DB
     */
    public function ProdViewToDB()
    {
        try{
            $prod_view = config('redis.prodView') .'_' .$this->ref_date;
            $Statistics_Redis       = Redis::connection('sts');

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $prod_view_top10_withscores        = $Statistics_Redis->ZREVRANGE($prod_view,0 ,-1,"withscores");
            if ($prod_view_top10_withscores) {
                foreach ($prod_view_top10_withscores as $prodId => $day_view_times) {

                    $data = [];

                    $data = [
                        'pdtId' => $prodId,
                        'day_view_times' => $day_view_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'pdtId' => $prodId,
                        'ref_date' => $this->ref_date
                    ];

                    ProdViewStatistics::updateOrCreate($where, $data);

                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }

    //分类访问数据数据
    public function catViewToDB()
    {
        try{
            $prod_view = config('redis.catView') .'_' .$this->ref_date;
            $Statistics_Redis       = Redis::connection('sts');

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $prod_view_top10_withscores        = $Statistics_Redis->ZREVRANGE($prod_view,0 ,-1,"withscores");
            if ($prod_view_top10_withscores) {
                foreach ($prod_view_top10_withscores as $cat_id => $day_view_times) {

                    $data = [];

                    $data = [
                        'prod_cat_id' => $cat_id,
                        'view_times' => $day_view_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'prod_cat_id' => $cat_id,
                        'ref_date' => $this->ref_date
                    ];

                    CatViewStatistics::updateOrCreate($where, $data);

                }
            }
        }
        catch (\Exception $exception){
            echo $exception;
        }
    }

    public function ProdViewByProdTypeToDB()
    {
        try{
            //链接到负责统计各种View的Redis库6
            $Statistics_Redis   = Redis::connection('statistics');
            $prod_statistics = "prod_statistics_by_prod_type_" . $this->ref_date;

            //以下Redis读取部分直接全量读取整个Zset集合，然后同步到Redis中
            $typeViewScores        = $Statistics_Redis->ZREVRANGE($prod_statistics , 0 ,-1,"withscores");
            if ($typeViewScores) {
                foreach ($typeViewScores as $prod_type_code => $type_view_times) {

                    $data = [];

                    $data = [
                        'prod_type_code' => $prod_type_code,
                        'type_view_times' => $type_view_times,
                        'ref_date' => $this->ref_date
                    ];

                    $where = [
                        'prod_type_code' => $prod_type_code,
                        'ref_date' => $this->ref_date
                    ];

                    ProdViewByTypeStatistics::updateOrCreate($where, $data);

                }
            }
        }catch (\Exception $exception){
            echo $exception;
        }

    }

}
