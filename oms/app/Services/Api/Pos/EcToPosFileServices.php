<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/19
 * Time: 13:47
 */

namespace App\Services\Api\Pos;
use Illuminate\Support\Facades\Redis;

use App\Repositories\Oms\OmsOrderMainRepository;
use App\Repositories\Oms\AfterOrderMainRepository;
//use Illuminate\Support\Facades\Log;

class EcToPosFileServices
{
    static $payment_list = [
        1 => "Alipay",
        2 => "WeChat",
        3 => "UnionPay",
        4 => "Huabai",
        5 => "Codpay"
    ];

    static $source_list = [
        1 => 'EC-Miniapp',
        2 => 'EC-Mobile',
        3 => 'EC-PC',
        4 => 'EC-Manual'
    ];

    /**
     *  生成Pos需要的Sales文件
     * @throws \Exception
     */
    public function makeSalesData(){
        $redis = Redis::connection();
        $makeTime = date("Ymd_His");
        //正向订单的redis set集合名称
        $forward_order_id_name = 'deliveryOrder'.date("Ymd", strtotime("-1 day"));

        $forward_order_count = $redis->scard($forward_order_id_name);
        if($forward_order_count > 0){
            $forward_order_id_list = $redis->smembers($forward_order_id_name);
            foreach ($forward_order_id_list as $forward_order_id){
                try{
                    $this->makeSalesForwardOrderInfo($forward_order_id, $makeTime);
                }
                catch (\Exception $exception){
                    throw  $exception;
                }
            }
            var_export("success" .  PHP_EOL);
        }

        $after_order_id_name = 'afterOrderReviced'.date("Ymd", strtotime("-1 day"));;
        $after_order_count = $redis->scard($after_order_id_name);
        if($after_order_count > 0){
            $after_order_id_list = $redis->smembers($after_order_id_name);
            foreach ($after_order_id_list as $after_order_id){
                try{
                    $this->makeSalesAfterOrderInfo($after_order_id, $makeTime);
                }
                catch (\Exception $exception){
                    throw  $exception;
                }
            }
        }
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
            $order_info = OmsOrderMainRepository::getOrderDetailUnion($sales_order_id);
            if(sizeof($order_info) > 0){
                //这里先处理正式订单明细，
                $path = base_path();
                $sale_file_name = $path . "/public/EC2_Sales_" . $makeTime . ".txt";
                if(env("APP_ENV") !== "local"){
                    $sale_file_name = "/opt/ecToPos/EC2_Sales_" . $makeTime . ".txt";
                }
                foreach ($order_info as $order_detail){
                    //先把Items里具体的订单明细逐条生成了。。。，其实还需要判断下类型这些。
                    $order_detail_array = object2array($order_detail);
//                    if($order_detail_array['is_free'] ==2){
//                        continue;
//                    }
                    $order_detai_line_tmp = $this->makeForwardOrderDetailTmp($order_detail_array);
                    file_put_contents($sale_file_name, $order_detai_line_tmp, FILE_APPEND);
                    
                }
                $order_first_detail  = object2array($order_info[0]);
                if($order_first_detail['order_type'] === 2){
                    $order_detai_line_tmp = $this->makeForwardTrialsDetailTmp($order_first_detail);
                    file_put_contents($sale_file_name, $order_detai_line_tmp, FILE_APPEND);
                }
                if ((@$order_first_detail['total_discount']) > 0){
                    $order_detai_line_tmp = $this->makeForwardPromotionDetailTmp($order_first_detail);
                    file_put_contents($sale_file_name, $order_detai_line_tmp, FILE_APPEND);
                }

            }
            else{

            }
        }
        catch (\Exception $exception){
            throw $exception;
        }

        return $order_info;
    }


    /**
     * 生成正向订单信息不含优惠
     * @param $orderInfoArray
     * @return bool|string
     */
    private function makeForwardOrderDetailTmp($orderInfoArray)
    {

        $sold_price = sprintf("%.4f",$orderInfoArray['original_price']);

        //如果商品超过1分钱了，代表是正品，批次号就填真实的，否则就回传def
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = $orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime('-1 day'));
        //客人订单出库的日期，因为默认处理前一天的，所以这里不读取表里的，而是系统生成，防止跨月问题
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//客人订单出库的时间
        $newOrderInfo['product_code'] = $orderInfoArray['sku'];//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = $this->getLotNumber($orderInfoArray); //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = $orderInfoArray['qty']; //数量
        $newOrderInfo['price_sold'] = $sold_price; //售价，保留4位小数，没有千分位
        $newOrderInfo['staff_code'] = '5004'; //BA代号, 这里也默认用5004了
        $newOrderInfo['payment_type'] = $this->getPaymentType($orderInfoArray);//付款方式
        //支付方式7月26号-8月31日pos发布传cash，9月1号之后传与pos新约定的
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
        $order_tmp = implode('^' , $newOrderInfo);
        return $order_tmp . PHP_EOL;
    }

    /**
     * 生成正向订单优惠部分
     * @param $orderInfoArray
     * @return bool|string
     */
    private function makeForwardPromotionDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = $orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime('-1 day'));
        //客人订单出库的日期，因为默认处理前一天的，所以这里不读取表里的，而是系统生成，防止跨月问题
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = 'EVENT191103';//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = 'DEF'; //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = -(@$orderInfoArray['total_discount'] * 100); //数量
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
        $order_tmp = implode('^' , $newOrderInfo);
        return $order_tmp . PHP_EOL;
    }

    /**
     * 生成正向订单优惠部分
     * @param $orderInfoArray
     * @return bool|string
     */
    private function makeForwardTrialsDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = $orderInfoArray['order_sn'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime('-1 day'));
        //客人订单出库的日期，因为默认处理前一天的，所以这里不读取表里的，而是系统生成，防止跨月问题
        $newOrderInfo['TransTime'] = date("His",strtotime($orderInfoArray['send_at']));//出库时间在下单时不存在
        $newOrderInfo['product_code'] = 'ECPT202000';//产品编号,这个要和产品确认下
        $newOrderInfo['lot_number'] = 'DEF'; //产品批次，是WMS里实际的，赠品用Def默认用Def,这个还是要再商量
        $newOrderInfo['quantity'] = 1; //数量
        $newOrderInfo['price_sold'] = sprintf("%.4f",20.0000); //售价，保留4位小数，没有千分位
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
        $order_tmp = implode('^' , $newOrderInfo);
        return $order_tmp . PHP_EOL;
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
            $order_info = AfterOrderMainRepository::getAfterOrderDetailUnion($after_order_id);
            if(sizeof($order_info) > 0){
                $path = base_path();
                $sale_file_name = $path . "/public/EC2_Sales_" . $makeTime . ".txt";
                if(env("APP_ENV") !== "local"){
                    $sale_file_name = "/opt/ecToPos/EC2_Sales_" . $makeTime . ".txt";
                }
                foreach ($order_info as $order_detail){
                    $order_detail_array = object2array($order_detail);
                    $order_detai_line_tmp = $this->makeAfterOrderDetailTmp($order_detail_array);
                    file_put_contents($sale_file_name, $order_detai_line_tmp, FILE_APPEND);
                }
                $order_first_detail  = object2array($order_info[0]);
                if ((@$order_first_detail['total_discount']) > 0){
                    $order_detai_line_tmp = $this->makeAfterPromotionDetailTmp($order_first_detail);
                    file_put_contents($sale_file_name, $order_detai_line_tmp, FILE_APPEND);
                }
            }
            else{

            }
        }
        catch (\Exception $exception){
            throw $exception;
        }

        return $order_info;
    }


    /**
     * 生成逆行订单明细不含优惠
     * @param $orderInfoArray
     * @return bool|string
     */
    private function makeAfterOrderDetailTmp($orderInfoArray)
    {
        $sold_price = sprintf("%.4f",$orderInfoArray['original_price']);
        //如果商品超过1分钱了，代表是正品，批次号就填真实的，否则就回传def
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = $orderInfoArray['after_sale_no'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime('-1 day'));
        //客人订单出库的日期，因为默认处理前一天的，所以这里不读取表里的，而是系统生成，防止跨月问题
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
        $order_tmp = implode('^' , $newOrderInfo);
        return $order_tmp . PHP_EOL;
        //这里我现在的思路是php生成utf-8编码的，然后linux里ENCA方式修改为utf-16le编码的
    }

    /**
     * 生成逆向订单促销信息
     * @param $orderInfoArray
     * @return bool|string
     */
    private function makeAfterPromotionDetailTmp($orderInfoArray)
    {
        $newOrderInfo = [];
        $newOrderInfo['posId'] = @$orderInfoArray['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newOrderInfo['store_code'] = '5004'; //柜台号, 固定的5004
        $newOrderInfo['TransNum'] = $orderInfoArray['after_sale_no'];//自订单号这里要重新商榷一下
        $newOrderInfo['TransDate'] = date("Ymd",strtotime('-1 day'));
        //客人订单出库的日期，因为默认处理前一天的，所以这里不读取表里的，而是系统生成，防止跨月问题
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
        $order_tmp = implode('^' , $newOrderInfo);
        return $order_tmp . PHP_EOL;
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