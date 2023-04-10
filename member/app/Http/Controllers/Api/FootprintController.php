<?php

namespace App\Http\Controllers\Api;

use App\Model\Footprint;
use App\Model\Help;
use App\Model\Redis;
use App\Service\UsersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Model\BrowseHistory;
use Validator;
use Exception;

class FootprintController extends Controller
{

    protected $wechatUserId;

    public function __construct(Request $request)
    {
        parent::__construct();
        if(!$this->user_id){
            return $this->error("未登陆");
        }
//        $encryptString = array_get($request->all(), 'openid');
//
//        $decrypted = decrypt($encryptString);
//
//        $this->wechatUserId = $decrypted['wechatUserId'];
    }
    //
    public function show(Request $request)
    {
    	try {
            $page = $request->page??1;
            $page_size = $request->page_size??10;
            $from = Help::getFrom();

            $data = UsersService::getFootsFromCache($this->user_id);
            if(!$data) $this->error('您尚无足迹商品');
//            $pids = array_column($data,'product_idx')??[];
//            if(!$pids) $this->error('您尚无足迹商品');

            $page_data = array_slice($data,($page-1)*$page_size,$page_size);
            if(!$page_data) $this->success("成功",['pageData'=>[]]);

            $ids = [];
            foreach($page_data as $one){
                $ids[] = $one['product_idx'].'-'.$one['type'];
            }
            $ids = array_unique($ids);

            $requestParams = [
                'ids' => implode(',',$ids),
//                'simple' => 1,
                'from'=>$from
            ];

            $headers = ['Content-Type: application/json'];
            $url = env('GOODS_DOMAIN'). '/outward/product/getProduct';
            $response = http_request($url, $requestParams, $headers, 'POST', '获取产品信息curl：');

            if($response['httpCode'] !== 200) {
                throw new Exception("获取产品信息失败", 0);
            }

            $result = json_decode($response['data'], true);

            if($result['code'] !== 1) {
                throw new Exception($result['message'], 0);
            }
            return $this->success('获取足迹列表成功', ['list'=>$result['data'],'count'=>count($ids)]);

    	} catch (Exception $e) {
    		 return response()->errorAjax($e);
    	}
    	
    }

    public function getPagePids(){
        try {
            $page = $request->page??1;
            $page_size = $request->page_size??10;

            $data = UsersService::getFootsFromCache($this->user_id);
            if(!$data) return $this->error('您尚无足迹商品');

            $page_data = array_slice($data,($page-1)*$page_size,$page_size)??[];

            foreach($page_data as $one){
                $ret[] = [
                    'id'=>$one['product_idx'],
                    'product_type'=>$one['type'],
                ];
            }

            if($ret) return $this->success("成功",['ids'=>$ret]);

        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }


    public function add(Request $request)
    {
        try {
            $proId = $request->input('pid');
//            // 记录商品浏览量
//            $url = env('GOODS_DOMAIN'). '/pddReport';
//
//            $requestParams = ['prodId' => $pdtId];
//
//            $headers = ['Content-Type: application/json'];
//
//            // 记录商品浏览次数
//            http_request($url, $requestParams, $headers, 'POST', '记录商品浏览次数：');
            list($pid,$type) = Help::parsePid($proId);

            $num = Footprint::updateOrCreate(
                ['user_id'=>$this->user_id,'product_idx'=>$pid,'type'=>$type],
                ['user_id'=>$this->user_id,'product_idx'=>$pid,'type'=>$type]
            );

            if($num){
                $redis = Redis::getRedis();
                $key = Redis::getKey('rk.user.footprint',['user_id'=>$this->user_id]);
                if($redis->exists($key)){   //全部足迹记录已在redis中，添加当前足迹
                    $redis->zadd($key,time(),$pid.(($type == 2)?"-2":""));
                    $redis->expire($key,config('common.a_day'));
                }
                return $this->success("添加足迹成功");
            }
            return $this->error("添加足迹失败");

        } catch (Exception $e) {
             return response()->errorAjax($e);
        }
        
    }

    // 显示用户最近前6个足迹
//    public function showRecentlyBrowse(Request $request)
//    {
//        try {
//            // 关联商品信息
//            $productIds = BrowseHistory::where('wechat_user_id', $this->wechatUserId)->limit(6)->orderBy('updated_at', 'desc')->pluck('product_id')->toArray();
//
//            if(!$productIds) {
//                return response()->ajax('你还没有收藏记录', ['idList' => []]);
//
//            }
//
//            return response()->ajax('获取收藏列表成功', ['idList' => $productIds]);
//
//        } catch (Exception $e) {
//            return response()->errorAjax($e);
//        }
//
//    }
}
