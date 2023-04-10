<?php

namespace App\Http\Controllers\Api;

use App\Model\UserCoupon;
use App\Service\UsersService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Model\WechatCoupons;
use App\Model\WechatUsers;
use Validator;
use Exception;
use App\Model\Help;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
	protected $wechatUserId;

	protected $crmId;

	protected $openId;

    /**
     * CouponController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
	{
	    parent::__construct();

//		$encryptString = array_get($request->all(), 'openid');
//
//		$decrypted = decrypt($encryptString);
//
//		$this->wechatUserId = $decrypted['wechatUserId'];
//
//		$this->crmId = $decrypted['crmId'];
//
//		$this->openId = $decrypted['openId'];

	}

	public function allCoupons(Request $request){
        try {
            $req_id = getmypid().rand(1,1000);
            Help::Log('allCoupons start api:'.$req_id, [microtime(true)],'im');
            $myCouponIds = [];
            $ori_coupons = $coupons = UsersService::getCouponsFromCache($this->user_id);
            Help::Log('ori_coupons end api:'.$req_id, [microtime(true)],'im');
            Help::Log($this->user_id.'券信息',$coupons);
            $coupons = array_combine(array_column($coupons,'coupon_id'),$coupons);
            $coupon_ids = array_column($coupons,'coupon_id');

            if(empty($coupon_ids)) {
                return response()->ajax('你还没有优惠券', []);
            }

            Help::Log('prom start api:'.$req_id, [microtime(true)],'im');
            list($suc,$infos) = UsersService::getCouponInfosFromApi($coupon_ids);
            Help::Log('prom end api:'.$req_id, [microtime(true)],'im');
            if(!$suc) throw new Exception($infos, 0);;

//            $skus = [];
//            foreach($infos as $info){
//                if($info['coupon_type'] == 'product_coupon'){
//                    if(!empty($info['sku'])) $skus[] = $info['sku'];
//                }
//            }
//
//            if($skus){
//                $skus = array_unique($skus);
//                $products = $this->http->curl('outward/product/getProductInfoBySkuIds', ['sku_ids' => implode(',',$skus),'simple'=>1]);
//                Help::Log('products end api:'.$req_id, [microtime(true)],'im');
//                Help::Log('outward/product/getProductInfoBySkuIds:',['sku_ids'=>implode(',',$skus),'products'=>$products]);
//                $products = $products['data']??[];
//            }
//
//            foreach($infos as $k=>$info){
//                if($info['coupon_type'] == 'product_coupon'){
//                    $p = $products[$info['sku']??'']??[];
//                    $infos[$k]['product_name'] = $p['product_name']??'';
//                    $infos[$k]['kv_image'] = $p['kv_image']??'';
//                }
//            }

            $ori_coupon_ids = array_column($ori_coupons,'coupon_id');
            Help::Log($this->user_id.'券ids信息',$ori_coupon_ids);
            $ret = UsersService::groupUserCoupons($ori_coupons,$infos);
            
//            foreach($ori_coupons as $coupon){
//                $info = $infos[$coupon['coupon_id']]??[];
//                if(!$info || !$coupon) continue;
//                $end = ( !empty($info['expire_days']))?min((strtotime($coupon['received_at']) + $info['expire_days'] * 86400),intval($info['end']) ):intval($info['end']);
//                $info['start'] = date('Y-m-d H:i:s',intval($info['start']));
//                $info['end'] = date('Y-m-d H:i:s',$end);
//                if(!empty($info['active']) && ($info['active'] == 3))    //未激活
//                    $info['status'] = 1;    //过期
//                else
//                    $info['status'] = ($end>time())?0:1;    //expire_days 的存在
//
//                if($coupon['used_at']) $ret['used_coupons'][] = $info;
//                elseif($info['status'] == 1 ) $ret['out_date_coupons'][] = $info;
//                elseif($info['status'] == 0 ) $ret['vaild_coupons'][] = $info;
//            }
            Help::Log('all end api:'.$req_id, [microtime(true)],'im');

            return response()->ajax('获取优惠券列表成功', $ret);

        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

//    public static function formatCoupon($coupon){
//
//    }


    // 有效优惠券（按照金额正序）
	public function list(Request $request)
	{
		try {
			$myCouponIds = [];
			$coupons = UsersService::getCouponsFromCache($this->user_id);
			$coupon_ids = array_column($coupons,'coupon_id');
//	        $myCouponData = WechatCoupons::where('wechat_user_id', $this->wechatUserId)->whereNull('used_at')->get()->each(function ($item) use (&$myCouponIds){
//	        	array_push($myCouponIds, $item->coupon_id);
//	        });

            if(empty($coupon_ids)) {
            	return response()->ajax('你还没有优惠券', ['list' => []]);
            }

			$url = env('PROMOTION_DOMAIN').'/promotion/coupon/list';

            $headers = ['Content-Type: application/json'];
            // 通过$myCouponIds 获取优惠券信息
            $response = http_request($url, $coupon_ids, $headers, 'POST', '获取优惠券信息curl：');
            if($response['httpCode'] !== 200) {
                throw new Exception("获取优惠券信息异常", 0);
                
            }
	        $result = json_decode($response['data'], true);
	        if(!$result['code']) {
            	throw new Exception($response['msg'], 0);
            			
            }
	        return response()->ajax('获取优惠券列表成功', ['list' => $result['data']]);

		} catch (Exception $e) {
        	return response()->errorAjax($e);
		}
	}


	// 执行领取优惠券操作
	public function action(Request $request)
	{
        Help::Log('领取优惠券',$request->all());
        try {
			$fields = [
				'coupon_id' => 'required|numeric',
//				'encryptedData' => 'required|string|max:1000',
//				'iv'  => 'required|string|max:255',
//				'type' => 'required'
		    ];
	    	$validator = Validator::make($request->all(), $fields, [
	            'required' => ':attribute 为必填项',//:attribute 字段占位符表示字段名称
	            'numeric'   => ':attribute 为数字',
	            'string'   => ':attribute 为字符串'
	        ]);
	        if($validator->fails()) {
	        	throw new Exception($validator->errors()->first(), 0);
	    	
	        }

	        $user = UsersService::getUserInfo($this->user_id);
			if(!$user){
				throw new Exception("用户不存在!", 0);
			}
			
	        $params = array_only($request->all(), array_keys($fields));

	        $couponId = array_get($params, 'coupon_id');
	        $couponType = array_get($params, 'type',2); // 1新客优惠券 2普通优惠券
	        $now = date('Y-m-d H:i:s');

            $coupons = UsersService::getCouponsFromCache($this->user_id);
            $coupon_ids = [];
            if($coupons)
                $coupon_ids = array_column($coupons,'coupon_id');

            Help::Log('用户优惠券信息',['uid'=>$this->user_id,'coupon_ids'=>$coupon_ids]);

            $tmp = Help::getControllerAndFunction();
            Help::Log('controller',$tmp);
            $controller = $tmp['controller']??'';
            //积分商城可以重复领取
	        if(($controller != 'PointmallController') && in_array($couponId,$coupon_ids)){
	        	throw new Exception("您已经领取过该优惠券", 0);
	        }

	        list($suc,$info) = UsersService::getCouponInfosFromApi($couponId);
	        if(!$suc) throw new Exception($info, 0);
            if(!isset($info['status']) || !isset($info['active'])) throw new Exception('券信息不正确', 0);
            if(($info['status'] == 1) || ($info['active'] == 3) ) throw new Exception("券失效", 0);

            //只有积分那边调取的，才可以领实物券
            if(($controller != 'PointmallController') && ($info['coupon_type'] == 'product_coupon')) throw new Exception("您无权领取此券", 0);

            list($suc,$msg) = UsersService::incrementCouponQtyFromApi($couponId);
            if(!$suc) throw new Exception($msg, 0);

            $ins = [
                'user_id'=>$this->user_id,
                'coupon_id'=>$couponId,
                'type'=>$couponType,
                'received_at'=>$now,
            ];

            $id = UserCoupon::insertGetId($ins);
            UsersService::clearUserCouponCache($this->user_id);

	        return response()->ajax('领取成功',['id'=>$id]);

	   	} catch (Exception $e) {
        	return response()->errorAjax($e);
		}
	}

	//内部调用发放接口
	public function apiGrantCoupon(Request $request){
        try{
            Help::Log('内部调用发放优惠券',$request->all());
            $couponId = $request->input('coupon_id');
            $user_id = $request->input('user_id');

            if(!$couponId || !$user_id) {
                throw new Exception("用户ID或优惠券ID不得为空！", 0);
            }

            $user = UsersService::getUserInfoFromCache($user_id);
            if(!$user){
                throw new Exception("用户不存在!", 0);
            }

            $couponType = 2; // 1新客优惠券 2普通优惠券
            $now = date('Y-m-d H:i:s');

            list($suc,$info) = UsersService::getCouponInfosFromApi($couponId);
            if(!$suc) throw new Exception($info, 0);
            if(!isset($info['status']) || !isset($info['active'])) throw new Exception('券信息不正确', 0);
            if(($info['status'] == 1) || ($info['active'] == 3) ) throw new Exception("券失效", 0);

            list($suc,$msg) = UsersService::incrementCouponQtyFromApi($couponId);
            if(!$suc) throw new Exception($msg, 0);

            $ins = [
                'user_id'=>$user_id,
                'coupon_id'=>$couponId,
                'type'=>$couponType,
                'received_at'=>$now
            ];

            $id = UserCoupon::insertGetId($ins);
            UsersService::clearUserCouponCache($user_id);

            return response()->ajax('领取成功',['id'=>$id]);

        }catch(\Exception $e){
            return response()->errorAjax($e);
        }

    }

    //查用户 某个券信息 接口
    public function apiGetUserCouponInfo(Request $request){
        try{
            Help::Log('使用优惠券',$request->all());
            $couponId = $request->input('coupon_id');
            $user_id = $request->input('user_id');

            if(!$couponId || !$user_id) {
                throw new Exception("用户ID或优惠券ID不得为空！", 0);
            }

            $all_coupons = $coupons = UsersService::getCouponsFromCache($user_id);

            $coupons = array_filter($all_coupons,function($coupon)use($couponId){
                if($coupon['coupon_id'] == $couponId) return true;
                return false;
            });

            return response()->ajax('查询成功',['num'=>count($coupons)]);
        }catch(\Exception $e){
            return response()->errorAjax($e);
        }


    }


    // 使用优惠券
    public function useCoupon(Request $request)
    {
        try {
            Help::Log('使用优惠券',$request->all());
            $couponId = $request->input('coupon_id');
            $user_id = $request->input('user_id');

            if(!$couponId || !$user_id) {
                throw new Exception("用户ID或优惠券ID不得为空！", 0);
            }
            $couponInfo = UserCoupon::where('coupon_id', $couponId)->where('user_id', $user_id)->where('used_at',NULL)->orderBy('received_at','asc')->first();
            Help::Log('券信息',$couponInfo);

            if(!$couponInfo){

                throw new Exception("优惠券不存在", 0);
            } else if($couponInfo->used_at) {
                throw new Exception("优惠券已使用", 0);
            } else {
                $res = UserCoupon::where('coupon_id', $couponId)->where('used_at',NULL)
                    ->where('id',$couponInfo['id'])
                    ->update(['used_at'=>date('Y-m-d H:i:s')]);
                Help::Log('使用结果',[$res,$request->all()],'im');
                UsersService::clearUserCouponCache($user_id);

                if($res){
                    return response()->ajax('使用优惠券成功');
                }
                return response()->errorAjax('使用优惠券失败');
            }


        } catch (Exception $e) {
            Help::Log('使用异常',$e->getMessage());
            return response()->errorAjax($e);
        }
    }

    //取消订单归还优惠券
    public function revertCoupon(Request $request)
    {
        try {
            Help::Log('回退参数:',$request->all());
            $user_id = $request->user_id;
            $couponId = $request->input('coupon_id');

            if(!$user_id){
                throw new Exception("用户信息缺失", 0);
            }

            if(!$couponId){
                throw new Exception("优惠券不存在", 0);
            }

            $couponInfo = UserCoupon::where('user_id', $user_id)->where('coupon_id', $couponId)->whereNotNull('used_at')->orderBy('used_at','desc')->limit(1)->update(['used_at'=>NULL,'revert_at'=>date('Y-m-d H:i:s')]);
            UsersService::clearUserCouponCache($user_id);
            Help::Log('回退信息:',[$couponInfo,$request->all()],'im');
            if(!$couponInfo) {
                throw new Exception("回退失败", 0);
            }
            return response()->ajax('success');
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

    //删除优惠券
    public function delCoupon(Request $request)
    {
        try {
            Help::Log('删除优惠券参数:',$request->all());
            $user_id = $request->user_id;
            $couponId = $request->input('coupon_id');

            if(!$user_id){
                throw new Exception("用户信息缺失", 0);
            }

            if(!$couponId){
                throw new Exception("优惠券不存在", 0);
            }

            $couponInfo = UserCoupon::where('user_id', $user_id)->where('coupon_id', $couponId)->whereNull('used_at')->orderBy('received_at','desc')->limit(1)->delete();
            UsersService::clearUserCouponCache($user_id);
            Help::Log('删除优惠券信息:',[$couponInfo,$request->all()],'im');
            if(!$couponInfo) {
                throw new Exception("删除失败", 0);
            }

            #回撤 prom 库存
            $incr_info = UsersService::restoreCouponQty($couponId);
            Help::Log('删除优惠券 回退库存信息:',[$couponInfo,$incr_info],'im');

            return response()->ajax('success');
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }


	public function importCoupon(Request $request){
        $data = $request->data;
        $data = json_decode($data,true);
        Help::Log('导入券信息:',$data);

        if(!$data) return $this->error("参数缺失");

        $num = 0;
        foreach($data as $fields){
            if(count($fields) != 2) continue;
            $fields = array_map('trim',$fields);
            UsersService::clearUserCouponCache($fields[0]);
            $ret = UserCoupon::insertGetId(
            [
                'user_id' => $fields[0],
                'coupon_id' => $fields[1],
                'received_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
            );
            if($ret) $num++;
        }
        Help::Log('导入券数量:',$num);

        if($num) return $this->success('成功',['num'=>$num]);
        return $this->error("处理失败");
    }



}
