<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Model\User;
use App\Model\Role;
use App\Model\Permission;
use App\Model\WxSmallDailySummary;
use App\Model\UserTracking;
use App\Model\prodOrderCollects;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{

    private $report_data;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->report_data = date("Ymd", strtotime("-1 day"));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.index');
    }

    public function collects()
    {
        $searchDate =  date('Y-m-d', strtotime('-1 day'));
        //pv/uv
       $wx_daily_summary = WxSmallDailySummary::where('ref_date', '<', date('Y-m-d'))
                                   ->orderBy('ref_date', 'desc')
                                   ->paginate(1)
                                   ->toArray();
        $wx_daily_pv = $wx_daily_summary['data'][0]['visit_pv'] ?? 0;
        $wx_daily_uv = $wx_daily_summary['data'][0]['visit_uv'] ?? 0;

        $list['lists'] = 
        [
            'visit_uv' => $wx_daily_uv,
            'visit_pv' => $wx_daily_pv
        ];

        $daliy_shopping_uv = 2;

        if(is_array($this->shoppingCartUv())){
            $daliy_shopping_uv = $this->shoppingCartUv()['data'][0]['shoppingCartUV']??0;
        }

        if($wx_daily_uv)
        {
            $list['rates']['rate2'] = bcdiv($daliy_shopping_uv, $wx_daily_uv, 4) * 100 . '%';
            //下单转化率
            $order_collects = prodOrderCollects::where('days', '<', date('Y-m-d'))
                                       ->orderBy('days', 'desc')
                                       ->paginate(1)
                                       ->toArray();
            $order_uv = $order_collects['data'][0]['uv'] ?? 0;
            $list['rates']['rate1'] = "0%";
            if($order_uv)
            {
                $list['rates']['rate1'] = bcdiv($order_uv, $wx_daily_uv, 4) * 100 . '%';
            }
        }else
        {
            $list['rates'] = ['rate1' => '0%', 'rate2' => '0%'];

        }
        //关键词
        $Statistics_Redis       = Redis::connection('statistics');
        $rank_keyword ="Ranking_SearchKeywords_" . $this->report_data;
        $total = $Statistics_Redis->ZCARD($rank_keyword);
        $search_keywords_top10  = $Statistics_Redis->ZREVRANGE($rank_keyword,0 ,10);
        $search_keywords_top10_withscores  = $Statistics_Redis->ZREVRANGE($rank_keyword, 0 ,10, "withscores");
        $search_detail = [];
        foreach ($search_keywords_top10 as $key => $value)
        {
            $search_detail[$key]["keyword"] = $value;
            $search_detail[$key]["count"] = $search_keywords_top10_withscores[$value];
        }
        $list['searchData'] =  $search_detail;

        //商品访问top3
        ////http://goods.css.com.cn
        $view_count = file_get_contents("http://goods.css.com.cn/prodView?date=$this->report_data");
        $view_count  = json_decode($view_count,true);
        $view_count = array_slice($view_count,0,3);
        $list['goodsVisitData'] = $view_count;
        //
        //商品分享top3
        $share_count = file_get_contents("http://goods.css.com.cn/prodShare?date=$this->report_data");
        $share_count  = json_decode($share_count,true);
        $share_count = array_slice($share_count,0,3);
        $list['goodsShareData'] = $share_count;

        //收藏top3
        $rank_favorite ="Ranking_Favorite_" . $this->report_data;
        $total = $Statistics_Redis->ZCARD($rank_favorite);
        $favorite_count = file_get_contents("http://goods.css.com.cn/prodFavorite?page=1&paginate=3&date=$this->report_data");
        if(strlen($favorite_count) == 2){
            $favorite_count = [];
        }
        if(!is_array($favorite_count)){
            $favorite_count = json_decode($favorite_count, true);
        }
        $list['goodsCollectData'] = $favorite_count;

        //链接到负责统计各种View的Redis库6
        $Statistics_Redis                       = Redis::connection('statistics');
        $prod_statistics = "prod_statistics_by_prod_type_" . $this->report_data;
        $typeViewScores        = $Statistics_Redis->ZREVRANGE($prod_statistics , 0 ,-1,"withscores");


        $typeView[0]['typeName'] = '定价黄金（GF）';
        $typeView[0]['count'] = $this->checkData("GF" ,$typeViewScores);

        $typeView[1]['typeName'] = '计价黄金（GA）';
        $typeView[1]['count'] = $this->checkData("GA" ,$typeViewScores);

        $typeView[2]['typeName'] = '定/计价铂金（PF /PA/MP）';
        $typeView[2]['count']   = $this->checkData("PF" ,$typeViewScores)
                                + $this->checkData("PA" ,$typeViewScores)
                                + $this->checkData("MP" ,$typeViewScores);

        $typeView[3]['typeName'] = '镶嵌（DF/DI/GS/PL/QF/SS/TF/XF）';
        $typeView[3]['count']  =  $this->checkData("DF" ,$typeViewScores)
                                + $this->checkData("DI" ,$typeViewScores)
                                + $this->checkData("GS" ,$typeViewScores)
                                + $this->checkData("PL" ,$typeViewScores)
                                + $this->checkData("QF" ,$typeViewScores)
                                + $this->checkData("SS" ,$typeViewScores)
                                + $this->checkData("TF" ,$typeViewScores)
                                + $this->checkData("XF" ,$typeViewScores);

        $typeView[4]['typeName'] = 'K金（FJ）';
        $typeView[4]['count'] = $this->checkData("FJ" ,$typeViewScores);

        $count = array_column($typeView,'count');
        array_multisort($count,SORT_DESC,$typeView);
        $typeView = array_slice($typeView,0,3);
        $list['goodsCategoryData'] = $typeView;
        $addcart_count = file_get_contents("http://goods.css.com.cn/prodAddcart?date=$this->report_data");
        $addcart_count  = json_decode($addcart_count,true);
        $addcart_count = array_slice($addcart_count,0,3);
        $list['goodsAddData']=$addcart_count;
        $list['title'] = '以下为'. $this->report_data.'数据';
        return $this->responseJson(200, 'success', $list);
    }
    public function shoppingCartUv()
    {
        $daily_shopping_uv = UserTracking::where('ref_date', '<', date('Y-m-d'))
            ->orderBy('ref_date', 'desc')
            ->paginate(1)
            ->toArray();
        return $daily_shopping_uv;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 数据表格接口
     */
    public function data(Request $request)
    {
        $model = $request->get('model');
        switch (strtolower($model)) {
            case 'user':
                $query = new User();
                break;
            case 'role':
                $query = new Role();
                break;
            case 'permission':
                $query = new Permission();
                $query = $query->where('parent_id', $request->get('parent_id', 0));
                break;
            default:
                $query = new User();
                break;
        }
        $res = $query->paginate($request->get('limit', 30))->toArray();
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res['total'],
            'data' => $res['data']
        ];
        return response()->json($data);
    }

    public function checkData($prodType ,$typeViewScores)
    {
        $result = isset($typeViewScores["$prodType"]) ? $typeViewScores["$prodType"] : 0;
        return $result;
    }

}
