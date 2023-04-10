<?php
/**
 * Created by PhpStorm.
 * User: Jack.Xu1
 * Date: 2019/12/10
 * Time: 15:39
 */

namespace App\Model;
use App\Lib\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

class SubscribeShipped extends Model
{
    protected $table = 'subscribe_shipped';
    protected $primaryKey = 'id';
    protected $guarded = [];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 待付款即将取消提醒
     * [pendingMessage 待付款]
     * @Author   Peien
     * @DateTime 2020-04-02T10:35:15+0800
     * @return   [type]                   [description]
     */
    public function pendingMessage($order_id = '')
    { 
        $item=DB::table('subscribe_shipped')
            ->where('template_status','accept')
            ->where('msg_status','1')
            ->where('type',1)
            ->where('order_sn', $order_id)
            ->first();

        Log::info('pendingMessage'.json_encode($item));
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->where('order_status','1')->get()->toArray();
            if(empty($order_info))
            {
                $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                Log::info('待支付msg数据异常');
                return '待支付msg没有数据异常';
            }
            $cancel_time = config('wms.oms_cancel_time');
            $oms_over_time = $cancel_time-config('dlc.order_pending_remind');
            $oms_over_time = $oms_over_time<0?1:ceil($oms_over_time/60);
            $oms_over_time = $oms_over_time>59?(ceil($oms_over_time/60).'小时'):($oms_over_time.'分钟');
            $data = [
                'thing4'  => ['value' => "您有订单未支付，请在{$oms_over_time}内完成支付哦"],
                //下单时间
                'date8'   => ['value' => date('Y年m月d日',strtotime($order_info[0]->created_at))],
                //失效日期
                'date3'   => ['value' => date('Y年m月d日 H:i',strtotime($order_info[0]->created_at." +{$cancel_time} seconds"))],
                //订单金额
                'amount5' => ['value' => sprintf("%.2f",$order_info[0]->total_amount??0)],
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data, $page,$item->state);
            Log::info('待支付msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('待支付msg没有数据');
            return '待支付msg没有数据';
        }
    }

    /**
     * [paidMessage 付款提醒]
     * @Author   Peien
     * @DateTime 2020-04-02T10:33:44+0800
     * @return   [type]                   [description]
     */
    public function paidMessage($order_id = '')
    {   
        $item=SubscribeShipped::query()
            ->where('template_status','accept')
            ->where('msg_status',1)
            ->where('type',2)
            ->where('order_sn', $order_id)
            ->first();
        Log::info('orderSn:'.$order_id);
        Log::info('paidMessage'.json_encode($item));
        Log::info('paidMessageSql'.SubscribeShipped::query()
                ->where('template_status','accept')
                ->where('msg_status',1)
                ->where('type',2)
                ->where('order_sn', $order_id)->toSql());
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->get()->toArray();
            if(empty($order_info))
            {
                 $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                 return true;
            }
            $data = [
                //订单号
                'character_string2' => ['value' => $order_info[0]->order_sn],
                //下单时间
                'date4'    => ['value' => date('Y年m月d日',strtotime($order_info[0]->created_at))],
                //订单金额
                'amount16' => ['value' => sprintf("%.2f",$order_info[0]->total_amount??0).'元'],
                //收货地址
                'thing15'  => ['value' => $order_info[0]->province.$order_info[0]->city.$order_info[0]->district],
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data, $page,$item->state);
            Log::info('已付款msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('已付款msg没有数据');
            return '已付款msg没有数据';
        }
    }

    /**
     * [shippedMessage 发货]
     * @Author   Peien
     * @DateTime 2020-09-09T16:50:43+0800
     * @return   [type]                   [description]
     */
    public  function shippedMessage($order_id = '')
    {
        $item=$this
            ->where('template_status','accept')
            ->where('msg_status',1)
            ->where('type',3)
            ->where('order_sn' , $order_id)
            ->first();
            Log::info('shipMessage'.json_encode($item));
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->where('order_status','4')->get()->toArray();
            if(empty($order_info))
            {
                $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                Log::info('发货订单msg数据异常');
                return '发货订单msg数据异常'; 
            }
            $data = [
                //订单编号
                'character_string7' => ['value' => $order_info[0]->order_sn],
                //物流公司
                'thing14'           => ['value' => array_get(\App\Model\Order::$delivery_mode_map,$order_info[0]->delivery_mode)],
                //快递单号
                'character_string3' => ['value' => $order_info[0]->express_no?? ''],
                //收货地址
                'thing8'            => ['value' => $order_info[0]->province.$order_info[0]->city.$order_info[0]->district],
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data,$page,$item->state);
            Log::info('发货订单msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('发货订单msg没有数据');
            return '发货订单msg没有数据';
        }
    }

    /**
     * [refundMessage 退款]
     * @Author   Peien
     * @DateTime 2020-09-09T16:50:51+0800
     * @return   [type]                   [description]
     */
    public function refundMessage($order_id = '')
    {
        $item=$this
            ->where('template_status','accept')
            ->where('msg_status',1)
            ->where('type',4)
            ->where('order_sn' , $order_id)
            ->first();
            Log::info('cancelMessage'.json_encode($item));
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->get()->toArray();
            if(empty($order_info))
            {
                $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                Log::info('订单退款msg数据异常');
                return '订单退款msg数据异常'; 
            }
            $data = [
                'character_string1' => ['value' => $order_info[0]->order_sn],
                'amount2' => ['value' => bcadd($order_info[0]->total_amount,0,2).'元'],
                'time3' => ['value' => date('Y年m月d日 H:i:s')],
                'phrase5' => ['value' => '成功']
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data,$page,$item->state);
            Log::info('订单退款msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('订单退款msg没有数据');
            return '订单退款msg没有数据';
        }

    }
    /**
     * [cancelMessage 订单取消]
     * @Author   Peien
     * @DateTime 2020-04-02T10:34:57+0800
     * @return   [type]                   [description]
     */
    public function cancelMessage($order_id ='')
    {
        $item=$this
            ->where('template_status','accept')
            ->where('msg_status',1)
            ->where('type',5)
            ->where('order_sn' , $order_id)
            ->first();
            Log::info('cancelMessage'.json_encode($item));
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->get()->toArray();
            if(empty($order_info))
            {
                $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                Log::info('取消订单msg没有数据有误');
                return '取消订单msg没有数据有误'; 
            }
            $data = [
                //温馨提示
                'thing5' => ['value' => '您的订单已取消,点击查看订单详情'],
                //订单号
                'character_string1' => ['value' => $order_info[0]->order_sn],
                //下单时间
                'date6'   => ['value' => date('Y年m月d日 H:i',strtotime($order_info[0]->created_at))],
                //订单金额
                'amount3' => ['value' => sprintf("%.2f",$order_info[0]->total_amount??0)],
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data,$page,$item->state);
            Log::info('取消订单msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('取消订单msg没有数据');
            return '取消订单msg没有数据';
        }
    }

    public function finishMessage($order_id ='')
    {
        $item=$this
            ->where('template_status','accept')
            ->where('msg_status',1)
            ->where('type',6)
            ->where('order_sn' , $order_id)
            ->first();
        Log::info('finishMessage'.json_encode($item));
        if($item) {
            $order_info = \DB::table('oms_order_main')->where('id',$order_id)->get()->toArray();
            if(empty($order_info))
            {
                $this->where('order_sn', $order_id)->where('type', $item->type)->update(['msg_status'=>0]);
                Log::info('完成订单msg没有数据有误');
                return '完成订单msg没有数据有误';
            }
            $data = [
                //订单号
                'character_string1' => ['value' => $order_info[0]->order_sn],
                //快递公司
                'name3' => ['value' => array_get(\App\Model\Order::$delivery_mode_map,$order_info[0]->delivery_mode)],
                //快递单号
                'character_string4'   => ['value' => $order_info[0]->express_no],
                //订单状态
                'phrase2' => ['value' => '已完成'],
            ];
            $page = "pages/order-detail/order-detail?orderSn={$order_info[0]->order_sn}";
            $result = $this->sendInfo($item, $data,$page,$item->state);
            Log::info('完成订单msg有数据'.'page='.$page);
            return $result;
        }else{
            Log::info('完成订单msg没有数据');
            return '完成订单msg没有数据';
        }
    }

    /**
     * [arrivalMessage 到货通知]
     */
    public function arrivalMessage($params,$open_id,$template_id,$state)
    {
        $name = array_get($params,'name');
        $spec = array_get($params,'spec');
        $data = [
            'thing5' => ['value' => cutSubstr($name,17)],
            'thing8' => ['value' => cutSubstr($spec,17)],
            'amount6' => ['value' => sprintf("%.2f",array_get($params,'amount'))],
            'date7'   => ['value' => array_get($params,'date')],
            'thing4'   => ['value' => '您关注的好物已补货'],
        ];
        $page = "pages/pdt-detail/pdt-detail?id=".array_get($params,'spu');
        $result = $this->sendMessageInfo($data,$open_id,$template_id,$page,$state);
        Log::info('到货通知msg有数据'.'page='.$page);
        return $result;
    }

    /**
     * [sendInfo description]
     * @Author   Peien
     * @DateTime 2020-04-02T13:45:50+0800
     * @return   [type]                   [description]
     */
    public function sendInfo($item, $data, $page,$miniprogram_state)
    {
        $http = new Http();
        $token=$this->getAccessToken();
        $params = [
            'touser' => $item->openid,
            'template_id' => $item->template_id,
            'data' => $data,
            'page' => $page,
        ];
        $miniprogram_state_map = [
            'develop'=>'developer',
            'trial'=>'trial',
            'release'=>'formal',
        ];
        if(array_key_exists($miniprogram_state,$miniprogram_state_map)){
            $params['miniprogram_state'] = $miniprogram_state_map[$miniprogram_state];
        }
        Log::info('params'.json_encode($params));
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $token;
        $response = $http->post($url, $params);
        Log::info('response'.json_encode($response));
        if ($response['errcode'] === 0) {
            $this->where('order_sn', $item->order_sn)->where('type', $item->type)->update(['msg_status'=>0]);
            return true;
        } else {
            return $response;
        }
    }

    public function wxApi($postUrl, $param)
    {
        $fullPostUrl    =   $postUrl . $this->accessToken;
        //请求的URL中，token要用最新的
        $result = $this->curlWx($fullPostUrl, $param);
        if ($this->isTokenInvaild($result)) {
            $this->resetAccessToken(true);
            $fullPostUrl    =   $postUrl . $this->accessToken;
            //Token刷新，请求的URL中Token也要变了
            $result = $this->curlWx($fullPostUrl, $param);
        }
        return $result;
    }


    public function getAccessToken()
    {
        $postUrl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('SHAREAPPID')."&secret=".env('SHARESECRET');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 9_1 like Mac OS X) AppleWebKit/601.1.46 (KHTML, like Gecko) Version/9.0 Mobile/13B143 Safari/601.1');
        curl_setopt($ch, CURLOPT_HEADER, 0); // no headers in the output
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // output to variable
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 0); //post提交方式
        $result = curl_exec($ch);
        curl_close($ch);
       /* error_log(print_r([
            'time'=>date('Y-m-d H:i:s'),
            'response'=>$result
        ],true),3,'../storage/logs/accessToken.log');*/

        $token=json_decode($result, true);
        return $token['access_token'];
    }

    public function sendMessageInfo($data,$open_id,$template_id,$page,$miniprogram_state)
    {
        $http = new Http();
        $token=$this->getAccessToken();
        $params = [
            'touser' => $open_id,
            'template_id' => $template_id,
            'data' => $data,
            'page' => $page,
        ];
        $miniprogram_state_map = [
            'develop'=>'developer',
            'trial'=>'trial',
            'release'=>'formal',
        ];
        if(array_key_exists($miniprogram_state,$miniprogram_state_map)){
            $params['miniprogram_state'] = $miniprogram_state_map[$miniprogram_state];
        }
        Log::info('params'.json_encode($params));
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=' . $token;
        $response = $http->post($url, $params);
        Log::info('response'.json_encode($response));
        if ($response['errcode'] === 0) {
            return false;
        } else {
            return $response;
        }
    }

}