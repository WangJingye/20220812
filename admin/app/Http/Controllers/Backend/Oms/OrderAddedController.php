<?php

namespace App\Http\Controllers\Backend\Oms;

use App\Http\Controllers\Backend\Controller;
use http\QueryString;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class OrderAddedController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        $all['channel'][4] = 'on';
        $data = $this->curl('order/index', $all);

        $list = $data['data'];
        $action = $this;
        return view('backend.oms.add.index', compact('list', 'action'));
    }

    public function list(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        $all['channel'][4] = 'on';
        $data = $this->curl('order/index', $all);
        $list = $data['data'];
        $action = $this;
        return view('backend.oms.add.index', compact('list', 'action'));
    }

    public function create(Request $request)
    {
        return view('backend.oms.add.add');
    }


    public function getSku(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);
        $all['simple'] = 1;
        $data = $this->curl('outward/product/getProductInfoBySkuIds', $all);
        if(empty($data['data']) || $data['code']==0){
            $data['code'] = 2;
            return $data;
        }


        $skus = explode(',',$all['sku_ids']);
        $sku_items = $data['data'];
        $arr = [];
        foreach ($skus as &$v) {
            $item = $sku_items[$v];
            $item['img'] = '';
            if(isset($sku_items[$v]['sku']['kv_images'][0]['url'])){
                $item['img'] = $sku_items[$v]['sku']['kv_images'][0]['url'];

            }
            $item['spec_desc'] = $sku_items[$v]['sku']['spec_desc'];
            $item['price'] = $sku_items[$v]['sku']['price'];
            $item['num'] = 1;
            $item['id'] = $sku_items[$v]['sku']['sku_id'];
            $arr[]= $item;
        }
        $data['data'] = $arr;
        return $data;

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
        foreach ($list as $order) {
            if (!isset($trade_type[$order['trade_type']])) {
                $trade_type[$order['trade_type']] = '';
            }
            foreach ($order['order_data_item'] as $goods) {

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
                if ($goods['is_gift'] == 1) {
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

                $return = '';
                if (isset($order['order_after_sale']['return_type']) && $goods['status'] > 1) {
                    if ($order['order_after_sale']['return_type'] == 1) {
                        $return = '全部退';
                    }

                    if ($order['order_after_sale']['return_type'] == 2) {
                        $return = '部分退';
                    }
                }

                $item = [
                    "\t" . $order['created_at'],//订单创建日期
                    "\t" . $order['pos_id'],//会员编号
                    "\t" . $order['transaction_time'],//支付时间
                    "\t" . $order['send_at'],//发货时间
                    "\t" . $return_at ?? '',//退货入库时间
                    "\t" . $return_pay_at ?? '',//退款完成时间
                    empty($order['order_after_sale']['after_sale_no']) ? '' : $order['order_after_sale']['after_sale_no'],//退换货编号
                    $return,//退货类型
                    $status_name,//商品退货状态
                    "\t" . $order['order_sn'],//订单号
                    $type_name,//
                    $goods_type[$goods['type']],
                    $gift_name,//
                    $free_name,//
                    $order['status_name'],//订单状态
                    $order['state_name'],//状态信息
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
                ];
                $info[] = $item;

            }


        }
        if (!$info) {
            $info = [[]];
        }
        $result = [

            'columns' => [
                '订单创建日期', '会员编号', '支付时间', '发货时间', '退货入库时间', '退款完成时间', '退换货编号', '退货类型', '商品退货状态', '订单号', '订单类型', '商品类型', '是否赠品', '是否小样', '订单状态', '状态信息', '顾客类型', '快递单号', '省份', '城市', '收件人', 'SPU', 'SKU', 'UPC', '产品名称', '系列', '子品类', '主品类', '零售价', '销售价', '折扣率', '优惠金额', '折后价格', '数量', '销售总额', '货值', '支付方式', '平台类型', '促销信息', '促销规则id', '优惠码', '优惠码信息','优惠券ID','优惠券', '备注', '快递地址','活动','活动入口','分享人'],
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
        return view('backend.oms.add.edit', [
            'order' => $detail,
        ]);
    }
    public function getStatusFilter($status)
    {
        switch ($status) {
            //所有
            case 'all':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'all', 'page' => 1]));
                break;
            //待付款
            case 'pending':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'pending', 'page' => 1]));
                break;
            case 'paid':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'paid', 'page' => 1]));
                break;
            //已支付
            case 'finished':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'finished', 'page' => 1]));
                break;
            //待发货
            case 'pending-shiped':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'pending-shiped', 'page' => 1]));
                break;
            //已发货
            case 'finished-shiped':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'finished-shiped', 'page' => 1]));
                break;
            //售后
            case 'after-sales':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'after-sales', 'page' => 1]));
                break;
            //已关闭
            case 'cancel':
                return url('admin/oms/order/add/index') . toQuery(array_merge(request()->all(), ['status' => 'cancel', 'page' => 1]));
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

    public function addOrder(Request $request)
    {

        $all = $request->all();
        unset($all['_url']);

        $info = $this->curl('member/getMemberInfo', $all);
        if($info['code']==0){
            return $info;
        }


        $all['shipping_address'] = json_decode($all['shipping_address'],true);
        $all = array_merge($info['data'],$all);

//        static $pay_list = [
//            'AliPay' => 1,
//            'WeixinPay' => 2,
//            'UnionPay' => 3,
//            'HuabeiPay' => 4,
//            'Offline' => 5,
//        ];

        $all['remark'] = $all['remark']."&nbsp;&nbsp;操作人:".auth()->user()->name;
        $all['payment_method'] = 'Offline';
        $all['channel'] = 4;
        $data = $this->curl('order/add', $all);

        return $data;
    }





}