<?php namespace App\Dlc\Coupon\Service;

use App\Dlc\Coupon\Model\Coupons;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;
use App\Dlc\Coupon\Service\PromotionService;

class CouponService extends BaseService
{
    /**
     * 获取促销详情
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getPromotionDetail($id){
        $detail = PromotionService::getInstance()->getDetail($id);
        if(empty($detail)){
            throw new \Exception('促销不存在');
        }return $detail;
    }

    /**
     * 获取新人礼促销详情
     * @return mixed
     * @throws \Exception
     */
    public function getPromotionNewDetail(){
        $uri = PROMOTION.'coupon/inner/getNewDetail';
        $request = Request::getInstance();
        $resp = $request->request($uri,'POST');
        if($resp['code']==1){
            $promotion = $resp['data'];
            return $promotion;
        }throw new \Exception($resp['message']);
    }

    /**
     * 判断是否有效
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function checkValidate(array $data){
        if($data){
            //判断状态
            if($data['status']!=2){
                throw new \Exception('促销未启用');
            }
            //判断日期
            $currDate = date('Y-m-d H:i:s');
            if($currDate<$data['start_time']||$currDate>$data['end_time']){
                throw new \Exception('促销未在有效时间内');
            }
            return true;
        }throw new \Exception('无数据');
    }

    /**
     * 优惠券核销
     * @param $uid
     * @param $couponId
     * @throws \Exception
     */
    public function couponUse($uid,$couponId){
        try{
            $coupon = Coupons::where('uuid',md5($uid.'_'.$couponId))->first();
            if(empty($coupon)){
                throw new \Exception('优惠码不存在');
            }
            $coupon->update(['status'=>1]);
        }catch (\Exception $e){
            $errMsg = $e->getMessage();
        }
        $status = isset($errMsg)?0:1;
        HelperService::getInstance()->log('couponUse',[
            'status'=>$status,
            'uid'=>$uid,
            'couponId'=>$couponId,
            'errMsg'=>$errMsg??'',
        ]);
        return $status;
    }

    /**
     * 优惠券反核销
     * @param $uid
     * @param $couponId
     * @return int
     */
    public function couponBack($uid,$couponId){
        try{
            $coupon = Coupons::where('uuid',md5($uid.'_'.$couponId))->first();
            if(empty($coupon)){
                throw new \Exception('优惠码不存在');
            }
            $coupon->update(['status'=>0]);
        }catch (\Exception $e){
            $errMsg = $e->getMessage();
        }
        $status = isset($errMsg)?0:1;
        HelperService::getInstance()->log('couponBack',[
            'status'=>$status,
            'uid'=>$uid,
            'couponId'=>$couponId,
            'errMsg'=>$errMsg??'',
        ]);
        return $status;
    }

    /**
     * 调用促销接口增加临朐数量(批量)
     * @param $pluck
     * @return bool
     * @throws \Exception
     */
    private function couponUsePluck($pluck){
        $uri = PROMOTION.'coupon/inner/couponUsePluck';
        $request = Request::getInstance();
        $resp = $request->request($uri,'POST',['ids_pluck'=>$pluck]);
        if($resp['code']==1){
            return true;
        }throw new \Exception($resp['message']);
    }

    /**
     * 处理领取优惠券
     * @param int $couponId
     * @param $userIds
     * @throws \Exception
     */
    public function couponUseHandle(int $couponId,$userIds){
        //获取促销信息
        /** @var \App\Dlc\Coupon\Service\CouponService $couponService */
        $couponService = CouponService::getInstance();
        $promotion = $couponService->getPromotionDetail($couponId);
        //检查促销是否有效
        $couponService->checkValidate($promotion);
        //将用户ID转成数组统一处理
        if(is_array($userIds)){
            $count = count($userIds);
        }else{
            $userIds = [$userIds];
            $count = 1;
        }
        //判断剩余库存
        if((intval($promotion['coupon_stock'])-intval($promotion['coupon_stock_used']))<$count){
            throw new \Exception('库存不足');
        }
        //领取优惠券
        DB::beginTransaction();
        try {
            $startTime = date('Y-m-d 00:00:00');
            $expireDays = intval($promotion['expire_days']);
            $endTime = date('Y-m-d 23:59:59',strtotime("+{$expireDays} day"));
            if($endTime>$promotion['end_time']){
                $endTime = $promotion['end_time'];
            }
            $insertData = $uuids = [];
            foreach($userIds as $userId){
                $uuid = md5($userId.'_'.$couponId);
                $uuids[] = $uuid;
                $insertData[$userId] = [
                    'uid'=>$userId,
                    'coupon_id'=>$couponId,
                    'is_new'=>$promotion['is_new'],
                    'type'=>$promotion['type'],
                    'start_time'=>$startTime,
                    'end_time'=>$endTime,
                    'uuid'=>$uuid,
                ];
            }
            //过滤掉已发放的
            $existsUids = Coupons::whereIn('uuid',$uuids)->pluck('uid');
            if($existsUids->count()){
                foreach($existsUids as $existsUid){
                    unset($insertData[$existsUid]);
                }
            }
            //增加绑定记录
            Coupons::insert(array_values($insertData));
            //增加领取数量
            $couponService->couponUsePluck([$couponId=>$count]);
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
        }return false;
    }


    /**
     * 优惠券列表
     * @param $uid
     * @param $status
     * @param int $simple
     * @return array
     */
    public function couponList($uid,$status,$simple=0){
        $currDate = date('Y-m-d H:i:s');
        if($status==0){
            //可用的
            $list = Coupons::where('uid',$uid)->where('status',0)
                ->where('end_time','>=',$currDate)->orderBy('created_at','desc')->get();
        }elseif($status==1){
            //已核销
            $list = Coupons::where('uid',$uid)->where('status',1)
                ->where('end_time','>=',$currDate)->orderBy('created_at','desc')->get();
        }elseif($status==2){
            //已过期
            $list = Coupons::where('uid',$uid)
                ->where('end_time','<',$currDate)->orderBy('created_at','desc')->get();
        }
        $couponList = [];
        if(isset($list) && $list->count()){
            $list = $list->toArray();
            $couponIds = array_column($list,'coupon_id');
            $promotionService = PromotionService::getInstance();
            $promotions = $promotionService->getDetails($couponIds);
            foreach($list as $item){
                $promotion = $promotions[$item['coupon_id']];
                $_item = [
                    'coupon_id'=>$item['coupon_id'],
                    'display_name'=>$promotion['display_name'],
                    'name'=>$promotion['name'],
                    'start_time'=>$item['start_time'],
                    'end_time'=>$item['end_time'],
                    'notice'=>$promotion['notice'],
                ];
                if($simple==1){
                    unset($_item['name']);
                    unset($_item['notice']);
                }
                if($item['type']=='coupon'){
                    $_item['title'] = $promotion['total_discount'];
                    if($promotion['total_amount']==1){
                        $_item['condition'] = "无门槛使用";
                    }else{
                        $_item['condition'] = "满{$promotion['total_amount']}减{$promotion['total_discount']}";
                    }
                }elseif($item['type']=='product_coupon'){
                    $_item['title'] = '随单礼';
                    $_item['condition'] = "正装满1件送";
                }
                $couponList[] = $_item;
            }
        }return $couponList;
    }

    /**
     * 获取可用的优惠券列表
     * @param $uid
     * @return array
     */
    public function couponActiveList($uid){
        $currDate = date('Y-m-d H:i:s');
        $list = Coupons::where('uid',$uid)->where('status',0)
            ->where('end_time','>=',$currDate)->orderBy('created_at','desc')->get();
        $couponList = [];
        foreach($list as $item){
            $couponList[] = [
                'coupon_id' => $item['coupon_id'],
                'start' => $item['start_time'],
                'end' => $item['end_time'],
            ];
        }return $couponList;
    }

    public function couponListByPage($uid){
        $list = Coupons::query()->where('uid',$uid)->orderBy('created_at','desc')->paginate(request()->get('limit',10))->toArray();
        $data = $list['data'];
        $total = $list['total'];
        if($total>0){
            $couponIds = array_column($data,'coupon_id');
            $promotionService = PromotionService::getInstance();
            $promotions = $promotionService->getDetails($couponIds);
            foreach($data as $item){
                $promotion = $promotions[$item['coupon_id']];
                $_item = [
                    'coupon_id'=>$item['coupon_id'],
                    'display_name'=>$promotion['display_name'],
                    'name'=>$promotion['name'],
                    'type'=>$item['type'],
                    'status'=>$item['status'],
                    'start_time'=>$item['start_time'],
                    'end_time'=>$item['end_time'],
                    'created_at'=>$item['created_at'],
                ];
                $couponList[] = $_item;
            }
        }return [
            'data'=>$couponList??[],
            'total'=>$total,
        ];
    }


}