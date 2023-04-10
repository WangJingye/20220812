<?php namespace App\Dlc\Coupon\Controllers\Api;

use App\Service\CrmUsersService;
use App\Service\Guide\EmployeeService;
use App\Service\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Validator;
use App\Dlc\Coupon\Service\CouponService;

class UserController extends ApiController
{
    public function getMemberInfo(Request $request){
        try{
            $param = $request->all();
            $v = Validator::make($param, [
                'uid' =>'required',
            ], [
                'required' => '用户ID不可为空',
            ]);
            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $user_id = $request->get('uid');
            $user_info = UsersService::getUserInfoFromCache($user_id);
            if(!$user_info) throw new Exception("用户信息获取失败", 0);
            //获取可用的优惠券列表
            $ret['coupon_list'] = CouponService::getInstance()->couponList($user_id,0,1);

            $address = UsersService::getAddressFromCache($user_id);
            foreach($address as $one){
                //地址信息
                $ret['shipping_address'][] = [
                    'shipping_address_id'=>$one['id'],
                    'name'=>$one['name'],
                    'sex'=>$one['sex'],
                    'mobile'=>$one['mobile'],
                    'province'=>$one['province'],
                    'city'=>$one['city'],
                    'district'=>$one['area'],
                    'address_detail'=>$one['address'],
                    'post_code'=>$one['zip_code'],
                    'is_default'=>$one['is_default'],
                ];
            }

            $emp = new EmployeeService();
            $guideInfo = $emp->getGuideInfo($user_id);
            $ret = array_merge($ret,$guideInfo??[]);
            return $this->success('成功',$ret);

        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }


    }

}