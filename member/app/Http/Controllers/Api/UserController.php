<?php

namespace App\Http\Controllers\Api;

use App\Model\Address;
use App\Model\Help;
use App\Service\CrmUsersService;
use App\Service\UsersService;
use App\Services\Api\UserServices;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Exception;
use App\Model\Users;
use Illuminate\Support\Facades\Log;
use App\Model\Activity;
use App\Support\Token;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    protected $wechatUserId;

    public function __construct(Request $request)
    {
        parent::__construct();
        if (!$this->user_id) {
            return $this->error("未登陆");
        }
    }


    public function addAddress(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'mobile' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'province' => 'required',
            'city' => 'required',
            'address' => [
                'required',
                'string',
                'max:100',
                'regex:/^([a-zA-Z]|[0-9]|[\x{4e00}-\x{9fa5}]|[\s\-\_]|[,，\(\)\（\）])*$/u'
            ],
            'name' => [
                'required',
                'string',
                'max:20',
                'regex:/^([a-zA-Z]|[0-9]|[\x{4e00}-\x{9fa5}]|[\s\-\_]|[,，\(\)])*$/u'
            ],
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex' => '手机号格式不正确',
            'name.required' => '姓名不能为空',
            'name.max' => '姓名长度不能超过20位字符',
            'name.regex' => '姓名不支持特殊字符',
            'address.required' => '地址不能为空',
            'address.max' => '地址长度不能超过50位字符',
            'address.regex' => '地址不支持特殊字符',
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }

        $count = Address::where('user_id', $this->user_id)->count();
        if ($count >= Address::MAX_ADDRESS_NUM) return $this->error("已超最大地址数");

        $data = [
            'user_id' => $this->user_id,
            'zip_code' => !empty($param['zip_code']) ? $param['zip_code'] : '',
            'sex' => $param['sex'] ?? 0,
            'name' => $param['name'],
            'mobile' => $param['mobile'],
            'city' => $param['city'],
            'province' => $param['province'],
            'area' => $param['area'] ?? '',
            'address' => $param['address'],
            'is_default' => $param['is_default'] ?? 0,
        ];

        try {
            $c_data = $data;
            unset($c_data['is_default']);
            $rec = Address::where($c_data)->first();
            if ($rec) return $this->error("地址已存在", $rec);
//            $id = Address::firstOrCreate($c_data,$data);
            $id = Address::insertGetId($data);
            if ($id) {
                if (!empty($param['is_default'])) Address::where('user_id', $this->user_id)->where('id', '!=', $id)->update(['is_default' => 0]);
                UsersService::cacheUserAddress($this->user_id);
                return $this->success("创建地址成功", array_merge($data, ['id' => $id]));
            }
            return $this->error("新增失败");
        } catch (\Exception $e) {
            return $this->error("新增失败了");
        }

    }

    public function updateAddress(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'mobile' => ['required', 'regex:/^1(3|4|5|6|7|8|9)\d{9}$/'],
            'id' => 'required',
            'province' => 'required',
            'city' => 'required',
            'address' => [
                'required',
                'string',
                'max:100',
                'regex:/^([a-zA-Z]|[0-9]|[\x{4e00}-\x{9fa5}]|[\s\-\_]|[,，\(\)\（\）])*$/u'
            ],
            'name' => [
                'required',
                'string',
                'max:20',
                'regex:/^([a-zA-Z]|[0-9]|[\x{4e00}-\x{9fa5}]|[\s\-\_]|[,，\(\)])*$/u'
            ],
        ], [
            'mobile.required' => '手机号不能为空',
            'mobile.regex' => '手机号格式不正确',
            'name.required' => '姓名不能为空',
            'name.max' => '姓名长度不能超过20位字符',
            'name.regex' => '姓名不支持特殊字符',
            'address.required' => '地址不能为空',
            'address.max' => '地址长度不能超过50位字符',
            'address.regex' => '地址不支持特殊字符',
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }

//        $count = Address::where('user_id',$this->user_id)->count();
//        if($count >= Address::MAX_ADDRESS_NUM) return $this->error("已超最大地址数");

        $data = [
            'user_id' => $this->user_id,
            'zip_code' => !empty($param['zip_code']) ? $param['zip_code'] : '',
            'sex' => $param['sex'] ?? 0,
            'name' => $param['name'],
            'mobile' => $param['mobile'],
            'city' => $param['city'],
            'province' => $param['province'],
            'area' => $param['area'] ?? '',
            'address' => $param['address'],
            'is_default' => $param['is_default'] ?? 0,
        ];

        try {
//            $c_data = $data;
//            unset($c_data['is_default']);
//            $rec = Address::where($c_data)->first();
//            if ($rec) return $this->error("地址重复");
//            $id = Address::firstOrCreate($c_data,$data);
            $id = Address::where('id', $param['id'])->update($data);
            if ($id) {
                if (!empty($param['is_default'])) {
                    Address::where('user_id', $this->user_id)->where('id', '!=', $param['id'])->update(['is_default' => 0]);
                }
                UsersService::cacheUserAddress($this->user_id);
                return $this->success("更新地址成功");
            }
            return $this->error("更新失败");
        } catch (\Exception $e) {
            return $this->error("更新失败了");
        }
    }

    //设置默认地址
    public function setDefaultAddress(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'id' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }

        try {
            Address::where('user_id', $this->user_id)->where('id', '!=', $param['id'])->update(['is_default' => 0]);
            Address::where('user_id', $this->user_id)->where('id', $param['id'])->update(['is_default' => 1]);
            UsersService::cacheUserAddress($this->user_id);
            return $this->success("设置成功");
        } catch (\Exception $e) {
            return $this->error("设置失败");
        }
    }

    public function delAddress(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'id' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }

        try {
            $id = Address::where('id', $param['id'])->where('user_id', $this->user_id)->delete();
            UsersService::cacheUserAddress($this->user_id);
            if ($id) return $this->success("删除地址成功");
            return $this->error("删除失败");
        } catch (\Exception $e) {
            return $this->error("删除失败了");
        }
    }

    public function showAddress(Request $request)
    {
        try {
            $adrs = UsersService::getAddressFromCache($this->user_id);
            return $this->success("获取地址成功", $adrs);
        } catch (\Exception $e) {
            return $this->error("获取失败了");
        }

    }

    public function innerGetUserAddress(Request $request)
    {

        $user_id = $request->user_id;
        $sn = $request->sn;
        if($sn != env('APP_KEY')) return $this->error("签名错误");
        if(empty($user_id)){
            return $this->error("用户ID获取失败");
        }
        try {
            $adrs = UsersService::getAddressFromCache($user_id);
            return $this->success("获取地址成功", $adrs);
        } catch (\Exception $e) {
            return $this->error("获取失败了");
        }

    }


    public function userCenter(Request $request)
    {
        try {
            if ($request->header('token')) {
                $request->merge(['token' => $request->header('token'), 'refresh-token' => $request->header('refresh-token')]);
            }
            $token = $request->input('token', '');
            $codeStatus = Token::checkToken($token);
            $from = $request->header('from');
            if ($from > 1 && $codeStatus === false) {
                $return = ['code' => 2, 'message' => "身份验证失败，请重新登录！", 'data' => []];
                return json_encode($return);
            }
            if ($codeStatus === false) {
                $ret['benefit_info'] = [
                    [
                        'icon' => 'https://static.connext.net.cn/static/dlc-web-200506/imgs/gift.svg',
                        'title' => '新客专享，首单有礼',
                        'txt' => [
                            "在Sisley法国希思黎中国官网注册会员，即享新客呵宠礼", "礼遇已自动放至您的账户中，首次购物即可带出",
                        ]
                    ],


                    [
                        'icon' => 'https://static.connext.net.cn/static/dlc-web-200506/imgs/delivery.svg',
                        'title' => '精美包装，奢宠礼遇',
                        'txt' => [
                            "您的每一个订单都将以精美的礼盒包装快递给您", "尊享官网独家购物体验，是您礼献挚爱的倾情之选"
                        ]
                    ],
                ];

                $ret['active_info'] = [
//                    [
//                        'pc_url' => '',
//                        'wx_url' => '',
//                        'big_image' => 'https://eesfe.oss-cn-shanghai.aliyuncs.com/static/dlc-web-200506/imgs/events/event_1-big.png',
//                        'small_image' => 'https://assetsuat.nightcherry.com/mp/beautiful/event_1.png'
//                    ],
//                    [
//                        'pc_url' => '',
//                        'wx_url' => '',
//                        'big_image' => 'https://eesfe.oss-cn-shanghai.aliyuncs.com/static/dlc-web-200506/imgs/events/event_2-big.png',
//                        'small_image' => 'https://assetsuat.nightcherry.com/mp/beautiful/event_2.png',
//
//
//                    ],
                ];
                $ret['coupon_list'] = [];
                $ret['shipping_address'] = [];
                $ret['order_list'] = [];
                $ret['total_points'] = 0;    //积分信息
                $ret['pos_id'] = 0;    //POS ID
                $ret['base_info'] = (object)[];
                $ret['shipping_address'] = [];
                return $this->success('成功', $ret);


            }
            $user_id = $codeStatus;
            $ret['benefit_info'] = [
                [
                    'icon' => 'https://static.connext.net.cn/static/dlc-web-200506/imgs/gift.svg',
                    'title' => '新客专享，首单有礼',
                    'txt' => [
                        "在Sisley法国希思黎中国官网注册会员，即享新客呵宠礼，", "礼遇已自动放至您的账户中，首次购物即可带出",
                    ]
                ],


                [
                    'icon' => 'https://static.connext.net.cn/static/dlc-web-200506/imgs/delivery.svg',
                    'title' => '精美包装，奢宠礼遇',
                    'txt' => [
                        "您的每一个订单都将以精美的礼盒包装快递给您", "尊享官网独家购物体验，是您礼献挚爱的倾情之选"
                    ]
                ],
            ];
            $ret['active_info'] = [
//                [
//                    'pc_url' => '',
//                    'wx_url' => '/pages/share/share',
//                    'big_image' => 'https://eesfe.oss-cn-shanghai.aliyuncs.com/static/dlc-web-200506/imgs/events/event_1-big.png',
//                    'small_image' => 'https://assetsuat.nightcherry.com/mp/beautiful/event_1.png'
//                ],
//                [
//                    'pc_url' => '',
//                    'wx_url' => '/pages/share/share',
//                    'big_image' => 'https://eesfe.oss-cn-shanghai.aliyuncs.com/static/dlc-web-200506/imgs/events/event_2-big.png',
//                    'small_image' => 'https://assetsuat.nightcherry.com/mp/beautiful/event_2.png',
//
//
//                ],
            ];
            $user_info = UsersService::getUserInfoFromCache($user_id);
            if (!$user_info) throw new Exception("用户信息获取失败", 0);
            unset($user_info['password']);
            $coupons = UsersService::getCouponsFromCache($user_id);
            Help::Log('用户' . $user_id, [$coupons, $user_info], 'im');

            if ($coupons) $coupon_ids = array_column($coupons, 'coupon_id');

            if (!empty($coupon_ids)) {
                $group_coupons = UsersService::groupUserCoupons($coupons);
                Help::Log('用户归类后券' . $user_id, $group_coupons, 'im');
                $ret['coupon_list'] = $group_coupons['vaild_coupons'] ?? [];  //有效的券
            }

            $ret['coupon_list'] = !empty($ret['coupon_list']) ? $ret['coupon_list'] : []; //券信息


            $crmService = new CrmUsersService();
            $crm_user_info = $crmService->userInfo(['pos_id' => $user_info['pos_id'], 'phone' => $user_info['phone']]);
            $customer_type = ['新客', '银卡', '金卡', '白金卡', '老客', '贵宾'];
            if ($crm_user_info) {
                $ret['total_points'] = $crm_user_info['AvailablePoints'] ?? 0;
                $ret['customer_type'] = $crm_user_info['CustomerType'] ?? 0;
                $ret['customer_level'] = $customer_type[$ret['customer_type']];
                UsersService::loadUser($user_info['pos_id'], $user_info['phone'],$crm_user_info);

            } else {
                $ret['customer_type'] = $user_info['level'] ?? 0;
                $ret['customer_level'] = $customer_type[$user_info['level']];
                $ret['total_points'] = $user_info['points'] ?? 0;
            }


            //积分信息
//            $ret['customer_type'] = $crm_user_info['CustomerType'] ?? 0;
//            Users::where('id' ,$user_id)->update([
//                'points'=>$ret['total_points'],
//                'level'=>$ret['customer_type']
//            ]);


            $ret['pos_id'] = $user_info['pos_id'] ?? 0;    //POS ID
            $ret['base_info'] = $user_info;
            $ret['shipping_address'] = [];
            $address = UsersService::getAddressFromCache($user_id);
            $ret['shipping_address'] = [];
            foreach ($address as $one) {
                if ($one['is_default'] == 1) {
                    $ret['shipping_address'] = [
                        'shipping_address_id' => $one['id'],
                        'name' => $one['name'],
                        'sex' => $one['sex'],
                        'mobile' => $one['mobile'],
                        'province' => $one['province'],
                        'city' => $one['city'],
                        'district' => $one['area'],
                        'address_detail' => $one['address'],
                        'post_code' => $one['zip_code'],
                    ];
                    break;
                }
                //地址信息

            }
            if (empty($ret['shipping_address']) && $address) {
                $ret['shipping_address'] = [
                    'shipping_address_id' => $address[0]['id'],
                    'name' => $address[0]['name'],
                    'sex' => $address[0]['sex'],
                    'mobile' => $address[0]['mobile'],
                    'province' => $address[0]['province'],
                    'city' => $address[0]['city'],
                    'district' => $address[0]['area'],
                    'address_detail' => $address[0]['address'],
                    'post_code' => $address[0]['zip_code'],
                ];
            }
            $url = env('OMS_DOMAIN') . '/oms/order/listLimit';
            $headers = ['Content-Type: application/json'];
            $response = http_request($url . '?user_id=' . $user_id . '&pos_id=' . $user_info['pos_id'], [], $headers, 'GET', '获取订单列表信息curl：');

            $ret['order_list'] = [];
            if ($response['httpCode'] == 200) {
                $result = json_decode($response['data'], true);
                if ($result['code'] == 1) {
                    $ret['order_list'] = $result['data'];
                }
            }


            return $this->success('成功', $ret);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }


    /**完善用户信息
     * @param Request $request
     */
    public function approachInfo(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'pos_id' => 'required',
            'sign' => 'required',
        ]);

        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        $open_id = '';
        $param['sso_code'] = $request->get('sso_code', '');
        $redis = Redis::connection('default');
        if ($redis->exists($param['sso_code']) && $param['sso_code']) {
            $open_id = $redis->get($param['sso_code']);
        }

        $check = UsersService::checkCompleteSign($param['pos_id'], $param['sign']);
        if ($check == 0) {
            return $this->error('完善信息非法');
        }
        if ($check == 2) {
            return $this->error('完善信息页面停留过长，请重新登录后授权');
        }
        try {

            $has_phone = $request->get('has_phone', true);
            if (!$has_phone) {

                $msg_code = $request->get('msg_code');
                if (empty($request->get('msg_code'))) {
                    return $this->error("短信验证码必填", [
                        'field' => 'msg_code',
                    ]);
                }

                //验证手机短信
                list($status, $message, $data) = UsersService::checkMsgCode($request->get('phone', ''), $msg_code, 5);
                if (!$status) {
                    return $this->error($message, $data);
                }

            }
            list($success, $message, $data) = UsersService::updateInfo($param['pos_id'], $param, $open_id);
            if ($success) {
                $data = UsersService::getUserInfo($data['uid']);
                return $this->success('成功', $data, $data['user_id']);
            }

            return $this->error($message, $data);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function saveActivity(Request $request)
    {
        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'activityChannel' => 'required',
                'entrance' => 'required',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $data['user_id'] = $this->user_id;
            $data['channel'] = $request->get('activityChannel');
            $data['active'] = $request->get('entrance');

            $data = Activity::firstOrCreate($data);
            if ($data) {
                return $this->success('成功', []);
            }
            return $this->error('操作失败', []);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());
        }
    }

    public function userExist(Request $request)
    {


        try {
            $param = $request->all();
            $v = Validator::make($param, [
                'uid' => 'required|numeric',
            ]);

            if ($v->fails()) {
                return $this->error($v->errors()->first());
            }
            $user = Users::where('id', $param['uid'])->exists();
            if ($user) {
                return $this->success('成功', []);
            }
            return $this->error('操作失败', []);
        } catch (\Exception $e) {
            Log::error($e);
            return $this->error($e->getMessage());

        }


    }

    /**
     * 导入user（有数）历史数据
     */
    public function exportUserHistory(Request $request)
    {
        $data = Users::exportUserHistory();
        return $this->success($data, 'success');
    }
}