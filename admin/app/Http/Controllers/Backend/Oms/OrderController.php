<?php

namespace App\Http\Controllers\Backend\Oms;

use App\Http\Controllers\Backend\Controller;
use http\QueryString;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $action = $this;
        return view('backend.oms.index', compact('list', 'action'));
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $action = $this;
        return view('backend.oms.index', compact('list', 'action'));
    }

    public function _export(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $all['limit'] = 5000;
        $all['type'] = 2;


        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $pay_list = [
            '',
            '支付宝',
            '微信',
            '银联',
            '花呗',
            '货到付款'
        ];
        $trade_type = [
            7 => '微信扫码',//微信扫码
            8 => 'H5支付',//h5 支付
            1 => '微信网页支付',//微信支付
            6 => '小程序支付',//小程序
        ];
        $customer_type = ['新客', '银卡', '金卡', '白金卡', '老客', '贵宾'];
        $virtual_prod_prefix = 'VIRTUAL_';
        foreach ($list as $order) {
            if (!isset($trade_type[$order['trade_type']])) {
                $trade_type[$order['trade_type']] = '';
            }
            $is_exception = '';
            if ($order['is_exception']==1) {
                $is_exception = '异常';
            }
            $unreal_sku = [];
            foreach ($order['order_data_item'] as $goods) {
                //增加套装子商品的折后价格>>>>>
                if((strpos($goods['sku'],'UNREAL_') === 0)){
                    $unreal_sku[$goods['sku']] = $goods;
                }
                if(!empty($goods['main_sku']) && $goods['original_price']>0 && array_key_exists($goods['main_sku'],$unreal_sku)){
                    $unreal_item = $unreal_sku[$goods['main_sku']];
                    $goods['order_amount_total'] = sprintf("%.2f", ($goods['original_price']/$unreal_item['original_price'])*$unreal_item['order_amount_total']);
                    $goods['discount'] = sprintf("%.2f",$goods['original_price']-$goods['order_amount_total']);
                    $unreal_item = null;
                }
                //<<<<<<<<<<<<<<<<<<<<<<<<

                $free_name = '';
                $gift_name = '';

                if ($order['order_status'] <= 2 || $order['payment_type'] == 5) {
                    $order['transaction_time'] = '';
                }
                $return_pay_at = '';
                if ($order['order_status'] == 7 && !isset($order['order_after_sale']['return_pay_at'])) {
                    $return_pay_at = $order['return_pay_at'];
                }
                if (isset($order['order_after_sale']['return_pay_at'])) {
                    $return_pay_at = $order['order_after_sale']['return_pay_at'];
                }
                $return_at = '';
                if (isset($order['order_after_sale']['return_at'])) {
                    $return_at = $order['order_after_sale']['return_at'];
                }


                if ($goods['is_free'] == 1) {
                    $free_name = '小样';
                }
                if ($goods['is_free'] == 2) {
                    $free_name = '优惠卷兑换';
                }
                if ($goods['is_gift']) {
                    $gift_name = '赠品';
                }
                $status_name = '';
                if ($goods['status'] == 2) {
                    $status_name = '售后中';
                }
                if ($goods['status'] == 3) {
                    $status_name = '已退回';
                }

                $goods_type = ['', '普通', '组合套装', '固定套装'];

                if ($order['order_type'] == 1) {
                    $type_name = '普通';
                } else {
                    $type_name = '付邮试用';
                }
                if ($goods['original_price'] > 0) {
                    $rate = (number_format($goods['order_amount_total'] / $goods['original_price'], 2) * 100) . '%';//折扣率

                } else {
                    $rate = 0;
                }
//
//                $goods['applied_rule_ids'],//促销信息
//                    $goods['applied_ids'],//促销信息
                $coupon = '';
                $code = '';
                $applied_ids = '';
                $applied_rule = '';
                if ($goods['applied_rule_ids']) {
//                    $goods['applied_rule_ids'] = str_replace(",", "-", $goods['applied_rule_ids']);
                    $arr = json_decode($goods['applied_rule_ids'], true);
                    if(is_array($arr)){
                        foreach ($arr as $v) {
                            $v['extend_name'] = $v['extend_name']??'';
                            $applied_rule = $applied_rule . $v['rule_name'].':' .$v['extend_name']. "\n\n";
                            $applied_ids = $applied_ids . $v['rule_id'] . '_';

                            if($v['type']=='coupon'){
                                $coupon =$v['extend_name'];
                            }
                            if($v['type']=='code'){
                                $code =$v['extend_name'];
                            }
                        }
                        $applied_ids = rtrim($applied_ids, "_");
                    }

                }

                $return = '';
                if (isset($order['order_after_sale']['return_type']) && $goods['status'] > 1) {
                    if ($order['order_after_sale']['return_type'] == 1) {
                        $return = '全部退';
                    }

                    if ($order['order_after_sale']['return_type'] == 2) {
                        $return = '部分退';
                    }
                }
                //过滤掉虚拟商品
                if((strpos($goods['sku'],$virtual_prod_prefix) === 0)){
                    continue;
                }
                $item = [
                    "\t" . $order['created_at'],//订单创建日期
                    "\t" . $order['pos_id'],//会员编号
                    "\t" . $order['transaction_time'],//支付时间
                    "\t" . $order['send_at'],//发货时间
                    "\t" . $order['order_sn'],//订单号
                    $type_name,//
                    $goods_type[$goods['type']],
                    $gift_name,//
                    $free_name,//
                    $order['status_name'],//订单状态
                    $order['state_name'],//状态信息
                    "\t" . $order['payment_id'],//支付流水号
                    $customer_type[$order['level']]??'',//顾客类型
                    "\t" . $order['express_no'],//快递单号
                    $order['province'],//省份
                    $order['city'],//城市
                    $order['contact'],//收件人
                    $goods['spu'] ?? '',//
                    $goods['sku'] ?? '',//
                    $goods['batch'] ?? '',//
                    $goods['name'],//
                    '',//系列
                    '',//子品类
                    str_replace(",", "-", $goods['cats']),//主品类
                    $goods['original_price'],//零售价
                    $goods['original_price'],//销售价
                    $rate,
                    $goods['discount'],//优惠金额
                    $goods['order_amount_total'],//折后价格
                    $goods['qty'],//数量
                    $order['total_amount'],//
                    '',//货值
                    $pay_list[$order['payment_type']],//支付方式
                    $order['channel_name'],//平台类型
                    $applied_rule,
                    $applied_ids,
                    $order['coupon_code'],//促销信息
                    $code,//促销信息
                    $order['coupon_id'],//促销信息
                    $coupon,//促销信息
                    $order['remark'],//促销信息
                    $order['address'],//促销信息
                    $order['activity_channel'],//促销信息
                    $order['activity'],//促销信息
                    $order['share_uid'],//促销信息
                    $order['mobile'],//手机号
                    $is_exception??'',
                    $order['received_at'],//收货时间
                    $order['comment']['content']??'',
                    $order['comment']['score_p']??'',
                    $order['comment']['score_cs']??'',
                    $order['comment']['score_l']??'',
                    $order['comment']['created_at']??'',
                    //发票
                    $order['order_invoice']['type']??'',
                    $order['order_invoice']['title']??'',
                    $order['order_invoice']['number']??'',
                    $order['order_invoice']['email']??'',
                    $order['order_invoice']['mobile']??'',
                ];
                $info[] = $item;

            }


        }
        if (!$info) {
            $info = [[]];
        }
        $result = [

            'columns' => [
                '订单创建日期', '会员编号', '支付时间', '发货时间', '订单号', '订单类型', '商品类型', '是否赠品', '是否小样', '订单状态', '状态信息','支付流水号', '顾客类型', '快递单号', '省份', '城市', '收件人', 'SPU', 'SKU', 'UPC', '产品名称', '系列', '子品类', '主品类', '零售价', '销售价', '折扣率', '优惠金额', '折后价格', '数量', '销售总额', '货值', '支付方式', '平台类型', '促销信息', '促销规则id', '优惠码', '优惠码信息','优惠券ID','优惠券', '备注', '快递地址','活动','活动入口','分享人','手机号','异常标识','收货时间',
                '评论内容','产品评分','客服评分','物流评分','评论时间',
                '发票类型','发票抬头','纳税人识别号','发票邮箱','发票联系电话'
            ],
            'value' => $info,

        ];
        return $this->success($result, 'success');
    }

    public function edit(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $data = $this->curl('order/get', $all);

        $detail = $data['data'];
        return view('backend.oms.edit', [
            'order' => $detail,
        ]);
    }


    public function update(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['_token'], $postData['_url']);
            $editBack = $this->curl('order/update', $postData);
            return $editBack;
        }
    }

    public function orderItemIndex(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.oms.orderItem.index', [
            'query_string' => http_build_query($all)
        ]);
    }

    public function orderItemList(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('orderItem/list', $all);

        return $data;
    }

    public function updateOrderStatus(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/status/update', $all);

        return $data;
    }

    public function orderFree(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        $all['operator'] = auth()->user()->id.'-'.auth()->user()->name;
        $data = $this->curl('order/free', $all);

        return $data;
    }

    public function getStatusFilter($status)
    {
        switch ($status) {
            //所有
            case 'all':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'all', 'page' => 1]));
                break;
            //待付款
            case 'pending':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'pending', 'page' => 1]));
                break;
            //待发货
            case 'paid':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'paid', 'page' => 1]));
                break;
            //已发货
            case 'shipped':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'shipped', 'page' => 1]));
                break;
            //售后
            case 'after-sales':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'after-sales', 'page' => 1]));
                break;
            //已取消
            case 'canceled':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'canceled', 'page' => 1]));
                break;
            //已完成
            case 'finished':
                return url('admin/oms/order/index') . toQuery(array_merge(request()->all(), ['status' => 'finished', 'page' => 1]));
                break;

        }
    }

    function getActionStatus($status)
    {
        $currentStatus = request('status', 'all');
        if ($currentStatus == $status) {
            return 'layui-btn-normal';
        } else {
            return 'layui-btn-primary';
        }
    }


    public function batchDelivery(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);

        $data = $this->curl('order/batch/delivery', $all);

        return $data;
    }

    /**
     * 订单同步
     * @param Request $request
     * @return array
     */
    public function omsSync(Request $request){
        try{
            $orderId = $request->get('orderId');
            if($orderId){
                $api = app('ApiRequestInner');
                $params = [
                    'orderId'=>$orderId,
                ];
                $resp = $api->request('inner/omsSync','POST',$params);
                if($resp['code']==1){
                    return ['code' => 1,'message'=>'操作成功'];
                }throw new \Exception($resp['message']);
            }throw new \Exception('订单号不存在');
        }catch (\Exception $e){
            return ['code' => 0,'message'=>$e->getMessage()];
        }
    }

    public function export(Request $request,$action)
    {
        $all = $request->all();
        unset($all['_url']);
        $all['action'] = $action;
        $all['limit'] = 1000;
        $data = $this->curl('oms/order/export', $all);
        $list = $data['data'];
        $columns = $list[0];
        unset($list['0']);
        $result = [
            'columns' => $columns,
            'value' => array_values($list),
        ];
        return $this->success($result, 'success');
    }
}