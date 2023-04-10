<?php

namespace App\Model;

use App\Lib\GuzzleHttp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTimeInterface;
use App\Support\Sms;
use App\Services\Api\Oms\AdServices;
use App\Model\YouShu;
use App\Jobs\OrderQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Services\ShipfeeTry\Revert;

class Order extends Model
{
    const PAY_PENDING_FLAG = 'pay_pending_flag';

    protected $table = 'oms_order_main';
    protected $action_array = [
        'cancel_order_unpay_auto' => 2,//过期取消
        'cancel_order_unpay_constomer' => 3,//用户取消
        'cancel_order_unpay' => 4, //未支付客服取消
    ];
    protected $channel_list = [
        'pc' => 3,
        'mobile' => 2,
        'wechat' => 1,
        'admin' => 4,
    ];
    static $pay_list = [
        'AliPay' => 1,
        'WeixinPay' => 2,
        'UnionPay' => 3,
        'HuabeiPay' => 4,
        'Offline' => 5,
    ];

    static $trade_type = [
        'AliPay' => 1,
        'WeixinPay' => 2,
        'UnionPay' => 3,
        'HuabeiPay' => 4,
        'Offline' => 5,
        'NATIVE' => 7,//微信扫码
        'MWEB' => 8,//h5 支付
        'JSAPI' => 1,//微信支付
        'minApp' => 6,//小程序
    ];
    static $state_info = [
        1 => '待支付',
        2 => '未支付，超时取消',
        3 => '未支付，客户取消',
        4 => '未支付，客服取消',
        5 => '已支付，待审核',
        6 => '货到付款，待审核',
        7 => '已支付，审核通过，发货中',
        8 => '已支付，发货拦截成功，待退款',
        9 => '已支付，发货拦截失败，待收货',
        10 => '已支付，已发货',
        11 => '货到付款，审核通过，发货中',
        12 => '货到付款，发货拦截成功，订单关闭',
        13 => '货到付款，发货拦截失败，待收货',
        14 => '已支付，已签收',
        15 => '货到付款，已签收',
        16 => '已支付，用户拒签，回仓中',
        17 => '货到付款，用户拒签，回仓中',
        18 => '已支付，申请售后，退货中',
        19 => '货到付款，申请售后，退货中',
        20 => '已支付，售后中，仓库已收货，待退款',
        21 => '货到付款，售后中，仓库已收货，待退款',
        22 => '已支付，售后中，仓库已收货，退款完成',
        23 => '货到付款，售后中，仓库已收货，退款完成',
        24 => '订单完成',
        25 => '订单关闭',

    ];

    /**
     * 跟前端约定的订单状态
     * @var array
     */
    static $oms_status_code_map = [
        1=>'pending',//待支付
        2=>'canceled',//已取消
        3=>'paid',//已支付
        4=>'shipped',//已发货
        5=>'refunding',//退款申请中
        7=>'refunded',//已退款
        8=>'returning',//退货申请中
        9=>'finished',//已完成
        10=>'finished',//已完成
        11=>'canceled',//已取消
        12=>'paid',//待发货
        13=>'returned',//已退货
    ];

    //快递Map
    static $delivery_mode_map = [
        'EMS'=>'EMS',
        'SF'=>'顺丰快递',
        'POSTB'=>'邮政国内小包',
        'STO'=>'申通E物流',
        'YTO'=>'圆通速递',
        'YUNDA'=>'韵达快运',
        'ZTO'=>'中通速递',
        'OTHER'=>'其他',
    ];

    /**
     * 用于详情页显示 区别普通订单状态的文案
     * @var array
     */
    static $oms_status_desc_map = [
        1=>'待付款',//待支付
        3=>'待发货',
        12=>'待发货',
    ];

    static $oms_status_list_map = [
        3=>'待发货',
        12=>'待发货',
    ];

    /**
     * 订单状态备注文案
     * @var array
     */
    static $oms_status_remark = [
        1=>'您的订单已提交，请在指定时间内完成付款，超时订单自动取消',
        2=>'您的订单已被取消',
        3=>'您的订单将在1-3个工作日内发出,请注意查收',
        4=>'您的商品已发货，请耐心等待',
        7=>'您的商品已退款成功',
        9=>'您的商品已被签收',
        10=>'您的商品已被签收',
        11=>'您的订单已被取消',
        12=>'您的订单将在1-3个工作日内发出,请注意查收',
        13=>'您的商品已退货成功',
    ];



//支付方式: AliPay, WeixinPay, UnionPay,HuabeiPay,Offline

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

//    protected $fillable = ['pos_id','order_id','store_code','order_sn','channel'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $guarded = ['id'];

    /**
     * 子订单信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDataItem()
    {
        return $this->hasMany('App\Model\OrderItem', 'order_main_id', 'id');
    }

    /**
     * 状态信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderDataStatus()
    {
        return $this->hasMany('App\Model\OmsStatusCircle', 'current_status_id', 'order_status');
    }

    /**
     * 状态记录信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderStatusLog()
    {
        return $this->hasMany('App\Model\OmsOrderLog', 'order_id', 'id');
    }

    /**
     * 发票信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderInvoice()
    {
        return $this->hasOne('App\Model\Dlc\DlcInvoice', 'order_sn', 'order_sn');
    }

    /**
     * 子订单信息
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderAfterSale()
    {
        return $this->hasOne('App\Model\AfterOrderSale', 'order_main_id', 'id');
    }

    public function comment(){
        return $this->hasOne('App\Model\OmsOrderComment','order_sn','order_sn');
    }

    public function returnapply(){
        return $this->hasOne('App\Model\OmsOrderReturnApply','order_sn','order_sn');
    }

    /**
     * 后台订单下一步状态信息
     * @param $status
     * @return array
     */
    public function orderNextStatus($status)
    {
        $status_next_array = [
            //待支付
            1 => array(
                [
                    'action_name' => '客服取消',
                    'action' => 'cancel_order_unpay',
                ],
            ),//已取消
            2 => array(),
            //待发货
            3 => array(
                [
                    'action_name' => '审核通过',
                    'action' => 'order_verify',
                ],
                [
                    'action_name' => '未发货取消订单',
                    'action' => 'order_verify_no',
                ],
                [
                    'action_name' => '拦截发货',
                    'action' => 'cancle_delivery',
                ]
            ),
            4 => array(),
            5 => array(),
            6 => array(),
            7 => array(),
            8 => array(),
            9 => array(),
            10 => array(),
            11 => array(),
            12 => array(),
        ];
        return array_get($status_next_array,$status)?:[];

    }

    /**
     * 生成订单号
     */
    public static function createOrderNo($channel_id = 1, $is_test = 2)
    {
        $nums = Redis::incr('create_order_no');
        return date('ymd') . $is_test . $channel_id . sprintf("%08d", $nums);
    }

    /**
     * 生成子订单号
     */
    public static function createOrderNoItem()
    {
        $nums = Redis::incr('create_order_no_item');
        return date('ymd') . sprintf("%07d", $nums);
    }

    /**
     * 生成订单信息
     * @param $order_info
     * @return array|bool
     */
    public function createOrder($order_info)
    {
        try {
            $order_info['user_id'] = $order_info['customer_id'];
            $self = new static();
            if (!isset($order_info['shipping_address']) || empty($order_info['shipping_address'])) {
                return [0, '地址信息不全', []];
            }
            if ($order_info['total']['total_amount'] < 0) {
                return [0, '商品金额异常，请确认', []];
            }

            if (!isset(self::$pay_list[$order_info['payment_method']])) {
                return [0, '支付方式不存在', []];
            }
            if (isset(self::$trade_type[$order_info['trade_type']])) {
                $order_info['trade_type'] = self::$trade_type[$order_info['trade_type']];
            } else {
                $order_info['trade_type'] = 0;
            }

            $order_info['payment_method'] = self::$pay_list[$order_info['payment_method']];


            $main_order_info = self::mainOrderInfo($order_info);
            $order_info['order_sn'] = $main_order_info['order_sn'];
            if (isset($order_info['invoice']) && !empty($order_info['invoice']['title'])) {
                $main_order_info['has_invoice'] = 1;
            }
            list($order_items_entries, $stock_sku, $total_num) = self::itemsOrderInfo($order_info);
            $main_order_info['total_num'] = $total_num;

            DB::beginTransaction();

            if ($main_order = $self->create($main_order_info)) {
                $main_order_id = $main_order->id;
                $date = date('y-m-d H:i:s');
                if (isset($order_info['invoice']) && !empty($order_info['invoice']['title'])) {
                    $invoice_info = array(
                        'order_sn' => $main_order_info['order_sn'],
                        'uid' => $order_info['user_id'],
                        'title' => $order_info['invoice']['title'],
                        'type' => $order_info['invoice']['type'],
                        'number' => $order_info['invoice']['id'],
                        'email' => isset($order_info['invoice']['email']) ? $order_info['invoice']['email'] : '',
                        'mobile' => isset($order_info['invoice']['mobile']) ? $order_info['invoice']['mobile'] : '',
                    );
                    $main_order->orderInvoice()->create($invoice_info);
                }
                foreach ($order_items_entries as &$v) {
                    $v['order_main_id'] = $main_order_id;
                    $v['guide_id'] = $main_order_info['guide_id'];
                    $v['created_at'] = $date;
                    $v['updated_at'] = $date;
                }
                $order_items_entries = array_values($order_items_entries);
                $res = $main_order->orderDataItem()->insert($order_items_entries);
                $log_status = $main_order->orderStatusLog()->create([
                    'order_id' => $main_order_id,
                    'status' => 1,
                    'desc' => '创建订单'
                ]);
                if ($res && $log_status) {
                    if ($order_info['coupon_id']) {
                        $result = Coupon::useCoupon($order_info['customer_id'], $order_info['coupon_id']);
                        if ($result['code'] != 1) {
                            DB::rollBack();
                            return [0, $result['message'], []];
                        }
                    }

                    $sku_model = new Sku;
                    $deal_sku = $sku_model->updateBatchStock(json_encode($stock_sku), $main_order_info['channel'], 0);
                    if (!$deal_sku) {
                        if ($order_info['coupon_id']) {
                            Coupon::revertCoupon($order_info['customer_id'], $order_info['coupon_id']);
                        }
                        DB::rollBack();
                        return [0, '下单失败', []];
                    }

                    if ($deal_sku['code'] != 1) {
                        if ($order_info['coupon_id']) {
                            Coupon::revertCoupon($order_info['customer_id'], $order_info['coupon_id']);
                        }
                        DB::rollBack();
                        return [0, '库存不足', $deal_sku['data']];
                    }

                    DB::commit();
                    $cps_link = self::orderCpsLink($main_order_id);
                    $data = [
                        'order_sn' => $order_info['order_sn'],
                        'order_id' => $main_order_id,
                        'cps_link' => $cps_link,
                        //steven add 20201124(前端获取下单时间)
                        'created_at' => $main_order->created_at->format('Y-m-d H:i:s'),
                    ];
                    //触发器
                    $self->where('id', $main_order_id)->first()->update(['trigger_status' => 1]);
                    Log::info('taskNotifyShare');
                    self::taskNotifyShare($order_info['openid'],$main_order_id);
                    //steven 增加待支付提醒
                    $pendingRemindTime = config('dlc.order_pending_remind');
                    app(Dispatcher::class)->dispatch(new Queued(
                        'pendingRemind',
                        ['orderId'=>$main_order_id],$pendingRemindTime)
                    );
                    return [1, 'success', $data];
                }
                return [0, '网络异常'];

            }
            return [0, '网络异常'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('created_order_error:' . $e);
            return [0, '网络异常', []];
        }
    }

    /**
     * 组合主订单信息
     */
    public static function mainOrderInfo($order_info)
    {
        $order_sn = self::createOrderNo($order_info['channel']);
        //订单类型
        $order_type = isset($order_info['order_type']) ? $order_info['order_type'] : 1;
        //快递信息
        $shipping_address = [];
        if (isset($order_info['shipping_address'])) {
            $shipping_address = array(
                'mobile' => $order_info['shipping_address']['mobile'],
                'contact' => $order_info['shipping_address']['name'],
                'province' => $order_info['shipping_address']['province'],
                'city' => $order_info['shipping_address']['city'],
                'district' => $order_info['shipping_address']['district'],
                'address' => $order_info['shipping_address']['address_detail'],
                'delivery_mode' => ''
            );
        }

        //金额信息
        $amount_info = [];
        if (isset($order_info['total'])) {
            $amount_info = array(
                'used_points' => $order_info['used_points']['points'],
                'total_amount' => (string)floatval($order_info['total']['total_amount']),
                'total_ship_fee' => (string)floatval($order_info['total']['total_ship_fee']),
                'total_product_price' => (string)floatval($order_info['total']['total_product_price']),
                'total_discount' => (string)floatval($order_info['total']['total_discount']),
                'total_point_discount' => (string)floatval($order_info['used_points']['money']),
                'total_wrap_fee' => (string)floatval($order_info['total']['total_wrap_fee']),
            );
        }

        $order_status = 1;
        $order_state = 1;
        if ($order_info['payment_method'] == 5) {
            $order_status = 3;
            $order_state = 5;
        }
        if (isset($order_info['share_uid']) && !$order_info['share_uid']) {
            $order_info['share_uid'] = NULL;
        }
        //主订单信息
        $main_order_info = [
            'pos_id' => $order_info['pos_id'],
            'user_id' => $order_info['customer_id'],
            'open_id' => $order_info['openid'],
            'store_code' => $order_info['member_info']['store_code'] ?? '',
            'guide_id' => $order_info['member_info']['guide_code'] ?? 0,
            'order_type' => $order_type,
            'share_from' => $order_info['member_info']['member_code'] ?? 0,
            'share_uid' => $order_info['share_uid'] ?? NULL,
            'order_sn' => $order_sn,
            'remark' => $order_info['remark'] ?? '',
            'pay_order_sn' => $order_sn,
            'wms_order' => $order_sn . '-0',
            'payment_at' => date("Y-m-d H:i:s"),
            'transaction_date' => $order_info['payment_method'] != 5 ? NULL : date("Y-m-d"),
            'channel' => $order_info['channel'],
            'transaction_time' => date("Y-m-d H:i:s"),
            'order_status' => $order_status,
            'order_state' => $order_state,
            'payment_type' => $order_info['payment_method'],
            'trade_type' => $order_info['trade_type'],
            'coupon_id' => empty($order_info['coupon_id']) ? 0 : $order_info['coupon_id'],
            'coupon_code' => $order_info['coupon_code'] ?? '',
            'card_from' => $order_info['card']['from'],
            'card_content' => $order_info['card']['content'],
            'card_to' => $order_info['card']['to'],
            'activity_channel' => $order_info['activityChannel'] ?? '',
            'activity' => $order_info['entrance'] ?? ''
        ];
        return array_merge($shipping_address, $main_order_info, $amount_info);
    }

    /**
     * 组合子订单信息
     * @param $order_info
     */
    public static function itemsOrderInfo($order_info)
    {
        $order_items_entries = [];
        //待扣除的sku
        $stock_sku = [];
        $total_num = 0;
        foreach ($order_info['goods_list']['goods'] as $goods_array) {
            $item_group_id = self::createOrderNoItem();
            foreach ($goods_array['main'] as $value) {
                $collection_id = self::createOrderNoItem();
                $order_item = [
                    'order_sn' => $order_info['order_sn'],
                    'is_gift' => 0,
                    'is_free' => 0,
                    'name' => $value['name'],
                    'pic' => $value['pic'],
                    'short_desc' => $value['short_desc'],
                    'sku' => $value['sku'],
                    'spu' => $value['product_id'],
                    'product_amount_total' => (string)floatval($value['original_price']*$value['qty']),
                    'collection_id' => $collection_id,
                    'original_price' => (string)floatval($value['original_price']),
                    'discount' => (string)floatval($value['discount']),
                    'order_amount_total' => (string)floatval($value['paid_amount']),
                    'qty' => $value['qty'],
                    'type' => $value['product_type'],
                    'display_type' => $value['display_type'],
                    'gift_group_id' => $item_group_id,
                    'collections' => json_encode($value['collections']),
                    'spec_desc' => $value['spec_desc'],
                    'spec_property' => $value['spec_property'],
                    'applied_rule_ids' => json_encode($value['applied_rule_ids']),
                    'revenue_type' => $value['revenue_type'] ? $value['revenue_type'] : 0,
                    'cats' => $value['cats'] ? $value['cats'] : '',
                    'rule_name'=>'',
                    'main_sku'=>'',
                    'if_show'=>1,
                ];
                if(!empty($value['main_sku'])){
                    $order_item['main_sku'] = $value['main_sku'];
                    $order_item['if_show'] = 0;
                }
                $order_items_entries[] = $order_item;
                if ($value['product_type'] == 2) {
                    foreach ($value['collections'] as $item) {
                        for ($s = 0; $s < $item['qty']; $s++) {
                            $collection_order_item = [
                                'order_sn' => $order_info['order_sn'],
                                'is_gift' => 0,
                                'is_free' => 0,
                                'name' => $item['name'],
                                'pic' => $item['pic'],
                                'short_desc' => $item['short_desc'],
                                'sku' => $item['sku'],
                                'spu' => $value['product_id'],
                                'product_amount_total' => (string)floatval($item['original_price']*$value['qty']),
                                'collection_id' => $collection_id,
                                'original_price' => (string)floatval($item['original_price']),
                                'discount' => (string)floatval($item['discount']),
                                'order_amount_total' => (string)floatval($item['paid_amount']),
                                'qty' => $value['qty'],
                                'type' => 2,
                                'display_type' => $item['display_type'],
                                'gift_group_id' => $item_group_id,
                                'collections' => '',
                                'spec_desc' => $item['spec_desc'],
                                'spec_property' => $item['spec_property'],
                                'applied_rule_ids' => '',
                                'revenue_type' => $item['revenue_type'] ? $item['revenue_type'] : 0,
                                'cats' => $item['cats'] ? $item['cats'] : '',
                                'rule_name'=>'',
                                'main_sku'=>'',
                                'if_show'=>1,
                            ];
                            $order_items_entries[] = $collection_order_item;
                        }
                        $stock_sku[] = [$item['sku'], $item['qty'], $order_info['order_sn']];
                    }
                }
                if ($value['product_type'] != 2) {
                    $stock_sku[] = [$value['sku'], $value['qty'], $order_info['order_sn']];
                }
                $total_num += $value['qty'];
            }

            foreach ($goods_array['gifts'] as $value) {
                $order_item = [
                    'order_sn' => $order_info['order_sn'],
                    'is_gift' => 1,
                    'is_free' => 0,
                    'name' => $value['name'],
                    'pic' => $value['pic'],
                    'short_desc' => $value['short_desc'],
                    'sku' => $value['sku'],
                    'spu' => $value['product_id'],
                    'product_amount_total' => (string)floatval($value['original_price']*$value['qty']),
                    'collection_id' => $item_group_id,
                    'original_price' => (string)floatval($value['original_price']),
                    'discount' => 0,
                    'order_amount_total' => 0,
                    'qty' => $value['qty'],
                    'type' => $value['product_type'],
                    'display_type' => $value['display_type'],
                    'gift_group_id' => $item_group_id,
                    'collections' => json_encode($value['collections']),
                    'spec_desc' => $value['spec_desc'],
                    'spec_property' => $value['spec_property'],
                    'applied_rule_ids' => '',
                    'revenue_type' => $value['revenue_type'] ? $value['revenue_type'] : 0,
                    'cats' => $value['cats'] ? $value['cats'] : '',
                    'rule_name'=>'赠品',
                    'main_sku'=>'',
                    'if_show'=>1,
                ];
                $order_items_entries[] = $order_item;
                $stock_sku[] = [$value['sku'], $value['qty'], $order_info['order_sn']];
                $total_num += $value['qty'];
            }

        }
        foreach ($order_info['goods_list']['free_try'] as $value) {
            $order_item = [
                'order_sn' => $order_info['order_sn'],
                'is_gift' => 0,
                'is_free' => 1,
                'name' => $value['name'],
                'pic' => $value['pic'],
                'short_desc' => $value['short_desc'],
                'sku' => $value['sku'],
                'spu' => $value['product_id'],
                'product_amount_total' => (string)floatval($value['original_price'] * $value['qty']),
                'collection_id' => null,
                'original_price' => (string)floatval($value['original_price']),
                'discount' => (string)floatval($value['discount']),
                'order_amount_total' => (string)floatval($value['paid_amount']),
                'qty' => $value['qty'],
                'type' => $value['product_type'],
                'display_type' => $value['display_type'],
                'gift_group_id' => null,
                'collections' => json_encode($value['collections']),
                'spec_desc' => $value['spec_desc'],
                'spec_property' => $value['spec_property'],
                'applied_rule_ids' => '',
                'revenue_type' => $value['revenue_type'] ? $value['revenue_type'] : 0,
                'cats' => $value['cats'] ? $value['cats'] : '',
                'rule_name'=>'试用装',
                'main_sku'=>'',
                'if_show'=>1,
            ];
            $order_items_entries[] = $order_item;
            $stock_sku[] = [$value['sku'], $value['qty'], $order_info['order_sn']];
            $total_num += $value['qty'];
        }
        if (isset($order_info['goods_list']['another_group_gifts'])) {
            foreach ($order_info['goods_list']['another_group_gifts'] as $rule) {
                $item_group_id = self::createOrderNoItem();
                $rule_name = $rule['name'];
                foreach ($rule['gifts'] as $value){
                    $order_item = [
                        'order_sn' => $order_info['order_sn'],
                        'is_gift' => 2,
                        'is_free' => 0,
                        'name' => $value['name'],
                        'pic' => $value['pic'],
                        'short_desc' => $value['short_desc'],
                        'sku' => $value['sku'],
                        'spu' => $value['product_id'],
                        'product_amount_total' => (string)floatval($value['original_price'] * $value['qty']),
                        'collection_id' => null,
                        'original_price' => (string)floatval($value['original_price']),
                        'discount' => (string)floatval($value['discount']),
                        'order_amount_total' => (string)floatval($value['paid_amount']),
                        'qty' => $value['qty'],
                        'type' => $value['product_type'],
                        'display_type' => $value['display_type'],
                        'gift_group_id' => $item_group_id,
                        'collections' => json_encode($value['collections']),
                        'spec_desc' => $value['spec_desc'],
                        'spec_property' => $value['spec_property'],
                        'applied_rule_ids' => '',
                        'revenue_type' => $value['revenue_type'] ? $value['revenue_type'] : 0,
                        'cats' => $value['cats'] ? $value['cats'] : '',
                        'rule_name'=>$rule_name,
                        'main_sku'=>'',
                        'if_show'=>1,
                    ];
                    $order_items_entries[] = $order_item;
                    if ($value['product_type'] == 2) {
                        foreach ($value['collections'] as $item) {
                            $collection_id = self::createOrderNoItem();
                            for ($s = 0; $s < $item['qty']; $s++) {
                                $collection_order_item = [
                                    'order_sn' => $order_info['order_sn'],
                                    'is_gift' => 0,
                                    'is_free' => 0,
                                    'name' => $item['name'],
                                    'pic' => $item['pic'],
                                    'short_desc' => $item['short_desc'],
                                    'sku' => $item['sku'],
                                    'spu' => $value['product_id'],
                                    'product_amount_total' => (string)floatval($item['original_price']*$value['qty']),
                                    'collection_id' => $collection_id,
                                    'original_price' => (string)floatval($item['original_price']),
                                    'discount' => 0,
                                    'order_amount_total' => 0,
                                    'qty' => $value['qty'],
                                    'type' => 2,
                                    'display_type' => $item['display_type'],
                                    'gift_group_id' => $item_group_id,
                                    'collections' => '',
                                    'spec_desc' => $item['spec_desc'],
                                    'spec_property' => $item['spec_property'],
                                    'applied_rule_ids' => '',
                                    'revenue_type' => $item['revenue_type'] ? $item['revenue_type'] : 0,
                                    'cats' => $item['cats'] ? $item['cats'] : '',
                                    'rule_name'=>$rule_name,
                                    'main_sku'=>'',
                                    'if_show'=>1,
                                ];
                                $order_items_entries[] = $collection_order_item;
                            }
                            $stock_sku[] = [$item['sku'], $item['qty'], $order_info['order_sn']];
                        }
                    }
                    if ($value['product_type'] != 2) {
                        $stock_sku[] = [$value['sku'], $value['qty'], $order_info['order_sn']];
                    }
                }
                $total_num += $value['qty'];

            }
        }
        if (isset($order_info['goods_list']['product_coupon_sku'])) {
            $item_group_id = self::createOrderNoItem();
            foreach ($order_info['goods_list']['product_coupon_sku'] as $value) {
                $order_item = [
                    'order_sn' => $order_info['order_sn'],
                    'is_gift' => 0,
                    'is_free' => 2,
                    'name' => $value['name'],
                    'pic' => $value['pic'],
                    'short_desc' => $value['short_desc'],
                    'sku' => $value['sku'],
                    'spu' => $value['product_id'],
                    'product_amount_total' => (string)floatval($value['original_price'] * $value['qty']),
                    'collection_id' => null,
                    'original_price' => (string)floatval($value['original_price']),
                    'discount' => (string)floatval($value['discount']),
                    'order_amount_total' => (string)floatval($value['paid_amount']),
                    'qty' => $value['qty'],
                    'type' => $value['product_type'],
                    'display_type' => $value['display_type'],
                    'gift_group_id' => $item_group_id,
                    'collections' => json_encode($value['collections']),
                    'spec_desc' => $value['spec_desc'],
                    'spec_property' => $value['spec_property'],
                    'applied_rule_ids' => '',
                    'revenue_type' => $value['revenue_type'] ? $value['revenue_type'] : 0,
                    'cats' => $value['cats'] ? $value['cats'] : '',
                    'rule_name'=>'实物券',
                    'main_sku'=>'',
                    'if_show'=>1,
                ];
                $order_items_entries[] = $order_item;

                if ($value['product_type'] == 2) {
                    foreach ($value['collections'] as $item) {
                        $collection_id = self::createOrderNoItem();

                        for ($s = 0; $s < $item['qty']; $s++) {
                            $collection_order_item = [
                                'order_sn' => $order_info['order_sn'],
                                'is_gift' => 0,
                                'is_free' => 0,
                                'name' => $item['name'],
                                'pic' => $item['pic'],
                                'short_desc' => $item['short_desc'],
                                'sku' => $item['sku'],
                                'spu' => $value['product_id'],
                                'product_amount_total' => (string)floatval($item['original_price']*$value['qty']),
                                'collection_id' => $collection_id,
                                'original_price' => (string)floatval($item['original_price']),
                                'discount' => 0,
                                'order_amount_total' => 0,
                                'qty' => $value['qty'],
                                'type' => 2,
                                'display_type' => $item['display_type'],
                                'gift_group_id' => $item_group_id,
                                'collections' => '',
                                'spec_desc' => $item['spec_desc'],
                                'spec_property' => $item['spec_property'],
                                'applied_rule_ids' => '',
                                'revenue_type' => $item['revenue_type'] ? $item['revenue_type'] : 0,
                                'cats' => $item['cats'] ? $item['cats'] : '',
                                'rule_name'=>'实物券',
                                'main_sku'=>'',
                                'if_show'=>1,
                            ];
                            $order_items_entries[] = $collection_order_item;
                        }
                        $stock_sku[] = [$item['sku'], $item['qty'], $order_info['order_sn']];
                    }
                }
                if ($value['product_type'] != 2) {
                    $stock_sku[] = [$value['sku'], $value['qty'], $order_info['order_sn']];
                }
                $total_num += $value['qty'];
            }
        }
        return [$order_items_entries, $stock_sku, $total_num];
    }


    /**
     * 更改订单信息
     */
    public function doActionOrderStatus($order_sn, $action)
    {

        $redis = Redis::connection('default');
        $f = $redis->setnx("lock_oms_action" . $order_sn . $action, date("Y-m-d H:i:s"));
        if ($f != 1) {
            return false;
        }
        $redis->expire("lock_oms_action" . $order_sn . $action, 5);
        Log::info('orderRefuse' . $order_sn . $action);
        $self = new static();
        $info = $self->select('id', 'channel', 'order_status', 'payment_type', 'mobile', 'contact', 'coupon_id', 'user_id','order_type')->where('order_sn', $order_sn)->first();
        if ($action) {

            switch ($action) {
                case 'cancel_order_unpay':
                    $status = self::cancleOrder($order_sn, $info['id'], $info['payment_type'], $info['channel'], $this->action_array[$action], $info['mobile'], $info['contact'], $info['user_id'], $info['coupon_id'],$info['order_type']);
                    break;
                case 'order_verify_no':
                    $status = self::orderRefuse($info['id']);
                    break;
                case 'order_verify':
                    $status = self::orderDeliveryOrCancle($info['id'], 1);
                    break;
                case 'cancle_delivery':
                    $status = self::orderDeliveryOrCancle($info['id'], 2);
                    break;

                case 'set_exception':
                    $status = self::orderExceptionOrCancle($info['id'], 1);
                    break;
                case 'cancel_exception':
                    $status = self::orderExceptionOrCancle($info['id'], 2);
                    break;
                default:
                    $status = false;
            }
        }
        return $status;
    }

    /**
     * 审单拒绝
     * @param $order_id
     */
    public static function orderRefuse($order_id)
    {
        Log::info('orderRefuse' . $order_id);
        $self = new static();
        DB::beginTransaction();
        $order = $self->where('id', $order_id)->lockForUpdate()->first();
        if ($order->payment_type == 5) {
            $order->order_status = 2;
            $order->order_state = 4;
        } else {
            $order->order_status = 5;
            $order->order_state = 20;
        }
        if (!$order->save()) {

            DB::rollBack();
            return false;
        }
        if ($order->payment_type == 5) {
            self::skuReturn($order->order_sn, $order->channel, 1);
            if ($order->coupon_id > 0) {
                Coupon::revertCoupon($order->user_id, $order->coupon_id);
            }
            self::orderLog($order_id, 4, '审核拒绝，订单取消');
        } else {
            self::orderLog($order_id, 6, '审核拒绝，待退款');
        }
        DB::commit();
        return true;

    }

    /**
     * 第三方发货/拦截
     */
    public static function orderDeliveryOrCancle($order_id, $type = 1)
    {
        $qimen = new QimenDeliveryOrder;
        $self = new static();
        DB::beginTransaction();
        $order = $self->where('id', $order_id)->lockForUpdate()->first();
        if ($order->order_status != 3) {
            return false;
        }
        $status = $qimen->deliveryExecute($order_id, $type);


        if ($status) {
            $pay_type = $order->payment_type;
            if ($type == 1) {
                $order->order_status = 3;
                $order->order_state = 7;
                $status = 7;
                $desc = '审核通过,发货中';
            } else {

                if ($pay_type == 5) {
                    $order->order_status = 2;
                    $order->order_state = 3;


                } else {
                    $order->order_status = 5;
                    $order->order_state = 9;
                }

                $status = 9;
                $desc = '货物未发，拦截成功';
            }
            if (!$order->save()) {
                DB::rollBack();
                return false;
            }
            if ($pay_type == 5 && $type != 1) {
                $success = self::skuReturn($order->order_sn, $order->channel, $type = 1);

                if (!$success) {
                    DB::rollBack();
                    return false;
                }
            }
            self::orderLog($order_id, $status, $desc);
            DB::commit();
            return true;
        }
        if ($type == 1) {
            self::orderLog($order_id, 1, '提交审核失败');
        } else {
            self::orderLog($order_id, 1, '拦截失败');

        }

        return false;
    }


    /**
     * 未支付取消下单(超时取消 用户取消 客服取消)
     */
    public static function cancleOrder($order_sn, $order_id, $pay_type, $channel, $next_status, $mobile, $name, $uid, $coupon_id,$order_type)
    {

        $self = new static();

        DB::beginTransaction();
        $status = $self->where('order_sn', $order_sn)->where('order_state', 1)->update(
            ['order_status' => 2, 'order_state' => $next_status]
        );

        if (!$status) {
            return false;
        }
        $success = self::skuReturn($order_sn, $channel, 1);
        if ($coupon_id > 0) {
            Coupon::revertCoupon($uid, $coupon_id);
        }

        //如果是付邮试用则退回次数
        if($order_type==2){
            \App\Services\ShipfeeTry\Revert::revertNumber($order_sn);
        }
        if (!$success) {
            DB::rollBack();
            return false;
        }
        DB::commit();
        $sms = new Sms();
        $sub = new SubscribeShipped;
        $desc = '取消订单';
        if ($next_status == 2 && $channel != 4) {
            $desc = '支付超时，取消订单';
            $sms->send($mobile, 7, $name, $order_sn);
        }
        if ($next_status == 4 && $channel != 4) {
            $desc = '支付超时，客服取消订单';
            $sms->send($mobile, 8, $name, $order_sn);
        }
        $sub->cancelMessage($order_id);
        self::orderLog($order_id, $next_status, $desc);

        return true;
    }

    /**
     * 支付成功后解锁库存
     */
    public static function payOrder($data)
    {
        Log::info('pay_success', $data);
        $self = new static();
        $order_info = $self->select('id', 'channel', 'coupon_id', 'user_id', 'order_sn', 'order_status', 'coupon_id', 'mobile', 'contact')->where('pay_order_sn', $data['pay_order_sn'])->first();
        if (!$order_info) {
            return false;
        }

        if ($order_info['order_status'] == 3) {
            return true;
        }
        DB::beginTransaction();
        if ($order_info['order_status'] == 1) {
            $status = $self->where('id', $order_info['id'])->where('order_status', 1)->first()->update(
                [
                    'order_status' => 3,
                    'order_state' => 5,
                    'payment_type' => $data['type'],
                    'trade_type' => $data['tradeType'],
                    'payment_id' => $data['trade_no'],
                    'transaction_time' => $data['payTime'],
                    'transaction_date' => date('Y-m-d', strtotime($data['payTime'])),
                ]

            );
            if (!$status) {
                return false;
            }
            self::orderLog($order_info['id'], 5, '支付成功');
            DB::commit();
            Log::info('pay_success_order' . $order_info['coupon_id'] . $order_info['order_sn'], []);
            $sub = new SubscribeShipped;
            //支付成功
            $sub->paidMessage($order_info['id']);
            //加入队列 执行OMS推送(解锁库存放在推送成功后执行)
            app(Dispatcher::class)->dispatch(new Queued(
                'syncToOms',
                ['orderId'=>$order_info['id']])
            );
            //加入队列 增加对应商品的销量记录
            app(Dispatcher::class)->dispatch(new Queued(
                    'salesVolume',
                    ['orderId'=>$order_info['id']])
            );
            //支付回调成功后删除前端支付成功的临时标记
            \App\Services\Dlc\SalesServices::removePayPendingFlag($order_info['order_sn']);
            return true;

        }

        return false;
    }

    /**
     * 支付成功后解锁库存
     */
    public static function payUpdateOrder($data)
    {
        Log::info('payUpdateOrder', $data);
        $self = new static();
        $update = [
            'huabei_period' => $data['huabei_period'] ?? 0,
            'payment_type' => $data['type'],
            'pay_order_sn' => $data['pay_order_sn'],
            'trade_type' => $data['tradeType'],
            'payment' => $data['payment'] ?? '',
            'payment_at' => $data['time'] ?? date('Y-m-d H:i:s'),
        ];
        if (!isset($data['huabei_period'])) {
            unset($update['huabei_period']);
        }
        if (isset($data['order_id'])) {
            $status = $self->where('id', $data['order_id'])->update($update);
        } else {
            $status = $self->where('order_sn', $data['order_sn'])->update($update);
        }

        if (!$status) {
            return false;
        }

        return true;
    }


    /**
     * 完成订单
     * @param $data
     * @return bool
     */
    public static function completeOrder($order_id)
    {
        $self = new static();
        $status = $self->where('id', $order_id)->update(
            [
                'order_status' => 10,
                'order_state' => 22
            ]
        );
        if ($status) {
            self::orderLog($order_id, 11, '订单完成');
            return true;
        }
        return false;
    }

    /**
     * 获取cps 信息
     * @param $order_id
     */
    public static function orderCpsLink($order_id)
    {
        $self = new static();
        $data = $self->select('id', 'total_amount', 'order_sn')->where('id', $order_id)->with(['orderDataItem' => function ($query) {
            $query->select('id', 'order_main_id', 'qty', 'type', 'collections', 'sku', 'order_amount_total');
        }])->first()->toarray();

        $sub_orders = [];
        foreach ($data['order_data_item'] as $v) {
            if (empty($v['collections']) || $v['collections'] == '[]') {
                $sub_orders[] = [

                    'sub_order_num' => $v['id'],
                    'sku_id' => $v['sku'],
                    'buy_num' => $v['qty'],
                    'price' => (string)floatval($v['order_amount_total']),

                ];
            }

        }
        $order = [
            'order_num' => $data['order_sn'],
            'total_price' => (string)floatval($data['total_amount']),
            'sub_orders' => $sub_orders
        ];


        return AdServices::getPushOrderToLinktechLink($order);
    }


    /**
     * 订单详情
     */
    public static function orderInfo($order_sn, $pay_order_sn, $type = '')
    {

        if ($type == 'GA') {
            $data = self::orderListlimit('', $page = 1, $perCount = 10, $pay_order_sn);
            if (isset($data[0])) {
                $data[0]['payment_type'] = self::$pay_list[$data[0]['payment_type']];
                $data[0]['trade_type'] = self::$trade_type[$data[0]['trade_type']];
                return $data[0];
            }
            return [];
        }
        $self = new static();

        if ($order_sn) {
            $data = $self->select('id', 'payment_type', 'total_amount', 'huabei_period', 'trade_type', 'payment', 'payment_at', 'order_sn', 'order_status', 'order_state', 'pay_order_sn', 'coupon_id','province','city','district','address','contact','mobile','total_amount','total_ship_fee','created_at','express_no')
                ->where('order_sn', $order_sn)
                ->with(['orderDataItem' => function ($query) {
                    $query->select('id', 'order_main_id', 'name','spec_desc','original_price','qty','pic');
                }])->with('orderInvoice')->get();
        }
        if ($pay_order_sn) {
            $data = $self->select('id', 'payment_type', 'total_amount', 'huabei_period', 'trade_type', 'payment', 'payment_at', 'order_sn', 'order_status', 'order_state', 'pay_order_sn', 'coupon_id','province','city','district','address','contact','mobile','total_amount','total_ship_fee','created_at','express_no')
                ->where('pay_order_sn', $pay_order_sn)
                ->with(['orderDataItem' => function ($query) {
                    $query->select('id', 'order_main_id', 'name','spec_desc','original_price','qty','pic');
                }])->with('orderInvoice')->get();
        }


        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        foreach ($data as $k => &$v) {
            $v->state_name = $states_info[$v->order_state];
            $v->status_name = $status_info[$v->order_status];
            $v->order_remark = array_get(self::$oms_status_remark,$v->order_status)?:'';
            $v->order_status_code = array_get(self::$oms_status_code_map,$v->order_status)?:'';
        }
        return $data;
    }

    /**
     * 订单详情(使用中)
     * @param $uid
     * @param $order_sn
     * @return mixed
     */
    public static function orderInfoDetail($uid,$order_sn)
    {
        $self = new static();
        if ($order_sn) {
            $self = $self->where('user_id', $uid);
            $data = $self->where('order_sn', $order_sn)
                ->with(['orderDataItem'=>function($query){
                    $query->where('if_show',1);
                },'orderInvoice'])->get()->toArray();
            if($data){
                $status_model = new OmsOrderStatus;
                list($states_info, $status_info) = $status_model->getStatusMap();
                foreach ($data as $k => &$v) {
                    $goods_free = [];
                    $goods_arr = [];
                    $goods_point = [];
                    $group_gifts = [];
                    $total_num = 0;

                    $v['status_name'] = array_get(self::$oms_status_desc_map,$v['order_status'])?:$status_info[$v['order_status']] ;
                    $v['order_remark'] = array_get(self::$oms_status_remark, $v['order_status']) ?: '';
                    $v['order_status_code'] = array_get(self::$oms_status_code_map, $v['order_status']) ?: '';
                    $v['delivery_mode'] = array_get(self::$delivery_mode_map,$v['delivery_mode'])?:'';
                    $v['invoiced'] = array_get($v,'order_invoice.invoice_code')?1:0;
                    if($v['order_status']==1){
                        $cancel_time = bcadd(strtotime($v['created_at']),config('wms.oms_cancel_time'),0);
                        $cancel_rest_time = bcsub($cancel_time,time(),0);
                        $v['cancel_time'] = $cancel_rest_time<0?0:$cancel_rest_time;
                        //查看是否有前端支付成功的标记 如果有标记 订单状态又是未支付 则说明微信支付回调还没有过来
                        $hasPayPendingFlag = \App\Services\Dlc\SalesServices::hasPayPendingFlag($order_sn);
                        if($hasPayPendingFlag){
                            $v['wait_pay_notice'] = 1;
                        }
                    }
                    $v['return_status'] = self::getReturnStatus($v);

                    foreach ($v['order_data_item'] as $item) {
                        $item['original_price'] = (string)floatval($item['original_price']);
                        $item['product_amount_total'] = (string)floatval($item['product_amount_total']);
                        $item['order_amount_total'] = (string)floatval($item['order_amount_total']);
                        //付邮试用价格设置为0
                        if($v['order_type']==2){
                            $item['product_amount_total'] = (string)floatval('0.00');
                        }
                        //赠品小样商品单价设置为0
                        if($item['is_gift'] != 0 || $item['is_free'] != 0){
                            $item['original_price'] = (string)floatval('0.00');
                        }
                        if ($item['is_gift'] == 0 && $item['is_free'] == 0) {
                            if ($item['collections'] == '[]' && $item['type'] == 2) {
                                continue;
                            }
                            if (!$item['collections'] && $item['type'] == 2) {
                                continue;
                            }
                            $goods_arr[$item['gift_group_id']]['main'][] = $item;
                        } elseif ($item['is_gift'] == 1) {
                            $goods_arr[$item['gift_group_id']]['gift']['name'] = $item['rule_name'];
                            if(empty($goods_arr[$item['gift_group_id']]['gift']['qty'])){
                                $goods_arr[$item['gift_group_id']]['gift']['qty'] = 0;
                            }
                            $goods_arr[$item['gift_group_id']]['gift']['qty']+=$item['qty'];
                            $goods_arr[$item['gift_group_id']]['gift']['list'][] = $item;
                        } elseif ($item['is_free'] == 1) {
                            $goods_free[] = $item;
                        } elseif ($item['is_free'] == 2) {
                            if ($item['collections'] == '[]' && $item['type'] == 2) {
                                continue;
                            }
                            if (!$item['collections'] && $item['type'] == 2) {
                                continue;
                            }
                            $goods_point[] = $item;
                        } elseif ($item['is_gift'] == 2) {
                            if ($item['collections'] == '[]' && $item['type'] == 2) {
                                continue;
                            }
                            if (!$item['collections'] && $item['type'] == 2) {
                                continue;
                            }
                            $group_gifts[$item['gift_group_id']]['name'] = $item['rule_name'];
                            if(empty($group_gifts[$item['gift_group_id']]['qty'])){
                                $group_gifts[$item['gift_group_id']]['qty'] = 0;
                            }
                            $group_gifts[$item['gift_group_id']]['qty']+=$item['qty'];
                            $group_gifts[$item['gift_group_id']]['gifts'][] = $item;
                        }
                        $total_num += $item['qty'];
                    }
                    $v['order_data_item'] = $goods_arr ? array_values($goods_arr) : [];
                    $v['goods_free'] = $goods_free ? array_values($goods_free) : [];
                    $v['product_coupon_sku'] = $goods_point ? array_values($goods_point) : [];
                    $v['group_gifts'] = $group_gifts ? array_values($group_gifts) : [];

                    $v['total_num'] = $total_num;
                    $v['total_amount'] = (string)floatval($v['total_amount']);
                    $v['total_ship_fee'] = (string)floatval($v['total_ship_fee']);
                    $v['total_wrap_fee'] = (string)floatval($v['total_wrap_fee']);
                    $v['total_product_price'] = (string)floatval($v['total_product_price']);
                    $v['total_discount'] = (string)floatval($v['total_discount']);
                    $v['total_point_discount'] = (string)floatval($v['total_point_discount']);
                }
            }
        }
        return $data;
    }

    /**
     * @param $user_id
     * @param int $page
     * @param int $perCount
     * @return array
     */
    public static function orderList($pos_id, $page = 1, $perCount = 20)
    {

        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $offset = ($page - 1) * $perCount;
        $self = new static();
        $data = $self->where('pos_id', $pos_id)
            ->where('channel', '!=', 4)
            ->with('orderDataItem')
            ->with('orderInvoice')
            ->skip($offset)
            ->take($perCount)
            ->orderBy('id', 'desc')
            ->get()->toarray();

        $un_paid = [];
        $un_shiped = [];
        $refund = [];
        $finish = [];
        foreach ($data as $k => &$v) {

            $goods_free = [];
            $goods_arr = [];
            $goods_point = [];
            $group_gifts = [];
            $v['next_action'] = $self->orderNextStatus($v['order_status']);
            if ($v['order_status'] == 3 && $v['order_state'] == 1) {
                $v['next_action'][] = [
                    'action_name' => '客服取消',
                    'action' => 'cancel_order_unpay',
                ];
            }

            foreach ($v['order_data_item'] as &$item) {

                if ($v['channel'] != 0) {
                    if ($item['is_gift'] == 0 && $item['is_free'] == 0) {
                        if ($item['collections'] == '[]' && $item['type'] == 2) {
                            continue;
                        }
                        if (!$item['collections'] && $item['type'] == 2) {
                            continue;
                        }
                        $goods_arr[$item['gift_group_id']]['main'][] = $item;
                    }
                    if ($item['is_gift'] == 1) {
                        $goods_arr[$item['gift_group_id']]['gift'][] = $item;
                    }

                } else {
                    $goods_arr[$item['gift_group_id']]['main'][] = $item;
                }


                if ($item['is_free'] == 1) {
                    $goods_free[] = $item;
                }
                if ($item['is_free'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $goods_point[] = $item;
                }

                if ($item['is_gift'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $group_gifts[] = $item;
                }
            }

            $goods_arr = array_values($goods_arr);
            $detail['goods_list'] = $goods_arr;
            $detail['goods_free'] = $goods_free;
            $detail['product_coupon_sku'] = $goods_point;
            $detail['group_gifts'] = $group_gifts;
            $detail['address_info'] = [
                'mobile' => $v['mobile'],
                'contact' => $v['contact'],
                'province' => $v['province'],
                'district' => $v['district'],
                'city' => $v['city'],
                'address' => $v['address'],
                'remark' => $v['remark'],

            ];
            $detail['order_invoice'] = $v['order_invoice'];
            $detail['card'] = [
                'from' => $v['card_from'],
                'content' => $v['card_content'],
                'to' => $v['card_to'],
            ];
            $detail['money_info'] = [
                'used_points' => $v['used_points'],
                'total_amount' => (string)floatval($v['total_amount']),
                'total_wrap_fee' => (string)floatval($v['total_wrap_fee']),
                'total_ship_fee' => (string)floatval($v['total_ship_fee']),
                'total_product_price' => (string)floatval($v['total_product_price']),
                'total_discount' => (string)floatval($v['total_discount']),
                'total_point_discount' => (string)floatval($v['total_point_discount']),
            ];
            $items['total_number'] = $v['total_num'];
            $items['order_sn'] = $v['order_sn'];
            $items['pay_order_sn'] = $v['pay_order_sn'];
            $items['payment_at'] = $v['payment_at'];
            $items['payment'] = $v['payment'];
            $trade = self::$trade_type;
            $payment = self::$pay_list;
            $items['payment_type'] = array_search($v['payment_type'], $payment);
            $items['trade_type'] = array_search($v['trade_type'], $trade);
            $items['order_id'] = $v['id'];
            $items['state_status'] = $status_info[$v['order_status']];
            $items['order_status'] = $v['order_status'];
            $items['order_state'] = $v['order_state'];
            $items['has_invoice'] = $v['has_invoice'];
            $items['huabei_period'] = $v['huabei_period'];
            $items['invoice_url'] = $v['invoice_url'];
            $items['invoice_path'] = $v['invoice_path'];
            $items['invoice_download_url'] = $v['invoice_download_url'];
            $items['delivery_mode'] = array_get(self::$delivery_mode_map,$v['delivery_mode'])?:'';
            $items['express_no'] = $v['express_no'];
            $items['total_amount'] = (string)floatval($v['total_amount']);
            $items['created_at'] = $v['created_at'];
            $items['channel'] = $v['channel'];
            $items['detail'] = $detail;
//
            if (in_array($v['order_status'], [1])) {
                $un_paid[] = $items;
            }
            if (in_array($v['order_status'], [3, 4])) {
                $un_shiped[] = $items;
            }
            if (in_array($v['order_status'], [5, 6, 7, 8, 11])) {
                $refund[] = $items;
            }
            if (in_array($v['order_status'], [2, 9, 10])) {
                $finish[] = $items;
            }
        }
        unset($data);
        $data = [
            'un_paid' => $un_paid,
            'un_shiped' => $un_shiped,
            'refund' => $refund,
            'finish' => $finish,
        ];
        return $data;
    }

    /**
     * @param $pos_id
     * @param int $page
     * @param int $perCount
     * @return array
     */
    public static function orderListlimit($pos_id, $page = 1, $perCount = 10, $order_sn = '')
    {

        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $offset = ($page - 1) * $perCount;
        $self = new static();
        if ($pos_id) {
            $data = $self->where('pos_id', $pos_id)
                ->where('channel', '!=', 4)
                ->with('orderDataItem')
                ->with('orderInvoice')
                ->skip($offset)
                ->take($perCount)
                ->orderBy('id', 'desc')
                ->get()->toarray();
        } else {
            $data = $self->where('order_sn', $order_sn)
                ->where('channel', '!=', 4)
                ->with('orderDataItem')
                ->with('orderInvoice')
                ->skip($offset)
                ->take($perCount)
                ->orderBy('id', 'desc')
                ->get()->toarray();
        }

        $arr = [];
        foreach ($data as $k => &$v) {

            $goods_free = [];
            $goods_arr = [];
            $goods_point = [];
            $group_gifts = [];
            $v['next_action'] = $self->orderNextStatus($v['order_status']);
            foreach ($v['order_data_item'] as &$item) {
                if ($v['channel'] != 0) {
                    if ($item['is_gift'] == 0 && $item['is_free'] == 0) {
                        if ($item['collections'] == '[]' && $item['type'] == 2) {
                            continue;
                        }
                        if (!$item['collections'] && $item['type'] == 2) {
                            continue;
                        }
                        $goods_arr[$item['gift_group_id']]['main'][] = $item;
                    }
                    if ($item['is_gift'] == 1) {
                        $goods_arr[$item['gift_group_id']]['gift'][] = $item;
                    }
                }
                if ($item['is_free'] == 1) {
                    $goods_free[] = $item;
                }
                if ($item['is_free'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $goods_point[] = $item;
                }
                if ($item['is_gift'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $group_gifts[] = $item;
                }
            }

            $goods_arr = array_values($goods_arr);
            $detail['goods_list'] = $goods_arr;
            $detail['goods_free'] = $goods_free;
            $detail['product_coupon_sku'] = $goods_point;
            $detail['group_gifts'] = $group_gifts;

            $detail['address_info'] = [
                'mobile' => $v['mobile'],
                'contact' => $v['contact'],
                'province' => $v['province'],
                'district' => $v['district'],
                'city' => $v['city'],
                'address' => $v['address'],
                'remark' => $v['remark'],

            ];
            $detail['order_invoice'] = $v['order_invoice'];
            $detail['card'] = [
                'from' => $v['card_from'],
                'content' => $v['card_content'],
                'to' => $v['card_to'],
            ];
            $detail['money_info'] = [
                'used_points' => $v['used_points'],
                'total_amount' => (string)floatval($v['total_amount']),
                'total_wrap_fee' => (string)floatval($v['total_wrap_fee']),
                'total_ship_fee' => (string)floatval($v['total_ship_fee']),
                'total_product_price' => (string)floatval($v['total_product_price']),
                'total_discount' => (string)floatval($v['total_discount']),
                'total_point_discount' => (string)floatval($v['total_point_discount']),

            ];
            $trade = self::$trade_type;
            $payment = self::$pay_list;
            $items['total_number'] = $v['total_num'];
            $items['order_sn'] = $v['order_sn'];
            $items['pay_order_sn'] = $v['pay_order_sn'];
            $items['payment_at'] = $v['payment_at'];
            $items['payment'] = $v['payment'];
            $items['order_id'] = $v['id'];
            $items['payment_type'] = array_search($v['payment_type'], $payment);
            $items['trade_type'] = array_search($v['trade_type'], $trade);
            $items['state_status'] = $status_info[$v['order_status']];
            $items['order_status'] = $v['order_status'];
            $items['order_state'] = $v['order_state'];
            $items['has_invoice'] = $v['has_invoice'];
            $items['huabei_period'] = $v['huabei_period'];
//            $items['invoice_url'] = $v['invoice_url'];
//            $items['invoice_path'] = $v['invoice_path'];
            $items['channel'] = $v['channel'];
//            $items['invoice_download_url'] = $v['invoice_download_url'];
            $items['delivery_mode'] = array_get(self::$delivery_mode_map,$v['delivery_mode'])?:'';
            $items['express_no'] = $v['express_no'];
            $items['total_amount'] = $v['total_amount'];

            $items['created_at'] = $v['created_at'];
            $items['transaction_date'] = $v['transaction_date'];

            $items['detail'] = $detail;
            $arr[] = $items;
        }
        unset($data);
        return $arr;
    }

    /**
     * 订单操作记录
     * @param $order_id
     * @param $status
     * @param string $desc
     */
    public static function orderLog($order_id, $status, $desc = '')
    {
        $data = [
            'order_id' => $order_id,
            'status' => $status,
            'desc' => $desc,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return OmsOrderLog::create($data);

    }

    /**
     * 订单最终退回，库存返还
     * @param $order_sn
     * @param $channel
     * @param type 1全部退 2部分退
     * @return bool
     */
    public static function skuReturn($order_sn, $channel, $type = 1)
    {

        $sku_model = new Sku;
        $order_items = OrderItem::where('order_sn', $order_sn);
        if ($type != 1) {
            $order_items = $order_items->where('status', 1);
        }
        $order_skus = $order_items->select('sku', 'qty', 'order_sn', 'type', 'collections')->get()->toArray();
        $stock_sku = [];

        foreach ($order_skus as $k => $v) {

            if (empty($v['collections']) || $v['collections'] == '[]') {
                $stock_sku[] = [$v['sku'], $v['qty'], $v['order_sn']];
            }
            if ($v['type'] == 3) {
                $stock_sku[] = [$v['sku'], $v['qty'], $v['order_sn']];
            }
        }


        $deal_sku = $sku_model->updateBatchStock(json_encode($stock_sku), $channel, 1,1);

        if ($deal_sku['code'] != 1) {

            return false;
        }
        return true;
    }

    public function addFreeOrder($order_sn, $skus, $operator = '')
    {
        $self = new static();
        $order = $self->where('order_sn', $order_sn)->first();
        if ($order['order_status'] != 3) {
            return [0, '当前订单状态不允许修改商品赠品信息', []];
        }
        $sku_arr = explode(',', $skus);
        $items = [];
        $stock_sku = [];
        $sku_model = new Sku;
        $sku_data = $sku_model->getSku($skus);
        if ($sku_data['code'] == 0 || empty($sku_data['data'])) {
            return [0, '商品信息异常', []];
        }
        $sku_info = $sku_data['data'];
        foreach ($sku_arr as $v) {
            if ($sku_info[$v]['sku']['ori_price'] != 0) {
                return [0, 'sku不是赠品类型', []];
            }
            $gift_rule = [
                [
                    "rule_id" => -1,
                    "type" => "freetry",
                    "gift_skus" => $v,
                    "rule_name" => "客服操作赠送",
                    "display_name" => null
                ]
            ];
            $img = '';
            if (isset($sku_info[$v]['sku']['kv_images'][0]['url'])) {
                $img = $sku_info[$v]['sku']['kv_images'][0]['url'];
            }
            $item = [
                'order_sn' => $order_sn,
                'is_gift' => 0,
                'is_free' => 1,
                'order_main_id' => $order->id,
                'name' => $sku_info[$v]['product_name'],
                'pic' => $img,
                'short_desc' => $sku_info[$v]['short_product_desc'],
                'sku' => $v,
                'spu' => $sku_info[$v]['product_id'],
                'product_amount_total' => 0,
                'collection_id' => null,
                'original_price' => 0,
                'discount' => 0,
                'order_amount_total' => 0,
                'qty' => 1,
                'type' => $sku_info[$v]['product_type'],
                'display_type' => $sku_info[$v]['display_type'],
                'gift_group_id' => null,
                'collections' => '',
                'spec_desc' => $sku_info[$v]['sku']['spec_desc'],
                'spec_property' => $sku_info[$v]['sku']['spec_property'],
                'applied_rule_ids' => json_encode($gift_rule),
                'revenue_type' => $sku_info[$v]['sku']['revenue_type'] ? $sku_info[$v]['sku']['revenue_type'] : 0,
                'cats' => $sku_info[$v]['cats'] ?? '',
                'created_at' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
            ];
            $stock_sku[] = [$v, 1, $order_sn];
            $items[] = $item;
        }
        if ($order->order_state == 7) {

            $qimen = new QimenDeliveryOrder;
            $status = $qimen->deliveryExecute($order->id, 2);
            if ($status != true) {
                return [0, '取消发货失败', []];
            }

            self::orderLog($order['id'], 0, $operator . '客服添加赠品,取消原发货单' . $skus);
            $wms = explode('-', $order->wms_order);
            if (!isset($wms[1])) {
                $num = 0;
            } else {
                $num = $wms[1] + 1;
            }

            $wms_sn = $order_sn . '-' . $num;
            $self->where('order_sn', $order_sn)->update(
                [
                    'order_state' => 5,
                    'wms_order' => $wms_sn
                ]
            );
        }

        DB::beginTransaction();
        $inc_num = count($sku_arr);
        $order->total_num = $order->total_num + $inc_num;

        $item_status = OrderItem::insert($items);
        self::orderLog($order['id'], 0, $operator . '客服添加赠品' . $skus);

        if (!$order->save() || !$item_status) {
            DB::rollBack();
            return [0, '操作失败', []];
        }


        $sku_model = new Sku;
        $deal_sku = $sku_model->updateBatchStock(json_encode($stock_sku), $order['channel'], 0);
        if (!$deal_sku) {
            DB::rollBack();
            return [0, '网络异常', []];
        }
        if ($deal_sku['code'] != 1) {
            DB::rollBack();
            return [0, '库存不足', []];
        }
        DB::commit();
        return [1, '操作成功', []];

    }


    public static function loadPointGoods($coupon_id, $order_sn, $user_id)
    {
        $from_params = [
            'coupon_id' => $coupon_id,
            'order_id' => $order_sn,
            'user_id' => $user_id

        ];

        $url = config('api.map')['pointmall/paysuccess'];
        $http_params = [
            'method' => 'post',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('loadPointGoods:request', $http_params);
        Log::info('loadPointGoods:reponse:' . $content);
        $result = json_decode($content, true);
        if (!$result || !is_array($result)) {
            return false;
        }
        return $result;
    }

    public static function canclePointGoods($coupon_id, $order_sn, $user_id)
    {
        $from_params = [
            'coupon_id' => $coupon_id,
            'order_id' => $order_sn,
            'user_id' => $user_id

        ];


        $url = config('api.map')['pointmall/cancelOrder'];
        $http_params = [
            'method' => 'get',
            'data' => $from_params,
            'type' => 'FORM',
            'url' => $url
        ];

        $content = GuzzleHttp::httpRequest($http_params);
        Log::info('canclePointGoods:request', $http_params);
        Log::info('canclePointGoods:reponse:' . $content);
        $result = json_decode($content, true);
        if (!$result || !is_array($result)) {
            return false;
        }
        return $result;
    }


    public static function orderExceptionOrCancle($order_id, $type, $items = [])
    {
        $self = new static();
        if ($type == 1) {
            $update = [
                'is_exception' => 1
            ];
            $where = [
                'is_exception' => 0
            ];
            $desc = '设为异常订单';
        } else {
            $where = [
                'is_exception' => 1
            ];
            $update = [
                'is_exception' => 0
            ];
            $desc = '取消异常订单';
        }
        if (!empty($items)) {
            $status = $self->whereIn('id', $items)->where($where)->get()->each(function($item)use($update){
                $item->update($update);
            });
        } else {
            $status = $self->where('id', $order_id)->where($where)->first()->update(
                $update
            );
        }

        if ($status) {
            self::orderLog($order_id, 30, $desc);
            return true;
        }
        return false;
    }

    /**
     * 添加/更新订单
     */
    public static function taskAddOrder($id)
    {

    }

    /**
     * 获取业绩订单
     * @author Steven
     * @param $guide_id
     * @param int $perCount
     * @param array $condition
     * @return array
     */
    public static function getAchieveList($guide_id,$perCount=10,$condition=[])
    {

        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $self = new static();
        /** @var \Illuminate\Database\Eloquent\Builder $model */
        $model = $self->where('guide_id', $guide_id)
            ->with(['orderDataItem','orderInvoice']);
        if(!empty($condition['start_date'])){
            $model->where('created_at','>=',"{$condition['start_date']} 00:00:00");
        }
        if(!empty($condition['end_date'])){
            $model->where('created_at','<=',"{$condition['end_date']} 23:59:59");
        }
        $total_amount = (string)floatval($model->sum('total_product_price'));
        $list = $model->orderBy('id', 'desc')->paginate($perCount)->toArray();
        $data = $list['data'];
        $arr = [];
        foreach ($data as $k => &$v) {
            $goods_free = [];
            $goods_arr = [];
            $goods_point = [];
            $group_gifts = [];
            $v['next_action'] = $self->orderNextStatus($v['order_status']);
            foreach ($v['order_data_item'] as &$item) {
                if ($v['channel'] != 0) {
                    if ($item['is_gift'] == 0 && $item['is_free'] == 0) {
                        if ($item['collections'] == '[]' && $item['type'] == 2) {
                            continue;
                        }
                        if (!$item['collections'] && $item['type'] == 2) {
                            continue;
                        }
                        $goods_arr[$item['gift_group_id']]['main'][] = $item;
                    }
                    if ($item['is_gift'] == 1) {
                        $goods_arr[$item['gift_group_id']]['gift'][] = $item;
                    }
                }
                if ($item['is_free'] == 1) {
                    $goods_free[] = $item;
                }
                if ($item['is_free'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $goods_point[] = $item;
                }
                if ($item['is_gift'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $group_gifts[] = $item;
                }
            }

            $goods_arr = array_values($goods_arr);
            $detail['goods_list'] = $goods_arr;
            $detail['goods_free'] = $goods_free;
            $detail['product_coupon_sku'] = $goods_point;
            $detail['group_gifts'] = $group_gifts;

            $detail['address_info'] = [
                'mobile' => $v['mobile'],
                'contact' => $v['contact'],
                'province' => $v['province'],
                'district' => $v['district'],
                'city' => $v['city'],
                'address' => $v['address'],
                'remark' => $v['remark'],

            ];
            $detail['order_invoice'] = $v['order_invoice'];
            $detail['card'] = [
                'from' => $v['card_from'],
                'content' => $v['card_content'],
                'to' => $v['card_to'],
            ];
            $detail['money_info'] = [
                'used_points' => $v['used_points'],
                'total_amount' => (string)floatval($v['total_product_price']),
                'total_wrap_fee' => (string)floatval($v['total_wrap_fee']),
                'total_ship_fee' => (string)floatval($v['total_ship_fee']),
                'total_product_price' => (string)floatval($v['total_product_price']),
                'total_discount' => (string)floatval($v['total_discount']),
                'total_point_discount' => (string)floatval($v['total_point_discount']),

            ];
            $trade = self::$trade_type;
            $payment = self::$pay_list;
            $items['total_number'] = $v['total_num'];
            $items['order_sn'] = $v['order_sn'];
            $items['pay_order_sn'] = $v['pay_order_sn'];
            $items['payment_at'] = $v['payment_at'];
            $items['payment'] = $v['payment'];
            $items['order_id'] = $v['id'];
            $items['payment_type'] = array_search($v['payment_type'], $payment);
            $items['trade_type'] = array_search($v['trade_type'], $trade);
            $items['state_status'] = $status_info[$v['order_status']];
            $items['order_status'] = $v['order_status'];
            $items['order_state'] = $v['order_state'];
            $items['has_invoice'] = $v['has_invoice'];
            $items['huabei_period'] = $v['huabei_period'];
//            $items['invoice_url'] = $v['invoice_url'];
//            $items['invoice_path'] = $v['invoice_path'];
            $items['channel'] = $v['channel'];
//            $items['invoice_download_url'] = $v['invoice_download_url'];
            $items['delivery_mode'] = array_get(self::$delivery_mode_map,$v['delivery_mode'])?:'';
            $items['express_no'] = $v['express_no'];
            $items['total_amount'] = (string)floatval($v['total_product_price']);

            $items['created_at'] = $v['created_at'];
            $items['transaction_date'] = $v['transaction_date'];

            $items['detail'] = $detail;
            $arr[] = $items;
        }
        unset($data);
        return [
            'total_num'=>$list['total'],
            'total_amount'=>(string)floatval($total_amount),
            'list'=>$arr,
        ];
    }

    /**
     * 订单列表(使用中)
     * @param $uid
     * @param string $order_status
     * @param int $page
     * @param int $perCount
     * @return array
     */
    public static function orderListsByStatus($uid,$order_status='',$page=1,$perCount=20)
    {
        $api_status_map = [];
        foreach(self::$oms_status_code_map as $k=>$v){
            $api_status_map[$v][] = $k;
        }
        $status_id = array_key_exists($order_status,$api_status_map)?$api_status_map[$order_status]:0;
        $status_model = new OmsOrderStatus;
        list($states_info, $status_info) = $status_model->getStatusMap();
        $offset = ($page - 1) * $perCount;
        $self = new static();
        if($status_id){
            $self = $self->whereIn('order_status',$status_id);
            $has_status = 1;
        }
        $self = $self->where('user_id', $uid);
        $model = $self->with(['orderDataItem'=>function($query){
                $query->where('if_show',1);
            },'orderInvoice'])
            ->skip($offset)
            ->take($perCount)
            ->orderBy('id', 'desc');
        $size = request()->get('size',10);
        $data_list = $model->paginate($size)->toArray();
        $data = $data_list['data'];
        $total = $data_list['total'];
        $last_page = $data_list['last_page'];
        $list = compact('total','last_page');
        foreach ($data as $k => &$v) {
            $goods_free = [];
            $goods_arr = [];
            $goods_point = [];
            $group_gifts = [];
            $total_num = 0;
            foreach ($v['order_data_item'] as &$item) {
                $item['original_price'] = (string)floatval($item['original_price']);
                $item['product_amount_total'] = (string)floatval($item['product_amount_total']);
                $item['order_amount_total'] = (string)floatval($item['order_amount_total']);
                //付邮试用价格设置为0
                if($v['order_type']==2){
                    $item['product_amount_total'] = (string)floatval('0.00');
                }
                //赠品小样商品单价设置为0
                if($item['is_gift'] != 0 || $item['is_free'] != 0){
                    $item['original_price'] = (string)floatval('0.00');
                }
                if ($v['channel'] != 0) {
                    if ($item['is_gift'] == 0 && $item['is_free'] == 0) {
                        if ($item['collections'] == '[]' && $item['type'] == 2) {
                            continue;
                        }
                        if (!$item['collections'] && $item['type'] == 2) {
                            continue;
                        }
                        $goods_arr[$item['gift_group_id']]['main'][] = $item;
                    }
                    if ($item['is_gift'] == 1) {
                        $goods_arr[$item['gift_group_id']]['gift'][] = $item;
                    }
                } else {
                    $goods_arr[$item['gift_group_id']]['main'][] = $item;
                }
                if ($item['is_free'] == 1) {
                    $goods_free[] = $item;
                }
                if ($item['is_free'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $goods_point[] = $item;
                }

                if ($item['is_gift'] == 2) {
                    if ($item['collections'] == '[]' && $item['type'] == 2) {
                        continue;
                    }
                    if (!$item['collections'] && $item['type'] == 2) {
                        continue;
                    }
                    $group_gifts[$item['gift_group_id']]['name'] = $item['rule_name'];
                    if(empty($group_gifts[$item['gift_group_id']]['qty'])){
                        $group_gifts[$item['gift_group_id']]['qty'] = 0;
                    }
                    $group_gifts[$item['gift_group_id']]['qty']+=$item['qty'];
                    $group_gifts[$item['gift_group_id']]['list'][] = $item;
                }
                $total_num += $item['qty'];
            }

            $goods_arr = array_values($goods_arr);
            $detail['goods_list'] = $goods_arr;
            $detail['goods_free'] = $goods_free;
            $detail['product_coupon_sku'] = $goods_point;
            $detail['group_gifts'] = $group_gifts?array_values($group_gifts):[];
            $detail['address_info'] = [
                'mobile' => $v['mobile'],
                'contact' => $v['contact'],
                'province' => $v['province'],
                'district' => $v['district'],
                'city' => $v['city'],
                'address' => $v['address'],
                'remark' => $v['remark'],

            ];
            $detail['order_invoice'] = $v['order_invoice'];
            $detail['card'] = [
                'from' => $v['card_from'],
                'content' => $v['card_content'],
                'to' => $v['card_to'],
            ];
            $detail['money_info'] = [
                'used_points' => $v['used_points'],
                'total_amount' => (string)floatval($v['total_amount']),
                'total_wrap_fee' => (string)floatval($v['total_wrap_fee']),
                'total_ship_fee' => (string)floatval($v['total_ship_fee']),
                'total_product_price' => (string)floatval($v['total_product_price']),
                'total_discount' => (string)floatval($v['total_discount']),
                'total_point_discount' => (string)floatval($v['total_point_discount']),
            ];
            $items['total_number'] = $total_num;
            $items['order_sn'] = $v['order_sn'];
            $items['pay_order_sn'] = $v['pay_order_sn'];
            $items['payment_at'] = $v['payment_at'];
//            $items['payment'] = $v['payment'];
            $trade = self::$trade_type;
            $payment = self::$pay_list;
            $items['payment_type'] = array_search($v['payment_type'], $payment);
            $items['trade_type'] = array_search($v['trade_type'], $trade);
            $items['order_id'] = $v['id'];

            $items['state_status'] = array_get(self::$oms_status_list_map,$v['order_status']);
            if(empty($items['state_status'])){
                $items['state_status'] = $status_info[$v['order_status']];
            }
            $items['order_status'] = $v['order_status'];
            $items['order_state'] = $v['order_state'];
            $items['order_status_code'] = array_get(self::$oms_status_code_map, $items['order_status']) ?: '';
            $items['has_invoice'] = $v['has_invoice'];
            $items['huabei_period'] = $v['huabei_period'];
            $items['invoice_url'] = $v['invoice_url'];
            $items['invoice_path'] = $v['invoice_path'];
            $items['invoice_download_url'] = $v['invoice_download_url'];
            $items['delivery_mode'] = array_get(self::$delivery_mode_map,$v['delivery_mode'])?:'';
            $items['express_no'] = $v['express_no'];
            $items['total_amount'] = (string)floatval($v['total_amount']);
            $items['created_at'] = $v['created_at'];
            $items['channel'] = $v['channel'];
            $items['is_comment'] = $v['is_comment'];
            $items['is_apply_return'] = $v['is_apply_return'];
            $items['return_status'] = self::getReturnStatus($v);
            $items['detail'] = $detail;
            $list['data'][] = $items;
        }
        return $list;
    }

    /**
     * @param $userId
     * @param $orderId
     * @author Steven
     * 成功支付后通知裂变计算人头
     */
    public static function taskNotifyShare($userId, $orderId)
    {
        try {
            $url = config('api.map')['share/notify'];
            $params = [
                'type' => 2,
                'userId' => $userId,
                'orderId' => $orderId
            ];
            $http_params = [
                'method' => 'post',
                'data' => $params,
                'type' => 'FORM',
                'url' => $url
            ];
            GuzzleHttp::httpRequest($http_params);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param $item
        0 可退款
        1 不可退款
        2 退款申请中
        3 退款被拒绝
        4 退款已同意
     * @return int
     */
    public static function getReturnStatus($item){
        $allow_status = [3,4,12];
        if(in_array($item['order_status'],$allow_status) && empty($item['is_apply_return'])){
            return 0;
        }elseif(!empty($item['is_apply_return'])){
            if(($item['is_apply_return']==1)&&(empty($item['is_allow_return']))){
                return 2;
            }elseif($item['is_allow_return']==1){
                return 3;
            }elseif($item['is_allow_return']==2){
                return 4;
            }
        }return 1;
    }

    /**
     * 发货
     * @param $order_sn
     * @param $delivery_mode
     * @param $express_no
     */
    public static function statusChangeShip($order_sn,$delivery_mode,$express_no){
        if(!array_key_exists($delivery_mode,self::$delivery_mode_map)){
            throw new \Exception('物流公司编号不存在'.$delivery_mode);
        }
        $order = self::query()->where('order_sn',$order_sn)->first();
        if(empty($order)){
            throw new \Exception('订单号不存在');
        }
        if($order->order_status == 3){
            $order->update([
                'order_status'=>4,
                'order_state'=>8,
                'delivery_mode'=>$delivery_mode,
                'express_no'=>$express_no,
            ]);
            Order::orderLog($order->id,8,'OMS同步已发货');
            //发送订阅消息
            (new \App\Model\SubscribeShipped)->shippedMessage($order->id);
        }
    }
}
