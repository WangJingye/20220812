<?php

namespace App\Http\Controllers\Api;

use App\Model\Help;
use App\Service\UsersService;
use App\Services\Api\UserServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Favorite;
use Validator;
use Exception;
//use Illuminate\Support\Facades\Redis;
use App\Model\Redis;


class FavoriteController extends Controller
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
//        $this->user_id = $decrypted['wechatUserId'];
    }
    // 展示收藏列表
    public function show(Request $request)
    {
    	try {
    	    $page = $request->page??1;
    	    $page_size = $request->page_size??10;
            $from = Help::getFrom();

            $data = UsersService::getFavsFromCache($this->user_id);
//            $pids = array_column($data,'product_idx')??[];
            if(!$data) return $this->success("您尚未收藏商品",['pageData'=>[]]);

            $page_data = array_slice($data,($page-1)*$page_size,$page_size);
            if(!$page_data) return $this->success("成功",['pageData'=>[]]);

            $ids = [];
            foreach($page_data as $one){
                $ids[] = $one['product_idx'].'-'.$one['type'];
            }

            $requestParams = [
                'ids' => implode(',',$ids),
//                'simple'=>1,
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
            return response()->ajax('获取收藏列表成功', $result['data']);

    	} catch (Exception $e) {
            return response()->errorAjax($e);
    	}
    	
    }

    public function getPagePids(){
        try {
            $page = $request->page??1;
            $page_size = $request->page_size??10;

            $data = UsersService::getFavsFromCache($this->user_id);
            if(!$data) return $this->error('您尚未收藏商品');



//            $pids = array_column($data,'product_idx')??[];
//            if(!$pids) return $this->error('您尚未收藏商品');

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

    // 收藏
    public function collect(Request $request)
    {
        try {
//            $Statistics_Redis = Redis::getRedis('statistics');
            $fields = [
                'pid' => 'required|string|max:255',
            ];
            $validator = Validator::make($request->all(), $fields, [
                    'required' => ':attribute 为必填项', //:attribute 字段占位符表示字段名称
                    'string'   => ':attribute 须为字符串',
                    'max'      => ':attribute 大于了:size位',
                ]
            );
            if($validator->fails()){
                throw new Exception($validator->errors()->first(), 0);
            }
            $params = array_only($request->all(),array_keys($fields));

            $productId = array_get($params, 'pid');
            list($pid,$type) = Help::parsePid($productId);

            $num = Favorite::updateOrCreate(
                ['user_id'=>$this->user_id,'product_idx'=>$pid,'type'=>$type],
                ['user_id'=>$this->user_id,'product_idx'=>$pid,'type'=>$type]
            );
            if($num){
                $redis = Redis::getRedis();
                $key = Redis::getKey('rk.user.favorite',['user_id'=>$this->user_id]);
                if($redis->exists($key)){   //全部收藏记录已在redis中，添加当前收藏的
                    $redis->zadd($key,time(),$pid.(($type == 2)?"-2":""));
                    $redis->expire($key,config('common.a_day'));
                }
                return $this->success("收藏成功");
            }
            return $this->error("收藏失败");

        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
        
    }

    // 取消收藏
    public function cancel(Request $request)
    {
        try {
            $fields = [
                'pid' => 'required|string|max:255',
                'type' => 'required',
            ];
            $validator = Validator::make($request->all(), $fields, [
                    'required' => ':attribute 为必填项', //:attribute 字段占位符表示字段名称
                    'string'   => ':attribute 须为字符串',
                    'max'      => ':attribute 大于了:size位',
                ]
            );
            if($validator->fails()){
                throw new Exception($validator->errors()->first(), 0);
            }
            $params = array_only($request->all(),array_keys($fields));

            $productId = array_get($params, 'pid');
            list($pid,$type) = Help::parsePid($productId);
//            $type = $request->type??1;

//            $favoriteList = Favorite::where('wechat_user_id', $this->wechatUserId)->where('product_id', $productId)->first();
//
//            if(!$favoriteList) {
//                return response()->ajax('该商品还没有收藏');
//
//            }
           
//            $res = Favorite::destroy($favoriteList->id);
            $res = Favorite::where('user_id',$this->user_id)->where('product_idx',$pid)->where('type',$type)->delete();

            $redis = Redis::getRedis();
            $key = Redis::getKey('rk.user.favorite',['user_id'=>$this->user_id]);
            $redis->zrem($key,$pid.(($type == 2)?"-2":""));

            if($res){
                return response()->ajax('取消收藏成功');

            } else {
                return response()->ajax('取消收藏失败');

            }
           
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

    public function getFavPids(){
        $data = UsersService::getFavsFromCache($this->user_id)?:[];
        $ret = [];
        foreach($data as $one){
            if($one['type'] == 2) $ret[] = $one['product_idx'].'-2';
            else $ret[] = $one['product_idx'];
        }
//        $pids = array_column($data,'product_idx')??[];
        return $this->success('成功',['pids'=>$ret]);
    }

//    // 展示收藏列表
//    public function showRecently(Request $request)
//    {
//        try {
//            // 关联商品信息
//            $productIds = Favorite::where('wechat_user_id', $this->wechatUserId)->limit(6)->orderBy('updated_at', 'desc')->pluck('product_id')->toArray();
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
