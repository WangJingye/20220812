<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/9/10
 * Time: 15:32
 */

namespace App\Http\Controllers\Api\Pos;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Redis;
use App\Repositories\Oms\OmsOrderMainRepository;
use App\Repositories\Oms\AfterOrderMainRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class salesExcelController extends ApiController
{
    static $payment_list = [
        1 => "Alipay",
        2 => "Wechat",
        3 => "UnionPay",
        4 => "Huabei",
        5 => "Codpay"
    ];

    static $source_list = [
        1 => 'EC-Miniapp',
        2 => 'EC-Mobile',
        3 => 'EC-PC',
        4 => 'EC-Manual'
    ];

    static $excel_title = [
       '客户POS ID',
       '柜台号',
        '订单编号',
        '交易日期',
        '交易时间',
        '产品代码',
        '批号',
        '数量',
        '产品售价',
        'BA代码',
        '付款方式',
        '省份',
        '城市',
        '地址',
        '邮编',
        '联系人',
        '电话',
        '支付时间',
        '淘宝Nick',
        '订单来源'
    ];
    public function makeSalesExcel(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $redis = Redis::connection();
        $makeTime = date("Ymd_His");
        $forward_order_list[] = self::$excel_title;
        $after_order_list = [];
        //正向订单的redis set集合名称
        $forward_order_id_name = 'deliveryOrder'.date("Ymd", strtotime("-1 day"));
        $forward_order_count = $redis->scard($forward_order_id_name);
        if($forward_order_count > 0){
            $forward_order_id_list = $redis->smembers($forward_order_id_name);
            foreach ($forward_order_id_list as $forward_order_id){
                try{
                 $forward_order_list = array_merge($forward_order_list,
                     $this->makeSalesForwardOrderInfo($forward_order_id, $makeTime));
                }
                catch (\Exception $exception){
                    throw  $exception;
                }
            }
            var_export("forward success" .  PHP_EOL);
        }

        $after_order_id_name = 'afterOrderReviced'.date("Ymd", strtotime("-1 day"));;
        $after_order_count = $redis->scard($after_order_id_name);
        if($after_order_count > 0){
            $after_order_id_list = $redis->smembers($after_order_id_name);
            foreach ($after_order_id_list as $after_order_id){
                try{
                    $after_order_list =  array_merge($after_order_list,
                        $this->makeSalesAfterOrderInfo($after_order_id, $makeTime));
                }
                catch (\Exception $exception){
                    throw  $exception;
                }
                var_export("after success" .  PHP_EOL);
            }
        }

        $order_detail_all = array_merge($forward_order_list, $after_order_list);

        $spreadsheet->getActiveSheet()
            ->fromArray(
                $order_detail_all,  // The data to set
                null,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
            //    we want to set these values (default is A1)
            );
        $writer = new Xlsx($spreadsheet);
        $file_name = "/opt/ecToPos/" . date("Ymd") .".xlsx";
        $writer->save($file_name);
    }

    /**
     * 生成正向订单信息
     * @param $sales_order_id
     * @param $makeTime
     * @return mixed
     * @throws \Exception
     */
    private function makeSalesForwardOrderInfo($sales_order_id, $makeTime){
        try{
            $forward_order_list = [];
            $order_info = OmsOrderMainRepository::getOrderDetailUnion($sales_order_id);
            if(sizeof($order_info) > 0){
                //这里先处理正式订单明细，
                    foreach ($order_info as $order_detail){
                    //先把Items里具体的订单明细逐条生成了。。。，其实还需要判断下类型这些。
                    $order_detail_array = object2array($order_detail);
                    $order_detail = $this->makeForwardOrderDetailTmp($order_detail_array);
                    $forward_order_list[] = $order_detail;

                }
                $order_first_detail  = object2array($order_info[0]);
                if($order_first_detail['order_type'] === 2){
                    $order_detail = $this->makeForwardTrailsDetailTmp($order_first_detail);
                    $forward_order_list[] = $order_detail;
                }
                if (($order_first_detail['total_discount']) > 0){
                    $order_detail = $this->makeForwardPromotionDetailTmp($order_first_detail);
                    $forward_order_list[] = $order_detail;
                }
            }
            else{

            }
        }
        catch (\Exception $exception){
            throw $exception;
        }

        return $forward_order_list;
    }


    /**
     * 生成正向订单信息不含优惠
     * @param $orderInfoArray
     * @return array
     */
    private function makeForwardOrderDetailTmp($orderInfoArray)
    {

        $sold_price = sprintf("%.4f",$orderInfoArray['original_price']);

        //如果商品超过1分钱了，代表是正品，批次号就填真实的，否则就回传def
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] =  "\t" . $orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime($orderInfoArray['send_at'])); //交易日期
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = $orderInfoArray['sku'];//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = $this->getLotNumber($orderInfoArray); //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = $orderInfoArray['qty']; //数量
        $newOrderInfo['price_sold'] = $sold_price; //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式9月15日之前发布传CASH，9月15号之后传与pos新约定的
        $newOrderInfo['province'] = ''; //省份
        $newOrderInfo['city'] = ''; //城市
        $newOrderInfo['address'] = ''; //地址
        $newOrderInfo['zip_code'] = ''; //邮编
        $newOrderInfo['contact'] = $orderInfoArray['contact']; //联系人
        $newOrderInfo['contact_telephone'] = $orderInfoArray['mobile']; //电话
        $newOrderInfo['payment_time'] = date("Ymd",strtotime($orderInfoArray['transaction_time']));
        $newOrderInfo['Taobao_Nick'] = '';
        $source = isset($orderInfoArray['channel']) ? $orderInfoArray['channel'] : 1;
        $newOrderInfo['source'] = self::$source_list[$source];
        return $newOrderInfo;
    }

    /**
     * 生成正向订单优惠部分
     * @param $orderInfoArray
     * @return array
     */
    private function makeForwardPromotionDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = "\t" .$orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime($orderInfoArray['send_at'])); //交易日期
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = 'EVENT191103';//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = 'DEF'; //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = -($orderInfoArray['total_discount'] * 100); //数量
        $newOrderInfo['price_sold'] = 0.01; //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式7月26号-8月31日pos发布传cash，9月1号之后传与pos新约定的
        $newOrderInfo['province'] = ''; //省份
        $newOrderInfo['city'] = ''; //城市
        $newOrderInfo['address'] = ''; //地址
        $newOrderInfo['zip_code'] = ''; //邮编
        $newOrderInfo['contact'] = $orderInfoArray['contact']; //联系人
        $newOrderInfo['contact_telephone'] = $orderInfoArray['mobile']; //电话
        $newOrderInfo['payment_time'] = date("Ymd",strtotime($orderInfoArray['transaction_time']));;
        $newOrderInfo['Taobao_Nick'] = '';
        $source = isset($orderInfoArray['channel']) ? $orderInfoArray['channel'] : 1;
        $newOrderInfo['source'] = self::$source_list[$source];
        return $newOrderInfo;
    }


    /**
     * 生成付邮试用订单信息
     * @param $orderInfoArray
     * @return array
     */
    private function makeForwardTrailsDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = "\t" .$orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime($orderInfoArray['send_at'])); //交易日期
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = 'ECPT202000';//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = 'DEF'; //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = 1; //数量
        $newOrderInfo['price_sold'] = sprintf("%.4f",20); //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式7月26号-8月31日pos发布传cash，9月1号之后传与pos新约定的
        $newOrderInfo['province'] = ''; //省份
        $newOrderInfo['city'] = ''; //城市
        $newOrderInfo['address'] = ''; //地址
        $newOrderInfo['zip_code'] = ''; //邮编
        $newOrderInfo['contact'] = $orderInfoArray['contact']; //联系人
        $newOrderInfo['contact_telephone'] = $orderInfoArray['mobile']; //电话
        $newOrderInfo['payment_time'] = date("Ymd",strtotime($orderInfoArray['transaction_time']));;
        $newOrderInfo['Taobao_Nick'] = '';
        $source = isset($orderInfoArray['channel']) ? $orderInfoArray['channel'] : 1;
        $newOrderInfo['source'] = self::$source_list[$source];
        return $newOrderInfo;
    }

    /**
     * 生成逆向订单信息
     * @param $after_order_id
     * @param $makeTime
     * @return mixed
     * @throws \Exception
     */
    private function makeSalesAfterOrderInfo($after_order_id, $makeTime){
        try{
            $after_order_list = [];
            $order_info = AfterOrderMainRepository::getAfterOrderDetailUnion($after_order_id);
            if(sizeof($order_info) > 0){
                foreach ($order_info as $order_detail){
                    $order_detail_array = object2array($order_detail);
                    $order_detail = $this->makeAfterOrderDetailTmp($order_detail_array);
                    $after_order_list[] = $order_detail;
                }
                $order_first_detail  = object2array($order_info[0]);
                if (($order_first_detail['discount']) > 0){
                    $order_detail= $this->makeAfterPromotionDetailTmp($order_first_detail);
                    $after_order_list[] = $order_detail;

                }
            }
            else{

            }
        }
        catch (\Exception $exception){
            throw $exception;
        }

        return $after_order_list;
    }


    /**
     * 生成逆行订单明细不含优惠
     * @param $orderInfoArray
     * @return array
     */
    private function makeAfterOrderDetailTmp($orderInfoArray)
    {
        $sold_price = sprintf("%.4f",$orderInfoArray['original_price']);
        //如果商品超过1分钱了，代表是正品，批次号就填真实的，否则就回传def
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = "\t" .$orderInfoArray['after_sale_no'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime($this->getAfterTime($orderInfoArray))); //交易日期
        $newOrderInfo['TransTime'] = date("His",strtotime($this->getAfterTime($orderInfoArray)));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = $orderInfoArray['sku'];//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = $this->getLotNumber($orderInfoArray); //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = "-" . $orderInfoArray['qty']; //数量
        $newOrderInfo['price_sold'] = $sold_price; //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式7月26号-8月31日pos发布传cash，9月1号之后传与pos新约定的
        $newOrderInfo['province'] = ''; //省份
        $newOrderInfo['city'] = ''; //城市
        $newOrderInfo['address'] = ''; //地址
        $newOrderInfo['zip_code'] = ''; //邮编
        $newOrderInfo['contact'] = ''; //联系人
        $newOrderInfo['contact_telephone'] = ''; //电话
        $newOrderInfo['payment_time'] = date("Ymd",strtotime($orderInfoArray['updated_at']));;
        $newOrderInfo['Taobao_Nick'] = '';
        $newOrderInfo['source'] = '';
        return $newOrderInfo;
        //这里我现在的思路是php生成utf-8编码的，然后linux里ENCA方式修改为utf-16le编码的
    }

    /**
     * 生成逆行订单明细优惠部分
     * @param $orderInfoArray
     * @return array
     */
    private function makeAfterPromotionDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = "\t" .$orderInfoArray['after_sale_no'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime($this->getAfterTime($orderInfoArray))); //交易日期
        $newOrderInfo['TransTime'] = date("His",strtotime($this->getAfterTime($orderInfoArray)));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = 'EVENT191103';//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = 'DEF'; //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] =  @$orderInfoArray['total_discount'] * 100; //数量
        $newOrderInfo['price_sold'] = 0.01; //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式7月26号-8月31日pos发布传cash，9月1号之后传与pos新约定的
        $newOrderInfo['province'] = ''; //省份
        $newOrderInfo['city'] = ''; //城市
        $newOrderInfo['address'] = ''; //地址
        $newOrderInfo['zip_code'] = ''; //邮编
        $newOrderInfo['contact'] = ''; //联系人
        $newOrderInfo['contact_telephone'] = ''; //电话
        $newOrderInfo['payment_time'] = date("Ymd",strtotime($orderInfoArray['updated_at']));;
        $newOrderInfo['Taobao_Nick'] = '';
        $newOrderInfo['source'] = '';
        return $newOrderInfo;
    }

    private function getPaymentType($orderInfoArray){
        $payment_code = $orderInfoArray['payment_type'];
        $payment_type = @self::$payment_list[$payment_code];
        if(time()<strtotime('2020-09-15')){
            $payment_type = "CASH";
        }
        return $payment_type;
    }

    private function getLotNumber($orderInfoArray){
        $sold_price = sprintf("%.4f",$orderInfoArray['original_price']);
        if($sold_price > 0.01){
            $lot_number = $orderInfoArray['batch'];
        }
        else{
            $lot_number = 'DEF';
        }
        return $lot_number;
    }

    private function getAfterTime($orderInfoArray){
        $afterTime = isset($orderInfoArray['return_at']) ? $orderInfoArray['return_at'] : date("Y-m-d H:i:s", strtotime(" - 1 day"));
        return $afterTime;
    }
}