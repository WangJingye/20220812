<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Api\Controller;
use App\Model\Promotion\Cart;
use App\Model\Promotion\Code;
use App\Model\Promotion\Gift;
use App\Model\Promotion\Log;
use App\Model\Promotion\ProductDiscountPrice;
use App\Model\Promotion\RuleSaveValidation;
use Illuminate\Support\Facades\DB;
use App\Service\Redis\RedisRules;

class CartController extends Controller
{
    protected $_model = Cart::class;
    
    //检测输入的优惠码是否有效
    public function checkCode(){
        $params = request()->all();
        $input_code = $params['code_id']??'';
        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['code_code','=',$input_code],
        ];
        $codes = Cart::where($where)->whereRaw('code_stock-code_stock_used >0')->get()->toArray();
        if(!count($codes)){
            return ['code'=>2];//不存在
        }
        if($codes[0]['code_code'] != $input_code){
            return ['code'=>2];//区分大小写
        }
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
            ['code_code','=',$input_code],
        ];
        $count = Cart::where($where)->whereRaw('code_stock-code_stock_used >0')->count();
        if(!$count){
            return ['code'=>3];//不能用
        }      
        //运行一遍促销，看是否可以应用优惠码
        $carts = request()->all();
        if(!isset($carts['cartItems'])){
            return ['code'=>5];//参数错误 
        }
        $carts['order_can_used_max_points'] = 0;
        $carts['code_applied'] = 0;
        $cartService = new \App\Service\Rule($carts);
        $data = $cartService->apply();
        if(!$data['code_applied']){
            return ['code'=>4,
                'data'=>$data,
            ];//能够使用，但是跟会员折扣或者其它促销不能叠加
        }
        return ['code'=>1];//能够使用
        
    }

    //促销规则使用次数, 每次调用一个rule_id
    public function usedTime(){
        $cids = request()->all();
        if(is_array($cids) and count($cids)){
            $rules = Cart::whereIn('id',$cids)->get()->toArray();
            $gift = new Gift();
            $gift_table = $gift->getTable();
            $rule_table = (new Cart())->getTable();
            foreach($rules as $rule){
                $rule['gift_table'] = $gift_table;
                $rule['rule_table'] = $rule_table;
                DB::transaction(function () use ($rule){
                    if(substr($rule['type'],0,4) == 'code'){//优惠码
                        DB::update('update '.$rule['rule_table'].' set used_times=used_times+1,code_stock_used=code_stock_used+1 where id=? ',[$rule['id']]);
                    }else{
                        DB::update('update '.$rule['rule_table'].' set used_times=used_times+1 where id=? ',[$rule['id']]);
                    }
                    if(($rule['type'] == 'gift' or $rule['type'] == 'code_gift') and $rule['gift_id'] ){
                        DB::update('update '.$rule['gift_table'].' set used_qty=used_qty+1 where id=? ',[$rule['gift_id']]);
                    }
                });
            }
        }
        return ['code'=>1];
    }
    public function active(){
        $id = (int) request('id');
        $validate = new RuleSaveValidation();
        if(!$validate->statusCheck($id)){
            return ['code'=>0];
        }
        Cart::find($id)->update(['status'=>2]);
        //同步到redis缓存
        RedisRules::syncToCache($id,2);
        $product= new ProductDiscountPrice();
        $product->pushToDelayMq($id);
        $this->logPromotion('激活');
        return ['code'=>1];
    }
    public function unactive(){
        $id = (int) request('id');
        $userId = (int) request('userId');
        $userEmail  = request('userEmail');
        Cart::find($id)->update(['status'=>3]);
        //同步到redis缓存
        RedisRules::syncToCache($id,3);
        $this->logPromotion('禁用');
        return ['code'=>1];
    }
    public function messActive(){
        $ids = request('ids');
        $validate = new RuleSaveValidation();
        foreach($ids as $id){
            $id = (int)$id;
            if($validate->statusCheck($id)){
                Cart::find($id)->update(['status'=>2]);
                //同步到redis缓存
                RedisRules::syncToCache($id,2);
                $product= new ProductDiscountPrice();
                $product->pushToDelayMq($id);
                request()->offsetSet('id',$id);
                $this->logPromotion('激活');
            }
        }
        return ['code'=>1];
    }
    public function messUnactive(){
        $ids = request('ids');
        foreach($ids as $id){
            $id = (int)$id;
            Cart::find($id)->update(['status'=>3]);
            //同步到redis缓存
            RedisRules::syncToCache($id,3);
            request()->offsetSet('id',$id);
            $this->logPromotion('禁用');
        }
        return ['code'=>1];
    }
    private function logPromotion($actionType=''){
        $id = (int) request('id');
        $userId = (int) request('userId')??'';
        $userEmail  = request('userEmail')??'';
        $data = [
            'ruleId'=>$id,
            'userId'=>$userId,
            'userEmail'=>$userEmail,
            'actionType'=>$actionType,
        ];
        Log::create($data);
    }
    //时间是否有交集
    public function is_time_cross($beginTime1 = '', $endTime1 = '', $beginTime2 = '', $endTime2 = '') {
        $beginTime1 = strtotime($beginTime1);
        $endTime1 = strtotime($endTime1);
        $beginTime2 = strtotime($beginTime2);
        $endTime2 = strtotime($endTime2);
        $status = $beginTime2 - $beginTime1;
        if ($status > 0) {
            $status2 = $beginTime2 - $endTime1;
            if ($status2 >= 0) {
                return false;
            } else {
                return true;
            }
        } else {
            $status2 = $endTime2 - $beginTime1;
            if ($status2 > 0) {
                return true;
            } else {
                return false;
            }
        }
    }
    //商品列表页面，促销信息，只显示直接折扣信息
    //[{"model_id":"","cid":"xx","styleNumber":"xx"}]
    public function productList(){
        $data = request()->all();
        $product= new ProductDiscountPrice();
        return $product->productList($data);
    }
    //商品详情页面，促销信息，显示所有的促销信息
    //{"model_id":"","cid":"xx","styleNumber":"xx"}
    public function productDetail(){
        $data = request()->all();
        $product= new ProductDiscountPrice();
        return $product->productDetail($data);
    }
    //输入促销类型，返回矩阵中为Y的类型
    public function getPromotionArray($type){
        $promotion_type = [
            'product_discount'=>'直接折扣',
            'n_piece_n_discount'=>'多件多折',
            'full_reduction_of_order'=>'满减',
            'order_n_discount'=>'每满减',
            'gift'=>'赠品',
            'coupon'=>'满减券',
            'coupon_discount'=>'直接折扣-优惠券',
            'free_try'=>'试用装',
            'ship_fee_try'=>'付邮试用',
            'code_full_reduction_of_order'=>'满减优惠码',
            'code_order_n_discount'=>'每满减优惠码',
            'code_n_piece_n_discount'=>'多件多折优惠码',
            'code_gift'=>'赠品优惠码',
            'code_product_discount'=>'直接折扣优惠码',
        ];
        $origin_promotion_type = $promotion_type;
        unset($promotion_type[$type]);
        $product_discount = $promotion_type;
        unset($product_discount['n_piece_n_discount']);unset($product_discount['product_discount']);
        $n_piece_n_discount = $promotion_type;
        unset($n_piece_n_discount['product_discount']);unset($n_piece_n_discount['n_piece_n_discount']);
        $full_reduction_of_order = $promotion_type;
        unset($full_reduction_of_order['order_n_discount']);unset($full_reduction_of_order['full_reduction_of_order']);
        $order_n_discount = $promotion_type;
        unset($order_n_discount['full_reduction_of_order']);unset($order_n_discount['order_n_discount']);
        $code_type = $promotion_type;
        unset($code_type['code_full_reduction_of_order']);
        unset($code_type['code_order_n_discount']);
        unset($code_type['code_n_piece_n_discount']);
        unset($code_type['code_gift']);
        unset($code_type['code_product_discount']);
        $arr = [
            'product_discount'=>$product_discount,
            'n_piece_n_discount'=>$n_piece_n_discount,
            'full_reduction_of_order'=>$full_reduction_of_order,
            'order_n_discount'=>$order_n_discount,
            'coupon'=>$promotion_type,
            'coupon_discount'=>$promotion_type,
            'gift'=>$origin_promotion_type,
            'free_try'=>$promotion_type,
            'ship_fee_try'=>$promotion_type,
            'code_full_reduction_of_order'=>$code_type,
            'code_order_n_discount'=>$code_type,
            'code_n_piece_n_discount'=>$code_type,
            'code_gift'=>$code_type,
            'code_product_discount'=>$code_type,
        ];
        return array_keys($arr[$type]);
    }
    //获取当前状态激活，促销规则有效期重合，并且矩阵为Y的
    //是否为有相同款号或者相同材质
    public function getCrossRules(){
        $type = request('type');
        $start_time = request('start_time');
        $end_time = request('end_time');
        $curr_time = date('Y-m-d H:i:s');
        $id = request('id');
        $where = [
            ['end_time','>',$curr_time],
        ];
        $fields = ['id','name','start_time','end_time','type'];
        $rules = Cart::where($where)
                    ->whereIn('status',[1,2])
                    ->select(...$fields)
                    ->get()->toArray();
        $new_item = [];
        $cross_type = $this->getPromotionArray($type);
        foreach($rules as $rule){
            if($rule['id'] == $id){
                continue;
            }
            if(!in_array($rule['type'],$cross_type)){
                continue;
            }
            if($this->is_time_cross($start_time,$end_time,$rule['start_time'],$rule['end_time'])){
                unset($rule['start_time']);
                unset($rule['end_time']);
                $new_item[] = $rule;
            }
        }
        return $new_item;
    }
    public function codePost(){
        try {
            $id = request()->input('id');
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            //检查并过滤优惠码
            $data['code_code'] = \App\Service\Dlc\CartPromotionCode::checkAndGetCode();
            if(isset($data['gwp_skus'])){
                $data['gwp_skus'] = str_replace('，',',',$data['gwp_skus']);
                \App\Service\Dlc\Cart::checkItems($data['gwp_skus'],$prefix='不存在的赠品SKU');
            }
            if(request('cats')){
                $cats = implode(',',array_keys(request('cats')));
                $data['cids'] = $cats;
            }
            if(request('addrule')){
                $addrule = implode(',',array_keys(request('addrule')));
                $data['addrules'] = $addrule;
            }
            if(request('nn_n')){
                $data['nn_n'] = implode(',',request('nn_n'));
            }
            if(request('nn_discount')){
                $data['nn_discount'] = implode(',',request('nn_discount'));
            }
            if(request('total_amount')){
                $data['total_amount'] = implode(',',request('total_amount'));
            }
            if(request('total_discount')){
                $data['total_discount'] = implode(',',request('total_discount'));
            }
            if(request('step_amount')){
                $data['step_amount'] = implode(',',request('step_amount'));
            }
            if(request('step_discount')){
                $data['step_discount'] = implode(',',request('step_discount'));
            }
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);
            }
            return [
                'code' => 1,
                'msg' => '编辑成功',
                'data'=>$model->toArray()
            ];
        } catch (\Exception $e) {
            return ([
                'code' => 0,
                'msg' => $e->getMessage()
            ]);
        }
    }
    
    public function post()
    {
        $id = request()->input('id');
        if($id){
            $validate = new RuleSaveValidation();
            if(!$validate->statusCheckForSave($id)){
                return ['code'=>3,'msg'=>'禁止编辑'];
            }
        }
        
        $type = request()->input('type');
        $promotion_type = [
            'code_full_reduction_of_order',
            'code_order_n_discount',
            'code_n_piece_n_discount',
            'code_gift',
            'code_product_discount',
        ];
        if(in_array($type,$promotion_type)){
            return $this->codePost();
        }
        $id = request()->input('id');
        $validate = new RuleSaveValidation();
        $validate_result = $validate->check($id);
        if($validate_result !== true){
            return ['code'=>3,'msg'=>'与以下促销冲突,'.$validate_result];
        }
        try {
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            if(isset($data['gwp_skus'])){
                $data['gwp_skus'] = str_replace('，',',',$data['gwp_skus']);
                \App\Service\Dlc\Cart::checkItems($data['gwp_skus'],$prefix='不存在的赠品SKU');
            }
            if(isset($data['add_sku'])){
                $data['add_sku'] = str_replace('，',',',$data['add_sku']);
            }
            if(request('cats')){
                $cats = implode(',',array_keys(request('cats')));
                $data['cids'] = $cats;
            }else{
                $data['cids'] = '';
            }
            if(request('addrule')){
                $addrule = implode(',',array_keys(request('addrule')));
                $data['addrules'] = $addrule;
            }else{
                $data['addrules'] = '';
            }
            if(request('nn_n')){
                $data['nn_n'] = implode(',',request('nn_n'));
            }
            if(request('nn_discount')){
                $data['nn_discount'] = implode(',',request('nn_discount'));
            }
            if(request('total_amount')){
                $data['total_amount'] = implode(',',request('total_amount'));
            }
            if(request('total_discount')){
                $data['total_discount'] = implode(',',request('total_discount'));
            }
            if(request('step_amount')){
                $data['step_amount'] = implode(',',request('step_amount'));
            }
            if(request('step_discount')){
                $data['step_discount'] = implode(',',request('step_discount'));
            }
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);
            }
            return [
                'code' => 1,
                'msg' => '编辑成功',
                'data'=>$model->toArray()
            ];
        } catch (\Exception $e) {
            return ([
                'code' => 0,
                'msg' => $e->getMessage()
            ]);
        }
    }
    public function applyNew(){
        //styleNumber,款号；mid,材质,cid,类别
        //priceType,Y,计价
        $cartItems = [
            ['cart_item_id'=>'1', 'sku'=>'1','qty'=>2,'styleNumber'=>'1','mid'=>'50', 'priceType'=>'', 'price'=>'200','unit_price'=>'100','labourPrice'=>'','pro_type'=>'pro|member|point|auto','usedPoint'=>'5','discount'=>0,'maxUsedPoint'=>''],
            ['cart_item_id'=>'2','sku'=>'11','qty'=>2,'styleNumber'=>'1','mid'=>'1', 'priceType'=>'', 'price'=>'1888','unit_price'=>'200','labourPrice'=>'','pro_type'=>'pro|member|point','usedPoint'=>'','discount'=>0,'maxUsedPoint'=>''],
            ['cart_item_id'=>'3','sku'=>'12','qty'=>2,'styleNumber'=>'1','mid'=>'1', 'priceType'=>'', 'price'=>'3888','unit_price'=>'300','labourPrice'=>'','pro_type'=>'pro|member|point','usedPoint'=>'','discount'=>0,'maxUsedPoint'=>''],
        ];
        $carts = [
            'cartItems'=>$cartItems,
            'coupon_id'=>'',//只有一个优惠券，如果给多个，就自动选择出最优的
            'member_coupon_list'=>'1,3,35',//顾客有效优惠券列表
            'code'=>'X7YEM8',//3BBTTY只有一个优惠码
            'code_applied'=>'0',//code是否应用成功
            'is_member'=>'0,1',//0,1,是否会员
            'total_points'=>'300',//顾客的总悦享钱
            'used_points'=>'0',//订单使用的积分
            'order_can_used_max_points'=>'0',//订单能够使用的最大悦享钱,
            'auto'=>'',//0,1,自动应用悦享钱,按照最高价格排序，把悦享钱用光
            'combile'=>'1',//0,1手动选择item的方式
            'highest_used'=>'',//0,1,使用最高悦享钱
            'page'=>'',//cart,order,所在页面
            'from'=>'',
        ];
        $carts = request()->all();
        $carts['order_can_used_max_points'] = 0;
        $carts['code_applied'] = 0;
        $cartService = new \App\Service\Rule($carts);
        $data = $cartService->apply();
               
        return $data;
    }
    
	public function apply(){
	    
	    return [];
	}
		
	function get(){
	    $id = request('id');
	    $item = $this->getModel()->find($id);
	    $ruleType=DB::table('rule_type')->pluck('label','key');
	    $curr_time = date('Y-m-d H:i:s');
	    $giftList=DB::table('gift')->where('status',2)->pluck('name','id');
	    
	    $item['rule_type_options']=$ruleType;
	    $item['gift_list']=$giftList;
	    if($item){
	        return $this->success($item, '获取成功');
	    }else{
	        return $this->error('获取失败');
	    }
	}
	
	protected  function addFilter($model){
	    $label = request('name');
	    $start_time = request('start_time');
	    $end_time  = request('end_time');
	    $status = request('status');
	    $type = request('type');
	    if($label){
	        $model = $model->where('name','like','%'.$label.'%');
	    }
	    if($type and $type != '-1'){
	        $model = $model->whereIn('type',[$type]);
	    }
	    if($status and $status != '-1'){
	        $curr_time = date('Y-m-d H:i:s');
	        if($status == '1'){
	            $model = $model->where('status','=',$status);
	            $model = $model->where('end_time','>',$curr_time);//未过期的才算，未激活
	        }
	        if($status == '2'){
	            $model = $model->where('status','=',$status);
	            $model = $model->Where('end_time','>',$curr_time);//未过期的才算，激活的
	        }
	        if($status == '3'){
	            $model = $model->WhereRaw('(status =3 or end_time < ?)',[$curr_time]);//禁用的，包括已经过期的
	        }
	    }
	    if($start_time and $end_time){//开始和结束时间都有
	        $model = $model->whereRaw('((start_time > ? and start_time < ?) or ( end_time > ? and end_time < ? ) or (start_time < ? and end_time > ? ) )',[$start_time,$end_time,$start_time,$end_time,$start_time,$end_time]);
	    }
	    if($start_time and !$end_time){//只有开始时间
	        $model = $model->where('end_time','>',$start_time);
	    }
	    if(!$start_time and $end_time){//只有结束时间
	        $model = $model->where('start_time','<',$end_time);
	    }
	    return $model;
	}
	
	function type(){
	    $data= DB::table('rule_type')->pluck('label','key');
	    return $this->success($data);
	}
	//
	public function dataList()
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
	    $res = $model->orderBy($table . '.id', 'desc')
	    ->paginate($limit)
	    ->toArray();
	    $promotion_type = [
	        'product_discount'=>'直接折扣',
	        'n_piece_n_discount'=>'多件多折',
	        'full_reduction_of_order'=>'满减',
	        'order_n_discount'=>'每满减',
	        'gift'=>'赠品',
            'coupon'=>'满减券',
            'coupon_discount'=>'直接折扣-优惠券',
            'product_coupon'=>'随单礼券',
            'free_try'=>'试用装',
            'ship_fee_try'=>'付邮试用',
	        'code_full_reduction_of_order'=>'满减优惠码',
	        'code_order_n_discount'=>'每满减优惠码',
	        'code_n_piece_n_discount'=>'多件多折优惠码',
	        'code_gift'=>'赠品优惠码',
	        'code_product_discount'=>'直接折扣优惠码',
	    ];
	    $new_item = [];
	    foreach($res['data'] as $item){
	        $type = $item['type'];
	        $item['datetime_period'] = $item['start_time'] .'-' . $item['end_time'];
	        $item['type_label'] = $promotion_type[$type]??'';
	        $status = '待激活';
	        $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="active">激活</a>';
	        if($item['status'] ==2){
	            $status = '激活';
	            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="unactive">禁用</a>';
                if(in_array($item['type'],['coupon','product_coupon'])){
                    $action .= '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="send_coupon">发券</a>';
                }
	        }
	        if(time()>strtotime($item['end_time']) ){//过期了
	            $status = '禁用';
	            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a>';
	        }
	        if($item['status'] == 3){//禁用
	            $status = '禁用';
	            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a>';
	        }
	        $item['status'] = $status;
	        $item['action'] = $action;
	        $new_item[] = $item;
	    }
	    $response['code'] = 0;
	    $response['msg'] = "获取规则列表成功.";
	    $response['count'] = $res['total'];
	    $response['data'] = $new_item;
	    
	    return $response;
	}
	public function destroy()
	{
	    return $this->success([], '不能成功');
	}

}
