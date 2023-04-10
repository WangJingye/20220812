<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Exception;
use App\Model\Help;
use App\Services\Top\TopHelper;

class QimenDeliveryOrder extends Model
{

    /**
     * @param $order_id
     * @param $method_type
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deliveryExecute($order_id, $method_type)
    {
        try {
            $date = date('Y-m-d H:i:s');
            $this->client = new Client();
            $from_params = [
                'app_key' => '20200628',
                'timestamp' => $date,
                'method' => 'deliveryorder.create',
                'sign' => 'Sisley',
                'sign_method' => 'md5',
                'format' => 'xml'
            ];

            if ($method_type == 1) {
                $from_params['method'] = 'deliveryorder.create';
                $data = $this->deliveryOrder($order_id);

            } else {
                $from_params['method'] = 'order.cancel';
                $data = $this->cancleDeliveryOrder($order_id);
            }

            if (!is_array($data)) {
                throw new Exception('发货参数异常', 501);
            }
            $body = array2Xml($data);
            if (!$body) {
                throw new Exception('数组转xml解析错误', 502);
            }

            $url = config('wms.url');
            $body = "<?xml version='1.0' encoding='utf-8'?><request>" . $body . '</request>';
            Log::info($from_params['method'].'_request:' . $body, $from_params);
            $response = $this->client->request('post', $url, [
                'body' => $body,
                'query' => $from_params,

            ]);
            $code = $response->getStatusCode();
            Help::Log('deliveryExecute:'.$from_params['method'].'response_code:' . $code,$from_params,'wms');
            if ($code == 200) {
                $contents = $response->getBody()->getContents();
                Help::Log('deliveryorder_response:'.$contents,$from_params,'wms');

                $array = TopHelper::xml2array($contents);
                if (!is_array($array)) {
                    throw new Exception('远程请求发货接口错误', 0);
                }
                if ($array['code'] == 0 && $array['flag'] == 'success') {
                    return true;
                }
                throw new Exception($array['success'], $array['code']);
            }
        } catch (Exception $e) {
            return $this->error($e);

        }
    }

    /**
     *
     * 通知wms 发货
     * @param $order_id
     * @return array
     */
    private function deliveryOrder($order_id)
    {
//totalAmount=itemAmount-discountAmount+freight
//arAmount=totalAmount-gotAmount
//totalAmount=sum(actualPrice*planQty)
        $self = new Order();
        $data = $self->where('id', $order_id)
            ->with('orderDataItem')
            ->first();

        if ($data) {

            $date = date('Y-m-d H:i:s');
            if ($data['payment_type'] == 5) {
                $orderFlag = 'COD';
                $arAmount = sprintf("%.0f", $data['total_amount']);
                $gotAmount = 0;
            } else {
                $arAmount = 0;
                $orderFlag = '';
                $gotAmount = sprintf("%.0f", $data['total_amount']);

            }
            $delivery['deliveryOrder'] = [
                "deliveryOrderCode" => $data['wms_order'],//出库单号
                "orderType" => "JYCK",
                "warehouseCode" => "XSL_OK",//仓库编码(统仓统配等无需ERP指定仓储编码的情况填OTHER)
                "orderFlag" => $orderFlag,//订单标记(用字符串格式来表示订单标记列表:例如COD=货到付款;LIMIT=限时配 送;PRESELL=预 售;COMPLAIN=已投诉;SPLIT=拆单;EXCHANGE=换货;VISIT=上 门;MODIFYTRANSPORT=是否 可改配送方式;CONSIGN = 物流宝代理发货;SELLER_AFFORD=是否卖家承担运费;FENXIAO=分销订 单)
                "sourcePlatformCode" => "other",
                "createTime" => $date,
                "placeOrderTime" => $data['created_at']->format('Y-m-d h:i:s'),
                "payTime" => $data['created_at']->format('Y-m-d h:i:s'),
                "payNo" => $data['order_sn'],
                "operateTime" => $date,
                "shopNick" => "dlc",
                "buyerNick" => $data['contact'],
                "totalAmount" => sprintf("%.2f", $data['total_amount']),
                "itemAmount" => sprintf("%.2f", $data['total_product_price']),
                "discountAmount" => sprintf("%.2f", $data['total_discount']),
                "freight" => sprintf("%.2f", $data['total_ship_fee']),
                "serviceFee" => 0,
                "arAmount" => $arAmount,//应收金额(消费者还需要支付多少--货到付款时消费者还需要支付多少约定使用这个字 段;单位元 )
                "gotAmount" => $gotAmount,
                "logisticsCode" => "SF",
                "senderInfo" => [
                    "company" => "上海联蔚科技有限公司",
                    "mobile" => "18518676209",
                    "email" => '',
                    "countryCode" => "CN",
                    "province" => "上海",
                    "city" => "上海市",
                    "area" => "徐汇区",
                    "town" => '',
                    "detailAddress" => "平福路188号2号楼6楼"
                ],
                "receiverInfo" => [
                    "name" => $data['contact'],
                    "zipCode" => '',
                    "tel" => $data['mobile'],
                    "mobile" => $data['mobile'],
                    "province" => $data['province'],
                    "city" => $data['city'],
                    "area" => $data['district'],
                    "detailAddress" => $data['address']
                ],
                "remark" => $data['remark'],
                "buyerMessage" => [],
                "sellerMessage" => [],
                "extendProps" => [
                    "cardMessageTo" => $data['card_to'],
                    "cardMessage" => $data['card_content'],
                    "cardMessageFrom" => $data['card_from'],
                    "order_type" => $data['order_type']
                ]
            ];


            foreach ($data['orderDataItem'] as $v) {
                if ($v['type'] == 2 && ($v['collections'])) {
                    continue;
                }
                if ($v['is_gift'] == 1) {
                    $v['name'] = $v['name'] . '(赠品)';
                }
                if ($v['is_free'] == 1) {
                    $v['name'] = $v['name'] . '(小样)';
                }
                $line = [
                    "orderLineNo" => $v['id'],
                    "sourceOrderCode" => $data['wms_order'],
                    "subSourceOrderCode" => '',
                    "ownerCode" => "Sisley",
                    "itemCode" => $v['sku'],
                    "itemId" => '',
                    "inventoryType" => "ZP",
                    "itemName" => $v['name'],
                    "planQty" => $v['qty'],
                    "actualPrice" => sprintf("%.2f", $v['order_amount_total']),
                    "retailPrice" => sprintf("%.2f", $v['original_price']),
                    "remark" => $data['remark'],
                ];
                if ($v['type'] == 2 && empty($v['collections'])) {
                    $line = [
                        "orderLineNo" => $v['id'],
                        "sourceOrderCode" => $data['wms_order'],
                        "subSourceOrderCode" => '',
                        "ownerCode" => "Sisley",
                        "itemCode" => $v['sku'],
                        "itemId" => '',
                        "inventoryType" => "ZP",
                        "itemName" => $v['name'] . '(套装)',
                        "planQty" => $v['qty'],
                        "actualPrice" => 0,
                        "retailPrice" => sprintf("%.2f", $v['original_price']),
                        "fromOrderLine" => $v['id'],
                        "fromSetCode" => $v['spu'],
                        "fromSetName" => $v['name'],
                        "fromSetQty" => $v['qty'],
                        "fromSetPrice" => sprintf("%.2f", $v['order_amount_total']),
                        "remark" => $data['remark'],
                    ];
                }

                $order_info['orderLine'][] = $line;
            }
            $delivery['orderLines'] = $order_info;
            return $delivery;
        }
        return false;
    }

    /**
     *
     * 通知wms 退货
     * @param $order_id
     * @return array
     */
    private function returnOrder($order_id, $type, $item_ids, $express_code, $express_type, $reason = '',$after_sale_no)
    {

        $self = new Order();
        $data = $self->where('id', $order_id)->first();

        if ($data) {

            if ($data['payment_type'] == 5) {
                $orderFlag = 'COD';
                $arAmount = sprintf("%.0f", $data['total_amount']);
                $gotAmount = 0;
            } else {
                $arAmount = 0;
                $orderFlag = '';
                $gotAmount = sprintf("%.0f", $data['total_amount']);

            }
            $delivery['returnOrder'] = [
                "returnOrderCode" => $after_sale_no,
                "warehouseCode" => 'XSL_OK',
                "orderType" => 'THRK',
                "orderFlag" => $orderFlag,//订单标记(用字符串格式来表示订单标记列表:例如COD=货到付款;LIMIT=限时配 送;PRESELL=预 售;COMPLAIN=已投诉;SPLIT=拆单;EXCHANGE=换货;VISIT=上 门;MODIFYTRANSPORT=是否 可改
                "preDeliveryOrderCode" => $data['wms_order'],
                "preDeliveryOrderId" => '',
                "logisticsCode" => $this->expressType($express_type),
                "expressCode" => $express_code,
                "buyerNick" => $data['contact'],
                "totalAmount" => sprintf("%.2f", $data['total_amount']),
                "itemAmount" => sprintf("%.2f", $data['total_product_price']),
                "discountAmount" => sprintf("%.2f", $data['total_discount']),
                "freight" => sprintf("%.2f", $data['total_ship_fee']),
                "serviceFee" => 0,
                "arAmount" => $arAmount,//应收金额(消费者还需要支付多少--货到付款时消费者还需要支付多少约定使用这个字 段;单位元 )
                "gotAmount" => $gotAmount,
                "remark" => [],
                "returnReason" => $reason,
                "senderInfo" => [
                    "name" => $data['contact'],
                    "zipCode" => '',
                    "tel" => $data['mobile'],
                    "mobile" => $data['mobile'],
                    "province" => $data['province'],
                    "city" => $data['city'],
                    "area" => $data['district'],
                    "detailAddress" => $data['address']
                ]
            ];

            if ($type == 1) {

                $goods = OrderItem::where('order_main_id', $order_id)->get()->toArray();

            } else {
                $after_arr = explode(',', $item_ids);
                $goods = OrderItem::whereIn('id', $after_arr)->get()->toarray();
            }

            $order_info =  [];
            foreach ($goods as $v) {

                if ($v['type'] == 2 && ($v['collections'])) {
                    continue;
                }
                if ($v['is_gift'] == 1) {
                    $v['name'] = $v['name'] . '(赠品)';
                }
                if ($v['is_free'] == 1) {
                    $v['name'] = $v['name'] . '(小样)';
                }
                $line = [
                    "orderLineNo" => $v['id'],
                    "sourceOrderCode" => $data['wms_order'],
                    "subSourceOrderCode" => '',
                    "ownerCode" => "Sisley",
                    "itemCode" => $v['sku'],
                    "itemId" => '',
                    "inventoryType" => "ZP",
                    "itemName" => $v['name'],
                    "planQty" => $v['qty'],
                    "actualPrice" => sprintf("%.2f", $v['order_amount_total']),
                    "retailPrice" => sprintf("%.2f", $v['original_price']),

                ];
                if ($v['type'] == 2 && empty($v['collections'])) {
                    $line = [
                        "orderLineNo" => $v['id'],
                        "sourceOrderCode" => $data['wms_order'],
                        "subSourceOrderCode" => '',
                        "ownerCode" => "Sisley",
                        "itemCode" => $v['sku'],
                        "itemId" => '',
                        "inventoryType" => "ZP",
                        "itemName" => $v['name'] . '(套装)',
                        "planQty" => $v['qty'],
                        "actualPrice" => 0,
                        "retailPrice" => sprintf("%.2f", $v['original_price']),
                        "fromOrderLine" => $v['id'],
                        "fromSetCode" => $v['spu'],
                        "fromSetName" => $v['name'],
                        "fromSetQty" => $v['qty'],
                        "fromSetPrice" => sprintf("%.2f", $v['order_amount_total']),

                    ];
                }

                $order_info['orderLine'][] = $line;
            }

            $delivery['orderLines'] = $order_info;

            return $delivery;
        }
        return false;
    }


    /**
     * 通知wms 取消发货
     * @param $order_id
     * @return array
     */
    private function cancleDeliveryOrder($order_id)
    {

        $data = Order::where('id', $order_id)
            ->with('orderDataItem')
            ->first();
        if (!$data) {
            return false;
        }
        $data = $data->toarray();
        $cancleDelivery = [
            "warehouseCode" => "XSL_OK",
            "ownerCode" => "Sisley",
            "orderCode" => $data['wms_order'],
            "orderId" => '',
            "orderType" => "JYCK"
        ];

        return $cancleDelivery;
    }


    /**
     * 售后单创建
     * @param $order_id
     * @param $method_type
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function afterSaleExecute($order_id, $data,$method_type)
    {
        try {
            $date = date('Y-m-d H:i:s');
            $this->client = new Client();
            $from_params = [
                'app_key' => '20200628',
                'timestamp' => $date,
                'method' => 'returnorder.create',
                'sign' => 'Sisley',
                'sign_method' => 'md5',
                'format' => 'xml'
            ];

            if ($method_type == 1) {
                $from_params['method'] = 'returnorder.create';
                $data = $this->returnOrder($order_id, $data['return_type'], $data['after_ids'], $data['express_no'], $data['express_type'], $data['question_desc'],$data['after_sale_no']);

            } else {
                $from_params['method'] = 'order.cancel';
                $data = $this->cancleReturnOrder($data['after_sale_no']);
            }

            if (!is_array($data)) {
                throw new Exception('发货参数异常', 501);
            }
            $body = array2Xml($data);
            if (!$body) {
                throw new Exception('数组转xml解析错误', 502);
            }
            $url = config('wms.url');
//            $url = 'http://oms.el.org/router/rest';
            $body = "<?xml version='1.0' encoding='utf-8'?><request>" . $body . '</request>';
            Help::Log('afterSaleExecute:'.$body,$from_params,'wms');
            $response = $this->client->request('post', $url, [
                'body' => $body,
                'query' => $from_params,

            ]);
            $code = $response->getStatusCode();

            if ($code == 200) {
                $contents = $response->getBody()->getContents();
                Help::Log('afterSaleExecute:'.$from_params['method'].$contents,$from_params,'wms');
                $array = TopHelper::xml2array($contents);
                if (!is_array($array)) {
                    throw new Exception('远程请求发货接口错误', 0);
                }
                if ($array['code'] == 0 && $array['flag'] == 'success') {
                    return true;
                }
                throw new Exception($array['success'], $array['code']);
            }
        } catch (Exception $e) {
            return $this->error($e);

        }
    }

    /**
     * 通知wms 取消发货
     * @param $order_id
     * @return array
     */
    private function cancleReturnOrder($after_order_sn)
    {

        $cancleDelivery = [
            "warehouseCode" => "XSL_OK",
            "ownerCode" => "Sisley",
            "orderCode" => $after_order_sn,
            "orderId" => '',
            "orderType" => "THRK"
        ];

        return $cancleDelivery;
    }


    /**
     * 前端接口异常返回
     *
     * @param \Exception $exception 异常
     * @return array
     * @author Jason
     * @date   2017-03-02
     */
    public function error(Exception $exception)
    {
        $errInfo = ['code' => $exception->getCode(), 'file' => $exception->getFile(), 'line' => $exception->getLine()];
        Log::error('QimenDeliveryOrder::' . $exception->getMessage(), $errInfo);
        return false;
    }

    /**
     * 获取快递信息
     * @return array
     */
    public function expressType($type)
    {
        $arr = ['', 'SF', 'STO', 'EMS', 'ZJS', 'HTKY', 'TTKDEX', 'YUNDA', 'JD'];
        return $arr[$type];
    }


}
