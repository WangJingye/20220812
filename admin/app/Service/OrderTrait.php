<?php
namespace App\Service;
use Illuminate\Support\Facades\Auth;

//后台叠加： 检测是否可以叠加 
Trait OrderTrait   
{
    public $refund_data = '';
    public $diff_data = '';
    static $order_status = [
        'pending'   => '待付款',
        'paid'      => '已支付',
        'shipping'  => '待发货',
        'shipped'   => '已发货',
        'waitrecv'  => '退货中',
        'recv'      => '等待退款',
        'refund'    => '已退款',
        'cancel'    => '已取消',
        'instock'   => '已支付',//待发货',//'锁货',
        'returned'  => '已退货',
        'syscancel' => '退货中',
        'approved'  => '待发货',
        'ready_to_ship' => '已发货',
        'printed'  => '待发货',//'已打印小票',
        'outofstock' => '已支付',
        'ready_for_collection' => '待取货',
        'waitpack' => '退货中',
        'signed'   => '已取货'
    ];
    //订单状态映射
    public function StatusMapping($status_en){
        $map = self::$order_status;
        return $map[$status_en]??$status_en; 
    }
    public function getOrderGoodsStatus($order,$item){
        $goodsStatus= [
            'status'=> $order['status'],
            'omsStatus'=> $order['oms_status'],
            'goodsStatus' => $item['status'],
            'goodsOmsStatus'=> $item['oms_status'],
            'assrStatus'    => $item['assr_status'],
            'serviceType'  => $order['service_type'],
            'shipping_id'  => $item['shipping_id'] ?? 0,
            'orderStatus'  => $item['order_status'] ?? ''
        ];
        $status = $this->getStatus($goodsStatus);
        return $status;
    }
    public function getOrderStatus($order){
        $statusInfo = [
            'status'=> $order['status'] ?? '',
            'omsStatus'=> $order['oms_status'] ?? '',
            'goodsStatus' => '',
            'goodsOmsStatus'=> '',
            'serviceType'  =>$order['service_type'] ?? '',
            'shipping_id'  => 0,
            'orderStatus'  => ''
        ];
        $status = $this->getStatus($statusInfo);
        return $status;
    }
    //是否售后
    public function isAfterSales($item,$order){
        $status = $item['status'];
        $order_status = $order['status'];
        $oms_status = $item['oms_status'];
        $assr_status = $item['assr_status'];
        if(in_array($assr_status,['WAITRECV', 'WAITPACK'])){
            return true;
        }
        if($status == 'refund'){
            return true;
        }
        if($status == 'returned'){
            return true;
        }
        if($oms_status == 'SYSCANCEL'){
            return true;
        }
        if($order_status == 'syscancel'){//订单整单取消，所有商品都是售后状态
            return true;
        }
        return false;
    }
    public function getStatus($info = [])
    {
        $status = array_key_exists($info['status'], self::$order_status) ? self::$order_status[$info['status']] : '';
        if($info['goodsStatus'] == 'refund')
    {
      return '已退款';
    }
    //售后状态       优先级+++
    if(isset($info['assrStatus']))
    {
      if(($info['status'] =='syscancel' && $info['assrStatus'] == 'WAITRECV') || ($info['status'] =='syscancel' && $info['assrStatus'] == 'WAITPACK' ) || ($info['status'] =='syscancel' && $info['assrStatus'] == 'PACK' ))
      {
        return '退货中';
      }elseif($info['status'] =='syscancel')
      {
         return '退款中';

      }elseif($info['assrStatus'] == 'WAITRECV' || $info['assrStatus'] == 'WAITPACK' || $info['assrStatus'] == 'PACK')
      {
        return '退货中';
      }
    }

    if($info['status'] == 'syscancel' && $info['omsStatus'] == 'SYSCANCEL')
    {
      return '退款中';
    }

    if(!isset($info['serviceType'])){

      return $status;
    }
    //物流
    if($info['serviceType'] == 'address')
    {
      if($info['status'] == 'shipping')
      {
        if($info['orderStatus'] == "PAID")
        {
          return '已支付';
        }elseif($info['orderStatus'] == "APPROVED")
        {
          return '待发货';
        }
        if($info['omsStatus'] == 'APPROVED')
        {
          if($info['orderStatus'] == "PAID")
          {
            return '已支付';
          }
          if($info['goodsStatus'] == 'instock')
          {
            return '待发货';
          }elseif($info['goodsStatus'] == 'paid')
          {
            return '已支付';
          }
          return  '待发货';
        }elseif ($info['omsStatus'] == 'READY') {
          if($info['goodsStatus'] == 'paid')
          {
            return  '已支付';
          }
          if($info['goodsStatus'] == 'printed')
          {
            return  '待发货';
          }
          return '待发货';
        }
      }elseif($info['status'] =='shipped')
      {
        if($info['orderStatus'] == 'PAID')
        {
          return '已支付';
        }elseif($info['orderStatus'] == 'APPROVED')
        {
          return '待发货';
        }elseif($info['orderStatus'] == 'READY')
        {
          return '待发货';
        }
        elseif($info['orderStatus'] == 'READYTOSHIP')
        {
          return '已发货';
        }elseif($info['orderStatus'] == 'SHIPPED')
        {
          return '已发货';
        }
      }
    }
    else
    {
      //自提
      if($info['status'] == 'shipping')
      {
        if($info['orderStatus'] =="PAID")
        {
          return "已支付";
        }
        if($info['omsStatus'] == 'SIGNED')
        {
          return '已取货';
        }elseif ($info['omsStatus'] == 'READY') {
          if($info['goodsStatus'] == 'paid')
          {
            return  '已支付';
          }
          if($info['goodsStatus'] == 'printed')
          {
            return  '待发货';
          }
        }elseif($info['omsStatus'] == "APPROVED")
        {
          if($info['orderStatus'] =="PAID")
          {
            return  '已支付';
          }
        }
      }
      elseif($info['status'] == 'shipped')
      {

        if($info['orderStatus'] == 'PAID')
        {
          return '已支付';
        }elseif($info['orderStatus'] == 'APPROVED')
        {
          return '待发货';
        }elseif($info['orderStatus'] == 'READY')
        {
          return '待发货';
        }
        elseif($info['orderStatus'] == 'READYTOSHIP')
        {
          return '已发货';
        }
       if($info['omsStatus'] == 'READYTOSHIP')
        {
          if($info['orderStatus'] =="PAID")
          {
            return '已支付';
          }elseif($info['orderStatus'] =="APPROVED"){

            return '待发货';
          }elseif ($info['orderStatus'] =="READY") {
            return '待发货';
          }else{
            if($info['goodsStatus'] == 'instock')
            {
              return  '待发货';
            }elseif($info['goodsStatus'] == 'printed')
            {
              return '已发货';
            }
          }
        }elseif($info['omsStatus'] == 'SIGNED')
        {
          return '已取货';
        }
        elseif($info['omsStatus'] !== 'SIGNED' || $info['omsStatus'] !== 'SHIPPED')
        {
          return '已发货';
        }
      }elseif ($info['status'] == 'ready_for_collection') {
        if($info['orderStatus'] == 'PAID')
        {
          return '已支付';
        }elseif($info['orderStatus'] == 'APPROVED')
        {
          return '待发货';
        }elseif($info['orderStatus'] == 'READY')
        {
          return '待发货';
        }
        elseif($info['orderStatus'] == 'READYTOSHIP')
        {
          return '已发货';
        }elseif($info['orderStatus'] == 'SHIPPED')
        {
          return '已发货';
        }
      }elseif($info['status'] == 'signed')
      {
        if($info['orderStatus'] == 'PAID')
        {
          return '已支付';
        }elseif($info['orderStatus'] == 'APPROVED')
        {
          return '待发货';
        }elseif($info['orderStatus'] == 'READY')
        {
          return '待发货';
        }
        elseif($info['orderStatus'] == 'READYTOSHIP')
        {
          return '已发货';
        }elseif($info['orderStatus'] == 'SHIPPED')
        {
          return '已发货';
        }
        elseif($info['orderStatus'] == 'READY_FOR_COLLECTION')
        {
          return '已取货';
        }
      }
    }
    return  $status;
    }
   
    //是否可以退款
    public function getRefundButton($order_id){
        if($this->refund_data){
            return $this->refund_data;
        }
        $data = $this->curl('order/getOrderRefundStatus',['orderId'=>$order_id]);
        $this->refund_data = $data;
        return $this->refund_data;
    }
    //用户是否有退款权限
    public function hasRefundPermission(){
        $user = Auth::user();
        if($user->hasPermissionTo('sales.order.refund')){
            return true;
        }
        return false;
    }
    
    public function getOrderRefundButton($order_id){
        if(!$this->hasRefundPermission()){
            return false;//没有权限
        }
        $data = $this->getRefundButton($order_id);
        $status = $data['data']['status']??false;
        if($status == '1'){
            return true;
        }
        return false;
    }
    public function getItemRefundButton($order_id,$item_id){
        if(!$this->hasRefundPermission()){
            return false;//没有权限
        }
        $data = $this->getRefundButton($order_id);
        $items = $data['data']['items']??[];
        foreach ($items as $item){
            if($item['id'] == $item_id){
                if($item['status'] == '1'){
                    return true;
                }else{
                    return false;
                }
            }
        }
        return false;
    }
    //门店地址
    public function getStoreAddress($store_address){
        $data = json_decode($store_address,true);
        $desc = $data['description'];
        $state = $data['state'];
        $city = $data['city'];
        return $desc . ' ' . $state . ' ' . $city;
    }
    public function getDiff($order_sn){
        if($this->diff_data){
            return $this->diff_data;
        }
        $data = $this->curl('order/getDiffInfo',['order_sn'=>$order_sn]);
        $this->diff_data = $data;
        return $this->diff_data;
    }
    public function getOrderItemEarnPoints($item){
        $earn_points = $item->give_point;
        $diff_earn_points = $item->diff_earn_points;
        if($diff_earn_points > 0){
            $earn_points = $diff_earn_points;
        }
        return $earn_points;
    }
    public function getOrderEarnPoints($order){
        $order_sn = $order->order_sn;
        $diff_earn_points = $this->getDiffEarnPoints($order_sn);
        if($diff_earn_points){
            return $diff_earn_points;
        }
        return $order->give_point;
    }
    public function getDiffEarnPoints($order_sn){
        $data = $this->getDiff($order_sn);
        $diff_pay_flag =  $data['data']['diff_pay_flag']??false;
        if($diff_pay_flag){
            return $data['data']['diff_earn_points'];
        }
        return false;
    }
    public function getDiffItemInfo($order_sn,$lineNbr){
        $data = $this->getDiff($order_sn);
        if(!isset($data['data'])){
            return '';//error
        }
        $items = $data['data']['items']??[];
        if(!$data['data']['diff_pay_flag']){
            return '';//没有支付
        }
        foreach($items as $item){
            if($item['line_nbr'] == $lineNbr){
                return $item['dscp'];
            }
        }
        return '';
    }
    public function getDiffItemPrice($order_sn,$lineNbr){
        $data = $this->getDiff($order_sn);
        $items = $data['data']['items']??[];
        if(!$data['data']['diff_pay_flag']){
            return 0;//没有支付
        }
        foreach($items as $item){
            if($item['line_nbr'] == $lineNbr){
                return $item['diff_price'];
            }
        }
        return 0;
    }
    
    public function getOrderTotalAmount($order){
        $amount = floor($order->total_amount); 
        $diff_amount = $order->diff->diff_total_price??0;
        return $amount + $diff_amount;
    }
    public function hasDiff($order){
        if(!$order->diff){
            return false;//没有差价
        }
        $diff_pay_flag = $order->diff->diff_pay_flag;
        if(!$diff_pay_flag){
            return false;//没有支付
        }
        return true;
    }
    //订单行是否有补差
    public function hasDiffItem($order,$good){
        $diff_items = $order->diffItems;
        if(!$diff_items){
            return '否';
        }
        foreach($diff_items as $item){
            if($good->lineNbr == $item->line_nbr){
                return '是';
            }
        }
        return '否';
    }
    //订单行实际支付金额，包括差价
    public function goodsAmount($order,$good){
        $amount = $good->price;
        $diff_items = $order->diffItems;
        if(!$diff_items){
            return $amount;
        }
        foreach($diff_items as $item){
            if($good->lineNbr == $item->line_nbr){
                return $amount+$item->diff_price;
            }
        }
        return $amount;
    }
    //订单补差金额
    public function getDiffPrice($order){
        $diff = $order->diff;
        if(!$diff){
            return 0;
        }
        return $diff->diff_total_price;
    }
    //订单行补差金额
    public function diffItemPrice($order,$good){
        $diff_items = $order->diffItems;
        if(!$diff_items){
            return 0;
        }
        foreach($diff_items as $item){
            if($good->lineNbr == $item->line_nbr){
                return $item->diff_price;
            }
        }
        return 0;
    }
    //订单级别，是否可以差价退款
    //TODO,什么订单状态就不能退差价了呢
    public function getDiffRefundButton($order){
        if(!$this->hasRefundPermission()){
            return false;//没有权限
        }
        $diff = $order->diff;
        if(!$diff){
            return false;//没有差价
        }
        if(!$diff->diff_pay_flag){
            return false;//没有支付
        }
        if($diff->diff_refund_flag){
            return false;//已经退款过了 
        }
        $data = $this->getRefundButton($order->id);
        $status = $data['data']['diffStatus']??false;//状态判断
        if(!$status ){
            return false;
        }
        return true;
    }
    //订单行级别，是否可以退差价， 
    public function getDiffItemRefundButton($order,$good){
        if(!$this->hasRefundPermission()){
            return false;//没有权限
        }
        $diff = $order->diff;//订单级别差价
        if(!$diff){
            return false;//没有差价
        }
        if(!$diff->diff_pay_flag){
            return false;//没有支付
        }
        if($diff->diff_refund_flag){
            return false;//已经退款过了
        }
        $diff_items = $order->diffItems;
        if(!$diff_items){
            return false;//没有订单行差价
        }
        $data = $this->getRefundButton($order->id);//订单行状态判断
        $items = $data['data']['items']??[];
        foreach ($items as $item){
            if($item['id'] == $good->id){
                if(!$item['diffStatus'] ){
                    return false;
                }
            }
        }
        foreach($diff_items as $item){
            if($good->lineNbr == $item->line_nbr){
                if($item->diff_refund_flag){
                    return false;//已经退过了
                }else{
                    return true;
                }
            }
        }
        return false;
    }
}
