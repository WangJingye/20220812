<?php

namespace App\Http\Controllers\Api;

use App\Model\Address;
use App\Model\User;
use App\Service\CrmUsersService;
use App\Service\Guide\EmployeeService;
use App\Service\UsersService;
use App\Services\Api\UserServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Favorite;
use Validator;
use Exception;
use App\Model\Help;
//use Illuminate\Support\Facades\Redis;
use App\Model\Redis;


class UserNoLoginController extends Controller
{


    public function getMemberInfo(Request $request){
        try{
            $req_id = md5(time());
            Help::Log('getMemberInfo start api:'.$req_id, [microtime(true)],'im');
            $param = $request->all();
            $v = Validator::make($param, [
                'user_id' =>'required',
            ], [
                'required' => '用户ID不可为空',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }

            $user_id = $request->user_id;

            $user_info = UsersService::getUserInfoFromCache($user_id);
            if(!$user_info) throw new Exception("用户信息获取失败", 0);
            Help::Log('user_info end api:'.$req_id, [microtime(true)],'im');

            $coupons = UsersService::getCouponsFromCache($user_id);
            Help::Log('coupons end api:'.$req_id, [microtime(true)],'im');
            Help::Log('用户获取的券信息',$coupons);
            $coupon_ids = [];
            if($coupons)
                $coupon_ids = array_column($coupons,'coupon_id');

            if($coupon_ids){
                Help::Log('prom start api:'.$req_id, [microtime(true)],'im');
                list($suc,$infos) = UsersService::getCouponInfosFromApi($coupon_ids);
                Help::Log('prom end api:'.$req_id, [microtime(true)],'im');
                Help::Log('prom 获取券信息',$infos);
                if($suc){
                    foreach($coupons as $coupon){
                        if(!empty($coupon['used_at'])) {
                            Help::Log("{$user_id}券已使用:",$coupon);
                            continue;
                        }
                        $info = $infos[$coupon['coupon_id']]??[];
                        if(UsersService::checkCouponLegal($info,$coupon)){
                            $end = ( !empty($info['expire_days']))?min((strtotime($coupon['received_at']) + $info['expire_days'] * 86400),$info['end'] ):$info['end'];
                            $ret['coupon_list'][] = [
                                'coupon_id'=>$info['id'],
                                'coupon_name'=>$info['desc']??'',
                                'start'=>date('Y-m-d H:i:s',$info['start']),
                                'end'=>date('Y-m-d H:i:s',$end)
                            ];
                        }else{
                            Help::Log("{$user_id}券不合法:",[$coupon,$info]);
                        }
                    }
                }
            }

            $ret['coupon_list'] = $ret['coupon_list']??[]; //券信息
            Help::Log("{$user_id}获取的券信息:",$ret['coupon_list']);

            if(!$request->no_crm){
                Help::Log("{$user_id}获取的crm信息:",[],'im');
                $crmService = new CrmUsersService();
                $crm_user_info = $crmService->userInfo(['pos_id'=>$user_info['pos_id'],'phone'=>$user_info['phone']]);
                Help::Log('crm_user_info end api:'.$req_id, [microtime(true)],'im');
                $ret['total_points'] = $crm_user_info['AvailablePoints']??0;    //积分信息
            }
            $ret['total_points'] = $ret['total_points']??0;    //积分信息
            if(empty($user_info['pos_id'])){
                //如果缓存中没有则去posid缓存中获取
                $user_info['pos_id'] = \App\Service\Dlc\UsersService::getPosIdByUid($user_id);
            }
            $ret['pos_id'] = $user_info['pos_id']??0;    //POS ID

            $address = UsersService::getAddressFromCache($user_id);
            Help::Log('address end api:'.$req_id, [microtime(true)],'im');

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
            Help::Log('all end api:'.$req_id, [microtime(true)],'im');

            $emp = new EmployeeService();
            $guideInfo = $emp->getGuideInfo($user_id);
            $ret = array_merge($ret,$guideInfo??[]);

            return $this->success('成功',$ret);

        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }


    }

}
