<?php namespace App\Http\Controllers\Api\Dlc;

use App\Http\Controllers\Controller;
use App\Jobs\OrderQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dlc\SalesServices;

class SalesController extends Controller
{
    /**
     * 省市区地址接口
     * @param Request $request
     * @return mixed
     */
    public function getOmsAddress(Request $request)
    {
        $address = SalesServices::getAddress();
        return $this->success('OK', $address);
    }

    /**
     * 内部调用获取指定订单状态的数量
     * @param Request $request
     * @return mixed
     */
    public function getStatusCount(Request $request){
        $uid = $request->get('uid');
        if($uid){
            $result = SalesServices::getOrderStatusCount($uid);
            return $this->success('OK',$result);
        }return $this->error();
    }

    /**
     * 获取物流轨迹
     * @param Request $request
     * @return mixed
     */
    public function getLogistics(Request $request){
        $v = Validator::make($request->all(), [
            "order_sn" => 'required',
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        try{
            $uid = $this->getUid();
            if(!$uid)return $this->expire();
            $order_sn = $request->get('order_sn');
            $result = SalesServices::getLogistics($order_sn,$uid);
            if(is_array($result)){
                return $this->success('OK',$result);
            }throw new \Exception($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 会员合并接口(内部调用)
     * @param Request $request
     * @return mixed
     */
    public function orderMerge(Request $request){
        $v = Validator::make($request->all(), [
            "OldMemberCode" => 'required',
            "NewMemberCode" => 'required',
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        try{
            $old_member_code = $request->get('OldMemberCode');
            $new_member_code = $request->get('NewMemberCode');
            $result = SalesServices::omsOrderMerge($old_member_code,$new_member_code);
            if($result===true){
                //调用oms接口合并成功,批量更新会员ID
                SalesServices::messUpdateOrderPosId($old_member_code,$new_member_code);
                return $this->success('合并成功');
            }elseif($result===false){
                return $this->success('旧会员ID没有订单,无需处理');
            }throw new \Exception($result);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

    }

    public function orderPosIdUpdate(Request $request){
        $v = Validator::make($request->all(), [
            "Uid" => 'required',
            "MemberCode" => 'required',
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        try{
            $uid = $request->get('Uid');
            $member_code = $request->get('MemberCode');
            Log::info(__FUNCTION__,[
                'uid'=>$uid,
                'member_code'=>$member_code,
            ]);
            SalesServices::updateOrderPosIdByUid($uid,$member_code);
            return $this->success('更新成功');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

    }

    public function getAllProvince(Request $request){
        try{
            $data = SalesServices::getAllProvince();
            return $this->success('OK',$data);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

    }

    /**
     * 前端支付成功后通知接口
     * @param Request $request
     * @return mixed
     */
    public function payPendingFlag(Request $request){
        try{
            $v = Validator::make($request->all(), [
                "order_sn" => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $order_sn = $request->get('order_sn');
            SalesServices::addPayPendingFlag($order_sn);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function goodsArrivalReminder(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'sku' => 'required',
                'open_id' => 'required',
                'template_id' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $sku = $request->get('sku');
            $open_id = $request->get('open_id');
            $template_id = $request->get('template_id');
            $state = $request->header('wxVersion')?:'';
            $data = json_encode(compact('template_id','state'));
            SalesServices::setArrivalReminder($sku,$open_id,$data);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function omsSync(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'orderId' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $orderId = $request->get('orderId');
            \App\Services\Dlc\OmsSync::run($orderId);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 评论提交
     * @param Request $request
     * @return mixed
     */
    public function omsCommentUpdate(Request $request){
        try{
            $params = $request->all();
            $validator = Validator::make($params, [
                'order_sn' => ['required','max:50'],
                'score_p' => ['required','numeric'],
                'score_cs' => ['required','numeric'],
                'score_l' => ['required','numeric'],
                'content' => ['nullable','max:500'],
            ],[
                'required' => ':attribute是必填项',
                'max' => ':attribute不能超过:max个字符',
                'regex' => ':attribute格式不正确',
                'in' => ':attribute不正确',
                'numeric' => ':attribute必须为数字',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            SalesServices::omsCommentUpdate($params);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function omsCommentGet(Request $request){
        try{
            $params = $request->all();
            $validator = Validator::make($params, [
                'order_sn' => ['required','max:50'],
            ],[
                'required' => ':attribute是必填项',
                'max' => ':attribute不能超过:max个字符',
                'regex' => ':attribute格式不正确',
                'in' => ':attribute不正确',
                'numeric' => ':attribute必须为数字',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            $order_sn = $params['order_sn'];
            $data = SalesServices::omsCommentGet($order_sn);
            return $this->success('OK',$data);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    /**
     * 退货申请
     * @param Request $request
     * @return mixed
     */
    public function omsReturnApplyRequest(Request $request){
        try{
            $params = $request->all();
            $validator = Validator::make($params, [
                'order_sn' => ['required','max:50'],
                'content' => ['nullable','max:500'],
            ],[
                'required' => ':attribute是必填项',
                'max' => ':attribute不能超过:max个字符',
                'regex' => ':attribute格式不正确',
                'in' => ':attribute不正确',
                'numeric' => ':attribute必须为数字',
            ]);
            if ($validator->fails()) {
                return $this->error($validator->errors()->first());
            }
            SalesServices::omsReturnApplyRequest($params);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }

    public function arrivalReminder(Request $request){
        $params = $request->all();
        $validator = Validator::make($params, [
            'skus_str' => ['required'],
        ],[
            'required' => ':attribute是必填项',
            'max' => ':attribute不能超过:max个字符',
            'regex' => ':attribute格式不正确',
            'in' => ':attribute不正确',
            'numeric' => ':attribute必须为数字',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $skus_str = $request->get('skus_str');
        $skus = explode(',',$skus_str);
        app(Dispatcher::class)->dispatch(new Queued(
            'arrivalReminder',
            ['skus'=>$skus])
        );
        return $this->success();
    }

    public function returnApplyStatusChange(Request $request){
        try{
            $v = Validator::make($request->all(), [
                'orderId' => 'required',
                'type' => 'required',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $orderId = $request->get('orderId');
            $type = $request->get('type');
            \App\Services\Dlc\SalesServices::omsReturnAllow($orderId,$type);
            return $this->success('OK');
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }
    }



}
