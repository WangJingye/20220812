<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Api\Promotion\CartController;
use App\Model\Promotion\Coupon;
use App\Model\Promotion\Cart;
use Carbon\Carbon;
use App\Lib\Http;
use Illuminate\Support\Facades\Redis;
use App\Service\Redis\RedisRules;

class CouponController extends CartController
{
//     protected $_model = Coupon::class;
	
    //领取优惠券，处理库存
    public function incrementCouponQty(){
        $coupon_ids = request()->all();
        $curr_time = Carbon::now();
        if(is_array($coupon_ids) and count($coupon_ids)){
            $coupons = Cart::whereIn('id',$coupon_ids)->get()->toArray();
            foreach($coupons as $coupon){
                Cart::where('id',$coupon['id'])->increment('coupon_stock_used');
            }
        }
        return ['code'=>1];
    }

    //归还优惠券库存
    public function restoreCouponQty(){
        $coupon_ids = request()->all();
        if(is_array($coupon_ids) and count($coupon_ids)){
            $coupons = Cart::whereIn('id',$coupon_ids)->get()->toArray();
            foreach($coupons as $coupon){
                Cart::where('id',$coupon['id'])->decrement('coupon_stock_used');
            }
        }
        return ['code'=>1];
    }
    
    //检测优惠券是否有效，库存，有效期，状态
    public function validaCoupon(){
        $coupon_ids = request()->all();
        $data = [];
        $curr_time = Carbon::now();
        if(is_array($coupon_ids) and count($coupon_ids)){
            $coupons = Cart::whereIn('id',$coupon_ids)->get()->toArray();
            foreach($coupons as $coupon){
                $is_valid = 1;
                $msg = '';
                if($coupon['status'] != '2'
                    or $curr_time < $coupon['start_time'] 
                    or $curr_time > $coupon['end_time'] 
                    ){
                    $is_valid = 0;
                    $msg= '优惠券失效';
                }elseif($coupon['coupon_stock_used'] >= $coupon['coupon_stock']){
                    $is_valid = -1;
                    $msg= '该优惠券已领完';
                }
                $data[] = [
                    'coupon_id'=>$coupon['id'],
                    'isValid'=>$is_valid,//0,1,-1
                    'msg'=>$msg,//错误信息，
                ];
            }
        }
        $response['data'] = $data;
        return $response;
    }
        
    protected  function addFilter($model){
        $model = $model->whereIn('type',['coupon','product_coupon']);
        $coupon_ids = request()->all();
        if(is_array($coupon_ids) and count($coupon_ids)){
            $model = $model->whereIn('id',$coupon_ids);
        }
        return $model;
    }
    //失效优惠券，只取近3个月的
    public function unactiveList(){
        $model = $this->getModel();
        $table = $model->getTable();
        $coupon_ids = request()->all();
        
        $limit = request('limit', 100);
        
        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        $model = $model->whereIn('type',['coupon','product_coupon']);
        if(is_array($coupon_ids) and count($coupon_ids)){
            $model = $model->whereIn('id',$coupon_ids);
        }
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        //只取最近3个月的
        $last_three_month = Carbon::now()->subMonth(3)->toDateTimeString();
        $model = $model->where('end_time','>',$last_three_month);
        $res = $model->orderByRaw('total_discount+0 asc,end_time desc')
        ->paginate($limit)
        ->toArray();
        //组装数据
        $data = $res['data'];
        $new_item = [];
        foreach($data as $item){
            $expire_flag = 0;
            if( time() > strtotime($item['end_time']) or $item['status'] == '3'){
                $expire_flag = 1;
            }
            $new_item[] = [
                'id'=>$item['id'],
                'status'=>$expire_flag,//0:未过期，1:已过期
                'price'=>$item['total_discount'],
                'require'=>$item['total_amount'],
                'start'=>strtotime($item['start_time']),
                'end'=>strtotime($item['end_time']),
                'desc'=>$item['name'],
                'coupon_type'=>$item['type'],
                'sku'=>$item['product_coupon_sku'],
                'stock'=>bcsub($item['coupon_stock'],$item['coupon_stock_used']),
            ];
        }
        $response['code'] = 1;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = count($new_item);
        $response['data'] = $new_item;
        
        return $response;
    }
    //有效优惠券
    public function list(){
        $model = $this->getModel();
        $table = $model->getTable();
        
        $name = request('name');
        $limit = request('limit', 100);
        $page = request('page');
        
        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        $model = $this->addFilter($model);
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        $curr_time = date('Y-m-d H:i:s');
        $model = $model->where('status',2)->where('end_time','>',$curr_time);
        $res = $model->orderByRaw('total_discount+0 asc,end_time desc')
        ->paginate($limit)
        ->toArray();
        //组装数据
        $data = $res['data'];
        $new_item = [];
        foreach($data as $item){
            $expire_flag = 0;
            if( time() > strtotime($item['end_time']) ){
                $expire_flag = 1;
            }
            if(!$expire_flag){
                $new_item[] = [
                    'id'=>$item['id'],
                    'status'=>$expire_flag,//0:未过期，1:已过期
                    'price'=>$item['total_discount'],
                    'require'=>$item['total_amount'],
                    'start'=>strtotime($item['start_time']),
                    'end'=>strtotime($item['end_time']),
                    'desc'=>$item['name'],
                    'coupon_type'=>$item['type'],
                    'sku'=>$item['product_coupon_sku'],
                    'stock'=>bcsub($item['coupon_stock'],$item['coupon_stock_used']),
                ];
            }
        }
        $response['code'] = 1;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = count($new_item);
        $response['data'] = $new_item;
        return $response;
    }


    public function allListFromDb(){
        $model = $this->getModel();
        $table = $model->getTable();

        $name = request('name');
        $limit = request('limit', 100);
        $page = request('page');

        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        $model = $this->addFilter($model);
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        $curr_time = date('Y-m-d H:i:s');
        $res = $model
            ->paginate($limit)
            ->toArray();
        //组装数据
        $data = $res['data'];
        $new_item = [];
        foreach($data as $item){
            $expire_flag = 0;
            if( time() > strtotime($item['end_time']) or $item['status'] != 2 ){
                $expire_flag = 1;
            }
            $new_item[] = [
                'id'=>$item['id'],
                'status'=>$expire_flag,//0:未过期，1:已过期
                'price'=>$item['total_discount'],
                'require'=>$item['total_amount'],
                'start'=>strtotime($item['start_time']),
                'end'=>strtotime($item['end_time']),
                'desc'=>$item['name'],
                'coupon_type'=>$item['type'],
                'sku'=>$item['product_coupon_sku'],
                'stock'=>bcsub($item['coupon_stock'],$item['coupon_stock_used'],0),
                'expire_days'=>(int)$item['expire_days'],
                'active'=>$item['status'],
            ];
        }
        $response['code'] = 1;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = count($new_item);
        $response['data'] = $new_item;

        return $response;
    }

    public function allListFromRedis(){
        $coupon_ids = request()->all();
        if(is_array($coupon_ids) and count($coupon_ids)){

        }else{
            exit;
        }
        $data = RedisRules::getFromCacheByIds($coupon_ids);
        $new_item = [];
        foreach($data as $item){
            $expire_flag = 0;
            if( time() > strtotime($item['end_time']) or $item['status'] != 2 ){
                $expire_flag = 1;
            }
            $new_item[] = [
                'id'=>$item['id'],
                'status'=>$expire_flag,//0:未过期，1:已过期
                'price'=>$item['total_discount'],
                'require'=>$item['total_amount'],
                'start'=>strtotime($item['start_time']),
                'end'=>strtotime($item['end_time']),
                'desc'=>$item['name'],
                'coupon_type'=>$item['type'],
                'sku'=>$item['product_coupon_sku'],
                'stock'=>bcsub($item['coupon_stock'],$item['coupon_stock_used'],0),
                'expire_days'=>(int)$item['expire_days'],
                'active'=>$item['status'],
                'coupon_pic'=>$item['product_coupon_pic']??'',
                'product_coupon_name'=>$item['product_coupon_name']??'',
            ];
        }
        $response['code'] = 1;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = count($new_item);
        $response['data'] = $new_item;

        return $response;
    }

    //优惠券列表
    public function allList(){
//        return $this->allListFromDb();
        return $this->allListFromRedis();
    }
    
    function dataList()
    {
        $model = $this->getModel();
        $table = $model->getTable();
        
        $name = request('name');
        $limit = request('limit', 10);
        $page = request('page');
        
        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        
        $model = $this->addFilter($model);
        
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        $sql = $model->orderBy($table . '.id', 'desc')->toSql();
        $res = $model->orderBy($table . '.id', 'desc')
        ->paginate($limit)
        ->toArray();
        
        if (env('APP_DEBUG')) {
            $response['sql'] = $sql;
        }
        
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $res['data'];
        
        return $response;
    }
	
    //激活的有效的优惠券列表
    function activeList()
    {
        $model = $this->getModel();
        $table = $model->getTable();
        
        $name = request('name');
        $limit = request('limit', 10);
        $page = request('page');

        $model = $model->select(['id','name','status','start_time','end_time','coupon_stock','coupon_stock_used','total_amount','total_discount']);
        $model = $model->whereIn('type',['coupon','product_coupon']);
        $curr_time = date('Y-m-d H:i:s');
        $model = $model->where('status',2)->where('end_time','>',$curr_time);
        if($name){
            $model = $model->where('name','like','%'.$name.'%');
        }
        $res = $model->orderBy($table . '.id', 'desc')
        ->paginate($limit)
        ->toArray();
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $res['data'];
        
        return $response;
    }
	public function newMemberCoupon(){
	    $http = new Http();
	    $data = $http->curl('admin/config/coupon');
	    return $data;
	}

    public function getAllByAddSku(){
        $curr_time = date('Y-m-d H:i:s');
        $result = Cart::query()->where('status',2)
            ->select('id','name','type','add_sku')
            ->where('start_time','<',$curr_time)
            ->where('end_time','>',$curr_time)
            ->whereNotNull('add_sku')->where('add_sku','<>','')
            ->get();

        $response['code'] = 1;
        $response['msg'] = "获取规则列表成功.";
        $response['data'] = $result->toArray();
        return $response;
    }
}
