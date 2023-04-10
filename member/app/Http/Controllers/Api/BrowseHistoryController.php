<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\BrowseHistory;
use Validator;
use Exception;

class BrowseHistoryController extends Controller
{

    protected $wechatUserId;

    public function __construct(Request $request)
    { 
        $encryptString = array_get($request->all(), 'openid');

        $decrypted = decrypt($encryptString);

        $this->wechatUserId = $decrypted['wechatUserId'];
    }
    //
    public function show(Request $request)
    {
    	try {
        	// 关联商品信息
        	$productIds = BrowseHistory::where('wechat_user_id', $this->wechatUserId)->limit(20)->orderBy('updated_at','desc')->pluck('product_id')->toArray();

            $url = env('GOODS_DOMAIN'). '/goods/inner/getProdInfoByIds';

            if(!$productIds) {
                return response()->ajax('你还没有历史记录', ['list' => []]);
                
            }

            $requestParams = ['prodIdStr' => json_encode($productIds)];

            $headers = ['Content-Type: application/json'];

            $response = http_request($url, $requestParams, $headers, 'POST', '获取产品信息curl：');

            if($response['httpCode'] !== 200) {
                throw new Exception("获取产品信息异常", 0);
                
            } 
            $result = json_decode($response['data'], true);
            
            return response()->ajax('获取浏览足迹成功', $result['data']);

    	} catch (Exception $e) {
    		 return response()->errorAjax($e);
    	}
    	
    }

    //
    public function add(Request $request)
    {
        try {
            $pdtId = $request->input('pdtId');
            // 记录商品浏览量
            $url = env('GOODS_DOMAIN'). '/pddReport';

            $requestParams = ['prodId' => $pdtId];

            $headers = ['Content-Type: application/json'];

            // 记录商品浏览次数
            http_request($url, $requestParams, $headers, 'POST', '记录商品浏览次数：');

            $history = BrowseHistory::where('wechat_user_id', $this->wechatUserId)->where('product_id', $pdtId)->first();
            if($history){
                $history->updated_at = date('Y-m-d H:i:s');
                $history->save();

            } else {
                // 关联商品信息
                $history = new BrowseHistory();
                $history->wechat_user_id = $this->wechatUserId;
                $history->product_id = $pdtId;
                $history->save();
            }
            
            return response()->ajax('记录足迹成功');

        } catch (Exception $e) {
             return response()->errorAjax($e);
        }
        
    }

    // 显示用户最近前6个足迹
    public function showRecentlyBrowse(Request $request)
    {
        try {
            // 关联商品信息
            $productIds = BrowseHistory::where('wechat_user_id', $this->wechatUserId)->limit(6)->orderBy('updated_at', 'desc')->pluck('product_id')->toArray();

            if(!$productIds) {
                return response()->ajax('你还没有收藏记录', ['idList' => []]);
                
            }
            
            return response()->ajax('获取收藏列表成功', ['idList' => $productIds]);

        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
        
    }
}
