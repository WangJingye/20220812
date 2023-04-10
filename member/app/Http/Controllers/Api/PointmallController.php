<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Model\Wbs;
use App\Service\CrmUsersService;
use App\Support\Token;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Validator;


class PointmallController extends Controller
{

    const STATUS_ACTIVE = 'active';
    const STATUS_PAYED = 'pay_success';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_EXPIRE = 'expire';

    protected $_model = Wbs::class;
    protected $channel_id;
    protected $posId=false;

    function __construct()
    {
        parent::__construct();
        //$this->user_id=316762;
        $this->channel_id = request()->header('from', 1);
        $posId = $this->getUserPosId();
        if($posId){
            $this->posId = $this->getUserPosId();
        }

    }

    function token()
    {
        return Token::createToken('316765');
    }

    /*
     {
            "code":1,
            "message":"success",
            "data":[
                {
                    "id":569,
                    "payment_type":"1",
                    "total_amount":"1020.0000",
                    "order_sn":"2008062300000237",
                    "order_status":1,
                    "order_state":1,
                    "coupon_id":0,
                    "order_data_item":[
                        {
                            "id":1248,
                            "order_main_id":569,
                            "name":"希思黎保湿眼唇凝露"
                        }
                    ]
                }
            ]
        }
     *  @param $orderSn
     */
    function getOmsOrder($orderSn){

//        $mock = '{
//            "code":1,
//            "message":"success",
//            "data":[
//                {
//                    "id":569,
//                    "payment_type":"1",
//                    "total_amount":"1020.0000",
//                    "order_sn":"2008062300000237",
//                    "order_status":1,
//                    "order_state":1,
//                    "coupon_id":0,
//                    "order_data_item":[
//                        {
//                            "id":1248,
//                            "order_main_id":569,
//                            "name":"希思黎保湿眼唇凝露"
//                        }
//                    ]
//                }
//            ]
//        }';
//
//        return json_decode($mock,true)['data'];
        $orderApi= $this->http->curl('orders/details',['order_sn'=>$orderSn]);
        if(isset($orderApi['code']) && $orderApi['code']==1){
            return $orderApi['data'];
        }else{
            return $this->error('oms接口orders/details错误');
        }
    }

    function revertCoupon($userId,$couponId){
        return $this->http->curl('delCoupon',['user_id'=>$userId,'coupon_id'=>$couponId]);
    }

    function doGoodStock($sku, $channel, $type)
    {
        $prams = ['sku_json' => json_encode([[$sku, 1, '积分商城']]), 'channel_id' => $channel, 'increment' => $type];
        $convertApi = $this->http->curl('outward/update/batchStock', $prams);
        return $convertApi;
    }

    function getCoupon($couponId)
    {

//        $mock ='{
//    "code":1,
//    "msg":"获取规则列表成功.",
//    "count":4,
//    "data":[
//        {
//            "id":1,
//            "status":1,
//            "price":"11",
//            "require":"111",
//            "start":1593532800,
//            "end":1596124800,
//            "desc":"优惠金额100",
//            "coupon_type":"coupon"
//
//
//        },
//        {
//            "id":2,
//            "status":1,
//            "price":"11",
//            "require":"111",
//            "start":1593532800,
//            "end":1596124800,
//            "desc":"优惠金额200",
//            "coupon_type":"coupon"
//        },
//        {
//            "id":3,
//            "status":1,
//            "price":"1",
//            "require":"11",
//            "start":1593532800,
//            "end":1596124800,
//            "desc":"商品109100",
//            "coupon_type":"product_coupon",
//            "sku":"109100"
//        },
//        {
//            "id":4,
//            "status":1,
//            "price":"11",
//            "require":"111",
//            "start":1593532800,
//            "end":1596124800,
//            "desc":"商品152103",
//            "coupon_type":"product_coupon",
//            "sku":"152103"
//        }
//    ]
//}';
//        return json_decode($mock,true);
        return $this->http->curl('promotion/cart/productList', $couponId);
    }

    function getProduct($skus)
    {
        return $this->http->curl('outward/product/getProductInfoBySkuIds', ['sku_ids' => $skus, 'from' => $this->channel_id]);
    }

    function getUserPosId()
    {
        return DB::table('tb_users')->where('id', $this->user_id)->value('pos_id');
    }


    function getRedis(){
        $redis = new \Redis();
        $config=config('database.redis.default');
        $redis->connect($config['host'], $config['port'], 10);
        $redis->auth($config['password']);
        $redis->select($config['database']);
        return $redis;
    }


    function list(){

        $redis=$this->getRedis();
        $cacheKey = 'porintmall:list';
        $cacheLock = 'porintmall:lock';
        $random = md5(time());
        $ttl =10 * 60;
        $on = false;
        $result = $redis->get($cacheKey);
        if($result && $on){
            $canFlashCache = $redis->set($cacheLock, $random, array('nx', 'ex' => $ttl));
            if ($canFlashCache) {
                $redis->set($cacheKey,json_encode($this->_list()));
//                if ($redis->get($cacheLock) == $random) {
//                    $redis->del($cacheLock);
//                }
            }
            return json_decode($result,true);
        }else{
            $result =$this->_list();
            $redis->set($cacheKey,json_encode($result));
            $canFlashCache = $redis->set($cacheLock, $random, array('nx', 'ex' => $ttl));
            return $result;
        }



    }

    function _list()
    {

        $list = DB::table("wbs")->where(
            [
                ['status', 1]
            ]
        )->get();

        $couponIds = $list->pluck('coupon_id')->toArray();
        $couponsArray=$this->getCouponArray($couponIds);
        $list->map(function ($item)use($couponsArray) {
                if(!isset($couponsArray[$item->coupon_id])){
                    $item->coupon = false;
                }else{
                    $coupon = $couponsArray[$item->coupon_id];
                    $item->coupon = $coupon;
                    return $item;
                }
        });

        $list = $list ->toArray();


        $list = array_values(array_filter($list,function($item){
            return $item->coupon != false;
        }));







        return [
            'code'=>1,
            'message'=>'success',
            'data'=>$list
        ];
    }

    public function Sisley_ChangePoints($params)
    {

        //pc, mobile,miniapp
        $url = config('crm.crm_domain') . '/Sisley_ChangePoints';
        $client =new \GuzzleHttp\Client();
        $form_params = [
            'CustomerSID' => $params['CustomerSID'],
            'ChangePoints' => $params['ChangePoints'],
            'source' => $params['source'],
            'remarks' => $params['remarks'],
        ];

        $response = $client->request('post', $url, ['form_params' => $form_params]);
        $code = $response->getStatusCode();

        $data=[];
        if ($code == 200) {
            $contents = $response->getBody()->getContents();
            loger(['request'=>$params,'response'=>$contents],'Sisley_ChangePoints');
            $xml = simplexml_load_string($contents);
            $jsonStr = json_encode($xml);
            $data = json_decode($jsonStr,true);
            $message = json_decode($data[0],true);
            return $message;
        }
        return ['Code'=>0,"Msg"=>"扣减积分返回异常"];

    }

    function convert()
    {
        if ($this->user_id == 0) {
            return $this->error('user_id没有值');
        }
        $fields = [
            'wbs_id' => 'required',
        ];


        $validator = Validator::make(request()->all(), $fields);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $wbs_id = request('wbs_id');
        $wbs = DB::table('wbs')->where('status', '1')->find($wbs_id);
        if (empty($wbs)) {
            return $this->error('商品已经下架');
        }

        $coupons=$this->getCouponArray([$wbs->coupon_id]);
        if(isset($coupons[$wbs->coupon_id])){
            $coupon= $coupons[$wbs->coupon_id];
        }else{
            return $this->error('兑换获取优惠劵接口错误');
        }
        $wbs->coupon=$coupon;

        if($coupon['coupon_type']=='product_coupon'){
            if(!isset($coupon['product']['stock'])){
                return $this->error('商品没有库存',$coupon);
            }
            if($coupon['product']['stock']<1){
                return $this->error('商品没有库存',$coupon);
            }
        }

        if($coupon['stock']<1){
                return $this->error('优惠劵没有库存');
        }

        if(empty($this->posId)){
            return $this->error('posId没有值');
        }

        request()->offsetSet('coupon_id', $wbs->coupon_id);
        request()->offsetSet('type', '2');
        $doActon = new CouponController(request());
        $convertApi = (Array)$doActon->action(request())->getData();
        //product_coupon：kendo商品卷库存下单的时候处理

        loger([
            'request'=>[
                'url'=>"CouponController::action",
                'coupon_id'=>$wbs->coupon_id,
                'type'=>'2',
                'user_id'=>$this->user_id,
            ],
            'response'=>$convertApi
            ],
            'couponController.action');

        //领卷成功，扣积分
        if ( isset($convertApi['code']) &&   $convertApi['code']== 1) {
            //扣减积分
            $desc = $coupon['desc'];
            if($coupon['coupon_type']=='coupon'){
                $desc='优惠礼券满'.$coupon['require'].'减'.$coupon['price'];
            }
            if($coupon['coupon_type']=='product_coupon'){
                $productName = $coupon['product']['name']??$desc;
                $desc='实物礼券'.$productName;
            }
            $params=[
                'CustomerSID'=>$this->posId,
                'ChangePoints'=>"-".$wbs->exchange_point,
                'source'=>"",
                'remarks'=>sprintf('用户POS ID ：%s，兑换了：%s，消耗了：%s积分',$this->posId,$desc,$wbs->exchange_point),
            ];
            $crmApi = $this->Sisley_ChangePoints($params);
            if(!isset($crmApi['Msg'])){
                //退卷操作
                $this->revertCoupon($this->user_id,$wbs->coupon_id);
                return $this->error("扣减积分接口没有Msg信息");
            }
            if (isset($crmApi['Msg']) && $crmApi['Msg'] != 'Success') {
                //退卷操作
                $this->revertCoupon($this->user_id,$wbs->coupon_id);
                return $this->error($crmApi['Msg']);
            }

        }else{
            return $this->error('处理优惠劵接口失败',$convertApi);
        }

        $user_coupon_id="";
        if(isset($convertApi['data']->id)){
            $user_coupon_id=$convertApi['data']->id;
        }
        if($crmApi['Msg'] == 'Success' && $convertApi['code']==1){
            $wbs_id = request('wbs_id');
            $data = [
                'user_id' => $this->user_id,
                'user_coupon_id'=>$user_coupon_id,
                'wbs_id' => $wbs_id,
                'marker' => json_encode($wbs),
                'coupon_id'=>$wbs->coupon['id'],
                'coupon_type'=>$wbs->coupon['coupon_type']
            ];
            $id = DB::table('wbs_user_point')->insertGetId($data);
            return $this->success('积分兑换成功', ['id' => $id, 'data' => $data]);
        }else{
            return $this->error(sprintf("失败：crm:%s,商城:%s",$crmApi['Msg'],$convertApi["message"]));
        }

    }


    function getCouponArray($ids){
        $couponIds = array_unique($ids);
        $coupons = [];
        if (count($couponIds) > 0) {
            $coupons = $this->getCoupon($couponIds);
            if (isset($coupons['code']) && $coupons['code'] == 1) {
                $coupons = $coupons['data'];

                $skus =array_column($coupons,'sku');
                $productArray=$this->getProductArray($skus);
                if(count($coupons)){
                    $coupons= array_map(function ($item)use($productArray){
                           if($item['status']!=0){
                               //throw  new \Exception(sprintf('coupon status error coupon_id:%s',$item['id']));
                               return false;
                           }
                           $sku=$item['sku']??"";
                           $productApi = [];
                           $product=[];
                           if(($sku)){
                               $productApi=$productArray[$sku]??[];
                           };
                            if ($productApi) {
                                $product = [
                                    'name' => $productApi['product_name'],
                                    'image' => $productApi['kv_image'],
                                    'stock' => $productApi['skus'][0]['stock']??$productApi['min_stock']
                                ];
                                $item['product'] = $product;
                            }
                            return $item;
                    },$coupons);
                    $coupons=array_column(array_filter($coupons),null,'id');
                }
            } else {
                throw  new \Exception('coupon api error!');
            }
        }
        return $coupons;
    }

    function getProductArray($ids){
        $productIds = array_filter(array_unique($ids));
        $productsArray=[];
        $products=[];
        if (count($productIds) > 0) {
            $productsApi = $this->getProduct(join(',', $productIds));
            if (isset($productsApi['code']) && $productsApi['code'] == 1) {
                $products = $productsApi['data'];
            } else {
                throw  new \Exception('product api error');
            }

            if(count($productIds) != count($products)){
                throw  new \Exception(sprintf('product api response count not match,params count:%s,api count:%s',json_encode($productIds),count($products)));
            }

            return $products;
        }

    }


    function myconvert()
    {

        $page = request('page');
        $userId = request('user_id', $this->user_id);
        if (empty($userId)) {
            return $this->error('user_id没有值');
        }


        $list = DB::table("wbs_user_point")
                ->where('user_id',$this->user_id)
                ->get()->map([$this,'getStatus']);
        return $this->success('成功',['list'=>$list]);
    }

    function getStatus($wbs){
        $coupon = json_decode($wbs->marker,true)['coupon'];
        if($coupon['expire_days']){
            $expireTime =  date("Y-m-d H:i:s",strtotime(sprintf("%s +%s day",$wbs->created_at,$coupon['expire_days'])));
            $wbs->expire_time= $expireTime;
        }
        $status='';
        if($wbs->coupon_type=='coupon'){

            if($wbs->order_id){
                $status="已使用";
            }else{
                $status = $this->couponExpire($wbs);
            }
        }
        if($wbs->coupon_type=='product_coupon'){
            $status = '';
            if($wbs->order_id){
                $order = $this->getOmsOrder($wbs->order_id)[0];
                $orderStatus = $order['order_status'];
                $orderState = $order['order_state'];
                if($orderState<8){
                    $status = '已使用';
                }
                if($orderState>=8){
                    $status = '随单已发货';
                }
                //$status=$order['status_name']." ".$order['state_name'];
            }else{
                $status = $this->couponExpire($wbs);
            }

        }
        $wbs->status = $status;
        return $wbs;
    }


    function couponExpire($wbs){
        $date=new \DateTime($wbs->created_at, new \DateTimeZone('+8'));
        $expire_days=json_decode($wbs->marker,true)['expire_days']??7;
        $date->sub(new \DateInterval('P'.$expire_days.'D'));//天
        if(date("Y-m-d H:i:s") < $date->format('Y-m-d H:i:s')){
            $status = '已过期';
        }else{
            $status = '未使用';
        }
        return $status;
    }

    function paysuccess()
    {


        $fields = [
            'coupon_id' => 'required',
            'order_id' => 'required',
            'user_id'=>'required'
        ];
        $params = request()->all();
        $validator = Validator::make($params, $fields);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = DB::table('wbs_user_point')->where(
            [
                ['coupon_id', $params['coupon_id']],
                ['user_id', $params['user_id']],
                ['order_id', ""],

            ]
        )->update([
            'order_id' => $params['order_id']
        ]);
        if ($result) {
            return $this->success('成功');
        } else {
            return $this->error('失败');
        }
    }

    function cancelOrder()
    {
        if ($this->user_id == 0) {
            return $this->error('user_id没有值');
        }

        $fields = [
            'coupon_id' => 'required',
            'order_id' => 'required',
            'user_id' => 'required'
        ];
        $params = request()->all();
        $validator = Validator::make($params, $fields);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $result = DB::table('wbs_user_point')->where(
            [
                ['coupon_id', $params['coupon_id']],
                ['user_id', $params['user_id']],
                ['order_id', $params['order_id']],

            ]
        )->update([
            'order_id' => ""
        ]);
        if ($result) {
            return $this->success('成功');
        } else {
            return $this->error('失败');
        }
    }


    protected function addFilter($model)
    {
        return $model;
    }

    function dataList()
    {
        // return ['数据库33344：'=>env('DB_DATABASE')];
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
        // return [get_class($model),get_class_methods($model)];
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

    function get()
    {
        $id = request('id');
        $item = $this->getModel()->find($id);
        if ($item) {
            return $this->success('获取成功', $item);
        } else {
            return $this->error('获取失败');
        }
    }

    function post()
    {
        try {
            $id = request()->input('id');;
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);;
            }
            return [
                'code' => 1,
                'msg' => '编辑成功',
                'data' => $model->toArray()
            ];
        } catch (\Exception $e) {
            return ([
                'code' => 0,
                'msg' => $e->getMessage()
            ]);
        }
    }

    protected function getModel()
    {
        return new $this->_model();
    }





}