<?php namespace App\Services\Dlc;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Model\{Order,OmsOrderComment,OmsOrderReturnApply};
use App\Services\DLCOms\Request as Oms;
use Illuminate\Support\Arr;

class SalesServices
{
    /**
     * 获取OMS地址(缓存)
     * @return array|mixed
     */
    public static function getAddress(){
        $key = 'oms_address';
        $json = Redis::get($key);
        if(!$json){
            $list = DB::table('dlc_oms_address')->get();
            $address = [];
            $map_p = $map_c = [];
            $count_p = $count_c = 0;
            foreach($list as $item){
                $province = $item->province;
                if(!in_array($province,$map_p)){
                    ++$count_p;
                    $key_p = 'P'.$count_p;
                }else{
                    $key_p = 'P'.array_search($province,$map_p);
                }
                $map_p[$count_p] = $province;
                $address[0][$key_p] = $province;

                $city = $item->city;
                if(!in_array($city,$map_c)){
                    ++$count_c;
                    $key_c = 'C'.$count_c;
                }else{
                    $key_c = 'C'.array_search($city,$map_c);
                }
                $map_c[$count_c] = $city;
                $address[$key_p][$key_c] = $city;

                $district = $item->district;
                $address[$key_c][] = $district;
            }
            //保存到缓存
            Redis::setex($key, 3600*24*7, json_encode($address));
            return $address;
        }
        $address = json_decode($json,true);
        return $address;
    }

    public static function getOrderStatusCount($uid){
        $model = Order::query();
        $model = $model->where('user_id', $uid);
        //获取1个月内的订单
        $last_month = date("Y-m-d H:i:s", strtotime("-1 month"));
        return $model->whereIn('order_status',[1,3,4,12,9,10])
            ->select('order_status',DB::raw('COUNT(*) as count'))
            ->where('created_at','>',$last_month)
            ->groupBy('order_status')->pluck('count','order_status');
    }

    /**
     * 获取物流轨迹
     * @param $order_sn
     * @param $uid
     * @return string
     */
    public static function getLogistics($order_sn,$uid){
        $logi_no = Order::query()->where('user_id',$uid)
            ->where('order_sn',$order_sn)
            ->value('express_no');
        if($logi_no){
            $oms = new Oms;
            $resp = $oms->getLogistics([
                'order_bn'=>$order_sn,
                'logi_no'=>$logi_no,
            ]);
            //临时调试
//            $resp = [
//                'code'=>'200',
//                'data'=>[
//                    ['time'=>"2019-01-05 18:30:15",'addr'=>'上海市','remark'=>'快件已发车'],
//                    ['time'=>"2019-01-06 18:30:15",'addr'=>'上海市','remark'=>'杨浦中转站已收货等待运输'],
//                ],
//            ];
            //end
            if($resp['code']=='200'){
                if(!empty($resp['data'])){
                    array_multisort(array_column($resp['data'],'time'),SORT_DESC,$resp['data']);
                }
                return $resp['data'];
            }return $resp['message'];
        }return '快递单号不存在';
    }

    /**
     * 调用oms接口合并会员code
     * @param $old_member_code
     * @param $new_member_code
     * @return bool|string
     */
    public static function omsOrderMerge($old_member_code,$new_member_code){
        $order_sn_list = Order::query()->where('pos_id',$old_member_code)->get()->pluck('order_sn');
        if($order_sn_list){
            $order_sn_list = $order_sn_list->toArray();
            $params = [
                'relate_order_bn'=>$order_sn_list,
                'old_member_code'=>$old_member_code,
                'new_member_code'=>$new_member_code,
            ];
            $oms = new Oms;
            $resp = $oms->memberUpdateCode($params);
            if($resp===true){
                return true;
            }return $resp;
        }return false;
    }

    public static function messUpdateOrderPosId($old_member_code,$new_member_code){
        Order::query()->where('pos_id',$old_member_code)->update(['pos_id'=>$new_member_code]);
        return true;
    }

    public static function updateOrderPosIdByUid($uid,$member_code){
        Order::query()->whereRaw("(pos_id=0 or pos_id='' or pos_id is null)")
            ->where('user_id',$uid)->update(['pos_id'=>$member_code]);
        return true;
    }

    public static function getAllProvince(){
        return DB::table('dlc_oms_address')->select('province')->distinct()->pluck('province')->toArray();
    }

    public static function hasPayPendingFlag($order_sn){
        $key = Order::PAY_PENDING_FLAG.":{$order_sn}";
        return Redis::get($key)?true:false;
    }

    public static function addPayPendingFlag($order_sn){
        $key = Order::PAY_PENDING_FLAG.":{$order_sn}";
        Redis::setex($key, 600, time());
        return true;
    }

    public static function removePayPendingFlag($order_sn){
        $key = Order::PAY_PENDING_FLAG.":{$order_sn}";
        Redis::del($key);
        return true;
    }

    const ARRIVAL_REMINDER = 'arrival_reminder';
    public static function setArrivalReminder($sku,$open_id,$data){
        $key = self::ARRIVAL_REMINDER.':'.$sku;
        Redis::hset($key,$open_id,$data);
    }

    /**
     * @param $sku
     * @return mixed
     */
    public static function getArrivalReminders($sku){
        $key = self::ARRIVAL_REMINDER.':'.$sku;
        return Redis::hgetall($key);
    }

    public static function hdelArrivalReminder($sku,$open_id){
        $key = self::ARRIVAL_REMINDER.':'.$sku;
        Redis::hdel($key,$open_id);
        return true;
    }

    /**
     * 评论提交
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public static function omsCommentUpdate($params){
        $order_sn = Arr::get($params,'order_sn');
        $insert_data = [
            'order_sn'=>$order_sn,
            'score_p'=>Arr::get($params,'score_p'),
            'score_cs'=>Arr::get($params,'score_cs'),
            'score_l'=>Arr::get($params,'score_l'),
            'content'=>Arr::get($params,'content',''),
        ];
        $allow_order_status = [9,10];
        $order = Order::query()->where('order_sn',$order_sn)->first();
        if(empty($order)){
            throw new \Exception('订单不存在');
        }
        if(!in_array($order->order_status,$allow_order_status)){
            throw new \Exception('订单状态不符合条件');
        }
        if(($order->is_comment===0) && $order->update(['is_comment'=>1])){
            OmsOrderComment::insertIgnore($insert_data);
        }
        return true;
    }

    /**
     * @param $order_sn
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public static function omsCommentGet($order_sn){
        return OmsOrderComment::query()->find($order_sn)?:[];
    }

    /**
     * 退货申请
     * @param $params
     * @return bool
     * @throws \Exception
     */
    public static function omsReturnApplyRequest($params){
        $order_sn = Arr::get($params,'order_sn');
        $insert_data = [
            'order_sn'=>$order_sn,
            'content'=>Arr::get($params,'content',''),
        ];
        $allow_order_status = [3,4];
        $order = Order::query()->where('order_sn',$order_sn)->first();
        if(empty($order)){
            throw new \Exception('订单不存在');
        }
        if($order->is_apply_return==1){
            throw new \Exception('您的退货退款申请已提交,请勿重复申请');
        }
        if(!in_array($order->order_status,$allow_order_status)){
            throw new \Exception('订单状态不符合条件');
        }
        if(($order->is_apply_return===0) && $order->update(['is_apply_return'=>1])){
            OmsOrderReturnApply::query()->insert($insert_data);
        }
        return true;
    }

    public static function omsReturnAllow($orderId,$type){
        $order = Order::query()->find($orderId);
        if(empty($order)){
            throw new \Exception('订单不存在');
        }
        if(!in_array($type,['allow','forbid','confirm'])){
            throw new \Exception('状态错误');
        }
//        if($order->is_apply_return!=1){
//            throw new \Exception('该订单未提交退货退款申请');
//        }
        DB::beginTransaction();
        try{
            if($type=='allow'){//允许退货
                $order->update(['order_status'=>8,'is_apply_return'=>1,'is_allow_return'=>1]);
                OmsOrderReturnApply::query()->where('order_sn',$order->order_sn)
                    ->where(['status'=>0])->update(['status'=>1]);
            }elseif($type=='forbid'){//拒绝退货
                $order->update(['is_allow_return'=>2]);
                OmsOrderReturnApply::query()->where('order_sn',$order->order_sn)
                    ->where(['status'=>0])->update(['status'=>2]);
            }elseif($type=='confirm'){//允许退款
                $order->update(['order_status'=>5,'is_return_wms'=>1]);
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
        return true;
    }
}
