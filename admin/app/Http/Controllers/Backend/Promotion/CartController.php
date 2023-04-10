<?php

namespace App\Http\Controllers\Backend\Promotion;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
{
    
    public $product_type = [
        'GA'=>'计价足金饰品',
        'PA'=>'计价铂金饰品',
        'GB'=>'金条/金片',
        'GI'=>'生生金宝（金片）',
        'GF'=>'定价足金饰品',
        'PF'=>'定价铂金饰品',
        'MP'=>'固定价复合贵金属饰品',
        'DF'=>'固定价钻石饰品',
        'DI'=>'钻石饰品',
        'XF'=>'固定价宝石饰品',
        'GS'=>'宝石饰品',
        'QF'=>'固定价半宝石饰品',
        'SS'=>'半宝石饰品',
        'TF'=>'固定价珍珠饰品',
        'PL'=>'珍珠饰品',
        'FJ'=>'K金饰品',
        'SF'=>'纯银饰品',
        'CHARME'=>'CHARME手绳',
        
    ];
    public $type = ['full_reduction_of_order'=>'满减',
        'order_n_discount'=>'每满减',
        'n_piece_n_discount'=>'多件多折',
        'gift'=>'赠品',
        'coupon'=>'满减券',
        'coupon_discount'=>'直接折扣-优惠券',
        'product_coupon'=>'随单礼券',
        'free_try'=>'试用装',
        'ship_fee_try'=>'付邮试用',
        'code_full_reduction_of_order'=>'满减-优惠码',
        'code_order_n_discount'=>'每满减-优惠码',
        'code_n_piece_n_discount'=>'多件多折-优惠码',
        'code_gift'=>'赠品-优惠码',
        'product_discount'=>'直接折扣',
        'code_product_discount'=>'直接折扣-优惠码',
    ];
    
    
    
    function __construct(){
        parent::__construct();
    }
    
    public function getCrossRules(){
        $response = $this->curl('promotion/cart/getCrossRules',request()->all());
        return $response;
    }
    public function index(Request $request){
        return view('backend.promotion.cart.index');
    }
    
    public function dataList(){
        $response = $this->curl('promotion/cart/dataList',request()->all());
        return $response;
    }
    
    public function edit(Request $request){
        $id= request('id');
        $detail = $this->curl('promotion/cart/get',request()->all())['data']??[];
        $type = $this->type;
        $getType= $detail['type']??request('type');
        $detail['getType'] = ['type'=>$getType,'name'=>$type[$getType]];
        $categoryData= $this->getProductType();
        $new_item = [];
        foreach($categoryData as $k=>$v){
            $cat = ['id'=>$k,'name'=>$v];
            $new_item[] = $cat;
        }
        $categoryData = $new_item;
        $detail['cids_arr']  = [];
        if($id){
            $cids_arr = explode(',',$detail['cids']);
            $detail['cids_arr'] = $cids_arr;
            $detail['addrules'] = $detail['addrules']?explode(',',$detail['addrules']):[];
            $detail['nn_n'] = $detail['nn_n']?explode(',',$detail['nn_n']):[];
            $detail['nn_discount'] = $detail['nn_discount']?explode(',',$detail['nn_discount']):[];
            $detail['total_amount'] = $detail['total_amount']?explode(',',$detail['total_amount']):[];
            $detail['total_discount'] = $detail['total_discount']?explode(',',$detail['total_discount']):[];
            $detail['step_amount'] = $detail['step_amount']?explode(',',$detail['step_amount']):[];
            $detail['step_discount'] = $detail['step_discount']?explode(',',$detail['step_discount']):[];
        }
        $detail['type_list'] = [
            'code_product_discount'=>'直接折扣-优惠码',
            'code_n_piece_n_discount'=>'多件多折-优惠码',
            'code_full_reduction_of_order'=>'满减-优惠码',
            'code_order_n_discount'=>'每满减-优惠码',
            'code_gift'=>'赠品-优惠码',
        ];
        $detail['coupon_type_list'] = [
            'coupon'=>'满减券',
//            'coupon_discount'=>'直接折扣-优惠券',
        ];
        if(in_array($getType,['full_reduction_of_order','order_n_discount'])){
            $detail['getType']['is_cut'] = 'yes';
        }
        if(substr($detail['getType']['type'],0,4) == 'code'){
            return view('backend.promotion.code.edit',['detail'=>$detail,'categoryData'=>$categoryData,]);
        }elseif(substr($detail['getType']['type'],0,6) == 'coupon'){
            return view('backend.promotion.coupon.edit',['detail'=>$detail,'categoryData'=>$categoryData,]);
        }
        return view('backend.promotion.cart.edit',['detail'=>$detail,'categoryData'=>$categoryData,]);
    }

    private function convertTreeToFlatArray($tree,$arr=[]){
        foreach($tree as $item){
            $id = $item['id'];
            $name = $item['label'];
            $arr[$id] = $name;
            if(isset($item['children']) and is_array($item['children'])){
                $arr = $this->convertTreeToFlatArray($item['children'],$arr);
            }
        }
        return $arr;
    }
    private function getProductType(){
        $tree = $this->curl('goods/product/getCategoryTree');
        $product_type = $this->convertTreeToFlatArray($tree['data']);
        return $product_type;
    }
    
    public function view(Request $request){
        $id= request('id');
        $detail = $this->curl('promotion/cart/get',request()->all())['data']??[];
        $type = $this->type;
        $getType= $detail['type']??request('type');
        $detail['getType'] = ['type'=>$getType,'name'=>$type[$getType]];
        $categoryData= $this->getProductType();
        $new_item = [];
        foreach($categoryData as $k=>$v){
            $cat = ['id'=>$k,'name'=>$v];
            $new_item[] = $cat;
        }
        $categoryData = $new_item;
        $detail['cids_arr']  = [];
        if($id){
            $cids_arr = explode(',',$detail['cids']);
            $detail['cids_arr'] = $cids_arr;
            $detail['addrules'] = $detail['addrules']?explode(',',$detail['addrules']):[];
            $detail['nn_n'] = $detail['nn_n']?explode(',',$detail['nn_n']):[];
            $detail['nn_discount'] = $detail['nn_discount']?explode(',',$detail['nn_discount']):[];
            $detail['total_amount'] = $detail['total_amount']?explode(',',$detail['total_amount']):[];
            $detail['total_discount'] = $detail['total_discount']?explode(',',$detail['total_discount']):[];
            $detail['step_amount'] = $detail['step_amount']?explode(',',$detail['step_amount']):[];
            $detail['step_discount'] = $detail['step_discount']?explode(',',$detail['step_discount']):[];
        }
        $detail['type_list'] = [
            'code_product_discount'=>'直接折扣-优惠码',
            'code_n_piece_n_discount'=>'多件多折-优惠码',
            'code_full_reduction_of_order'=>'满减-优惠码',
            'code_order_n_discount'=>'每满减-优惠码',
            'code_gift'=>'赠品-优惠码',
        ];
        $detail['coupon_type_list'] = [
            'coupon'=>'满减券',
            'coupon_discount'=>'直接折扣-优惠券',
        ];
        if(in_array($getType,['full_reduction_of_order','order_n_discount'])){
            $detail['getType']['is_cut'] = 'yes';
        }
        if(substr($detail['getType']['type'],0,4) == 'code'){
            return view('backend.promotion.code.view',['detail'=>$detail,'categoryData'=>$categoryData,]);
        }elseif(substr($detail['getType']['type'],0,6) == 'coupon'){
            return view('backend.promotion.coupon.view',['detail'=>$detail,'categoryData'=>$categoryData,]);
        }
        return view('backend.promotion.cart.view',['detail'=>$detail,'categoryData'=>$categoryData,]);
    }
    
    public function post(){
        $response=$this->curl('promotion/cart/post',request()->all());
        return $response;
    }
    //激活
    public function active(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response=$this->curl('promotion/cart/active',$data);
        return $response;
    }
    public function unactive(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response=$this->curl('promotion/cart/unactive',$data);
        return $response;
    }

    public function messActive(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response=$this->curl('promotion/cart/messActive',$data);
        return $response;
    }
    public function messUnactive(){
        $data = request()->all();
        $data['userId'] = auth()->user()->id;
        $data['userEmail'] = auth()->user()->email;
        $response=$this->curl('promotion/cart/messUnactive',$data);
        return $response;
    }
    
    public function destroy(){
        return ['0'];
        $response=$this->curl('promotion/cart/destroy',request()->all());
        if($response['code']){
            $userId=auth()->user()->id;
            $module="购物车促销规则";
            $content=json_encode(request()->all());
            $action='删除';
            $key=request('id');
            DB::table('report_log')->insert([
                'user_id' => $userId,
                'module' => $module,
                'action'=>$action,
                'key'=>$key,
                'content' => $content
            ]);
        }
        return $response;
    }
    
    public function _export(){
        ini_set('max_execution_time', '0');
        $list = $this->curl('promotion/cart/export')['data']??[];
        $filename = 'promotion_cart_'.time().'.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$filename );
        header('Cache-Control: max-age=0'); // 禁止缓存
        $excel = new \App\Lib\PhpExcel();
        $excel->make($list);
        exit;
    }

}
