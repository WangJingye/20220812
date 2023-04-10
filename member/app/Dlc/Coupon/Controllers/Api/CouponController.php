<?php namespace App\Dlc\Coupon\Controllers\Api;

use App\Model\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Validator;
use App\Dlc\Coupon\Service\CouponService;

class CouponController extends ApiController
{
    /**
     * 优惠券列表
     * @param Request $request
     * @return array|mixed
     */
    public function couponList(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'status' => 'required',
            ], [
                'required' => ':attribute是必填项',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $uid = $this->getUid();
            $status = $request->get('status');
            $couponService = CouponService::getInstance();
            $list = $couponService->couponList($uid,$status);
            return $this->success('success',compact('list'));
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 核销(内部调用)
     * @param Request $request
     * @return array|mixed
     */
    public function couponUse(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'uid' => 'required',
                'coupon_id' => 'required',
            ], [
                'required' => ':attribute是必填项',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $couponService = CouponService::getInstance();
            $res = $couponService->couponUse($request->get('uid'),$request->get('coupon_id'));
            if($res){
                return $this->success('success');
            }throw new \Exception('优惠码核销失败');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 反核销(内部调用)
     * @param Request $request
     * @return array|mixed
     */
    public function couponBack(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'uid' => 'required',
                'coupon_id' => 'required',
            ], [
                'required' => ':attribute是必填项',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $couponService = CouponService::getInstance();
            $res = $couponService->couponBack($request->get('uid'),$request->get('coupon_id'));
            if($res){
                return $this->success('success');
            }throw new \Exception('优惠码归还失败');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function couponListByPage(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'uid' => 'required',
            ], [
                'required' => ':attribute是必填项',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $uid = $request->get('uid');
            $couponService = CouponService::getInstance();
            $list = $couponService->couponListByPage($uid);
            return $this->success('success',$list);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * 发放优惠券（内部调用）
     * @param Request $request
     * @return array|mixed
     */
    public function couponSend(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'coupon_id' => 'required',
                'mobiles' => 'required',
            ], [
                'required' => ':attribute是必填项',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $mobiles = explode(',',$request->get('mobiles'));
            $userIds = Users::whereIn('phone',$mobiles)->pluck('id');
            if(empty($userIds)){
                throw new \Exception('未找到相应用户');
            }
            $userIds = $userIds->toArray();
            $couponService = CouponService::getInstance();
            $res = $couponService->couponUseHandle($request->get('coupon_id'),$userIds);
            if($res){
                return $this->success('优惠券发放成功');
            }throw new \Exception('优惠券发放失败');
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

}