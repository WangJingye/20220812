<?php

namespace App\Http\Controllers\Api;

use App\Model\Address;
use App\Service\CrmUsersService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\CrmCustomers;
use App\Model\WechatCoupons;
use App\Model\CrmAuthToken;
use Exception;
use Overtrue\Socialite\SocialiteManager;
use App\Model\Users;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    const CAN_MERGE_FIELDS = [
        'sex' => '性别',
        'respect_type' => '尊称',
        'birth' => '生日',
        'phone' => '手机',
        'name' => '姓名',
        'last_name' => '名',
        'first_name' => '姓',
        'email' => '邮箱',
        'outer_id' => 'CRM会员ID',
        'session_id' => 'SESSION ID',
        'address' => '地址簿',
    ];

    // 会员列表- 分页
    public function pageList(Request $request)
    {
        $params = $request->all();
        $limit = array_get($params, 'limit');
        $user = new Users();

        if ($request->has('phone') && $request->input('phone')) {
            $phone = $request->input('phone');
            $user = $user->where('phone', 'like', '%' . $phone . '%');

        }
        if ($request->has('name') && $request->input('name')) {
            $name = $request->input('name');
            $user = $user->where('name', 'like', '%' . $name . '%');

        }
        if ($request->input('source_type')) {
            $source_type = $request->input('source_type');
            $user = $user->where('source_type', $source_type);
        }
        if ($request->has('email') && $request->input('email')) {
            $email = $request->input('email');
            $user = $user->where('email', $email);
        }
        if ($request->has('pos_id') && $request->input('pos_id')) {
            $pos_id = $request->input('pos_id');
            $tag = array(" ", '"', "　", "\t", "\n", "\r");
            $pos_id = str_replace($tag, '', $pos_id);
            $user = $user->where('pos_id', $pos_id);
        }

        if ($request->has('from_entrance') && $request->input('from_entrance')) {
            $from_entrance = $request->input('from_entrance');
            $user = $user->where('from_entrance', 'like', '%' . $from_entrance . '%');

        }
        if ($request->has('from_activity') && $request->input('from_activity')) {
            $from_activity = $request->input('from_activity');
            $user = $user->where('from_activity', 'like', '%' . $from_activity . '%');
        }
        if ($request->has('channel') && $request->input('channel')) {
            $fromChannel = $request->input('channel');
            $user = $user->where('channel', $fromChannel);
        }

        if ($request->has('level') && $request->input('level')) {
            $level = $request->input('level');
            $user = $user->where('level', $level);
        }

        if ($request->has('start_time') && $request->input('start_time')) {
            $startTime = array_get($params, 'start_time');
            $user = $user->where('created_at', '>=', $startTime);
        }
        if ($request->has('end_time') && $request->input('end_time')) {
            $endTime = array_get($params, 'end_time');
            $user = $user->where('created_at', '<', $endTime);
        }
        $data = $user->orderBy('id', 'desc')->paginate($limit);


//        if($where) {
//            $users->whereHas('hasManySocialUsers', function($query) use ($where){
//                $query->where($where);
//            });
//        }


        return response()->api($data);
    }

    // 所有列表- 不分页
    public function export(Request $request)
    {
        try {
            $params = $request->all();
            $user = new Users();
            if ($request->has('name') && $request->input('name')) {
                $name = $request->input('name');
                $user = $user->where('name', 'like', '%' . $name . '%');

            }
            if ($request->has('phone') && $request->input('phone')) {
                $phone = $request->input('phone');
                $user = $user->where('phone', 'like', '%' . $phone . '%');

            }
            if ($request->input('source_type')) {
                $source_type = $request->input('source_type');
                $user = $user->where('source_type', $source_type);
            }
            if ($request->has('email') && $request->input('email')) {
                $email = $request->input('email');
                $user = $user->where('email', $email);
            }
            if ($request->has('pos_id') && $request->input('pos_id')) {
                $pos_id = $request->input('pos_id');
                $tag = array(" ", '"', "　", "\t", "\n", "\r");
                $pos_id = str_replace($tag, '', $pos_id);
                $user = $user->where('pos_id', $pos_id);
            }

            if ($request->has('from_entrance') && $request->input('from_entrance')) {
                $from_entrance = $request->input('from_entrance');
                $user = $user->where('from_entrance', 'like', '%' . $from_entrance . '%');

            }
            if ($request->has('from_activity') && $request->input('from_activity')) {
                $from_activity = $request->input('from_activity');
                $user = $user->where('from_activity', 'like', '%' . $from_activity . '%');
            }
            if ($request->has('channel') && $request->input('channel')) {
                $fromChannel = $request->input('channel');
                $user = $user->where('channel', $fromChannel);
            }

            if ($request->has('level') && $request->input('level')) {
                $level = $request->input('level');
                $user = $user->where('level', $level);
            }

            if ($request->has('start_time') && $request->input('start_time')) {
                $startTime = array_get($params, 'start_time');
                $user = $user->where('created_at', '>=', $startTime);
            }
            if ($request->has('end_time') && $request->input('end_time')) {
                $endTime = array_get($params, 'end_time');
                $user = $user->where('created_at', '<=', $endTime);
            }

            $data = [];
            $user->orderBy('id', 'desc')->chunkById(500, function ($user) use(&$data){
                foreach ($user as $v) {
                    $data[] = $v;
                }
            });
            return response()->api($data);
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

    // 所有列表- 不分页
    public function export_bak(Request $request)
    {
        try {
            $params = $request->all();
            $where = [];
            $wechatWhere = [];
            $visitors = 0;
            if ($request->has('phone') && $request->input('phone')) {
                $phone = array_get($params, 'phone');
                $where[] = ['mobile_number', $phone];
            }
            if ($request->has('email') && $request->input('email')) {
                $email = array_get($params, 'email');
                $where[] = ['email', $email];
            }
            if ($request->has('member_class') && $request->input('member_class')) {
                $memberClass = array_get($params, 'member_class');
                $where[] = ['member_class', $memberClass];
            }

            if ($request->has('from_channel') && $request->input('from_channel')) {
                $fromChannel = array_get($params, 'from_channel');
                if ($fromChannel == 4 && $where) {
                    return response()->api(['current_page' => 1, 'total' => 0, 'data' => []]);

                } elseif ($fromChannel == 4 && !$where) {
                    $visitors = 1;
                } else {
                    $where[] = ['fromchannel', $fromChannel];

                }
            }

            if ($request->has('start_time') && $request->input('start_time')) {
                $startTime = array_get($params, 'start_time');
                $wechatWhere[] = ['created_at', '>=', $startTime];
            }
            if ($request->has('end_time') && $request->input('end_time')) {
                $endTime = array_get($params, 'end_time');
                $wechatWhere[] = ['created_at', '<=', $endTime];
            }

            $data = [];
            $wechatUsers = WechatUsers::with('customers');
            if ($where) {
                $wechatUsers->whereHas('customers', function ($query) use ($where) {
                    $query->where($where);
                });
            }

            if ($visitors) {
                $wechatUsers->doesntHave('customers');
            }
            // 获取周友idList
            $customerIdList = CrmCustomers::where($where)->pluck('customer_id')->toArray();

            // 获取指定周友订单数据（下单数量、下单金额）
            $url = env('ORDER_DOMAIN') . '/order/orderInfo';
            $headers = ['Content-Type: application/json'];

            $response = http_request($url, ['custId' => $customerIdList], $headers, 'POST', '获取周友订单数据：');
            $customersOrderInfo = [];

            if ($response['httpCode'] != 200) {
                // Error API写入日志
                logger('获取周友订单数据异常', []);

            } else if ($response['httpCode'] == 200) {
                $result = json_decode($response['data'], true);
                $customersOrderInfo = $result['data'];
            }

            $wechatUsers->select('id', 'unionid', 'openid', 'created_at')->where($wechatWhere)->get()->each(function ($item) use (&$data, $customersOrderInfo) {
                $itemData = [];
                $itemData['customer_id'] = '/';
                $itemData['openid'] = $item->openid;
                $itemData['unionid'] = $item->unionid;
                $itemData['fromchannel'] = '/';
                $itemData['orderCount'] = '/';
                $itemData['orderMoney'] = '/';
                $itemData['mobileNumber'] = '/';
                $itemData['familyName'] = '/';
                $itemData['firstName'] = '/';
                $itemData['gender'] = '/';
                $itemData['dateOfBirth'] = '/';
                $itemData['residenceCountry'] = '/';
                $itemData['email'] = '/';
                $itemData['available'] = '/';

                if (!empty($item['customers'])) {
                    $itemData['customer_id'] = $item['customers']['customer_id'];
                    $itemData['fromchannel'] = $item['customers']['fromchannel'];
                    if ($customersOrderInfo) {
                        $itemData['orderCount'] = array_key_exists($item['customers']['customer_id'], $customersOrderInfo) ? $customersOrderInfo[$item['customers']['customer_id']]['count'] : 0;
                        $itemData['orderMoney'] = array_key_exists($item['customers']['customer_id'], $customersOrderInfo) ? $customersOrderInfo[$item['customers']['customer_id']]['money'] : 0;
                    }

                    $itemData['mobileNumber'] = '+' . $item['customers']['mobile_country_code'] . ' ' . $item['customers']['mobile_number'];
                    $itemData['familyName'] = $item['customers']['family_name'];
                    $itemData['firstName'] = $item['customers']['first_name'];
                    $itemData['gender'] = $item['customers']['gender'];
                    $itemData['dateOfBirth'] = $item['customers']['date_of_birth'] ?? '/';
                    $itemData['residenceCountry'] = $item['customers']['residence_country'];
                    $itemData['email'] = $item['customers']['email'];
                    $itemData['available'] = $item['customers']['available'] == 1 ? '激活' : '冻结';

                }
                unset($item['customers']);
                $data[] = $itemData;
            });

            return response()->api($data);
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

    public function detail(Request $request)
    {
        try {
            $params = $request->all();
            $id = array_get($params, 'id');

            // 获取周友idList
            $customerIdList = CrmCustomers::where('wechat_user_id', $id)->pluck('customer_id')->toArray();

            // 获取指定周友订单数据（下单数量、下单金额）
            $url = env('ORDER_DOMAIN') . '/order/orderInfo';
            $headers = ['Content-Type: application/json'];

            $response = http_request($url, ['custId' => $customerIdList], $headers, 'POST', '获取指定周友订单数据：');
            $customersOrderInfo = [];
            if ($response['httpCode'] !== 200) {
                // Error API写入日志
                logger('获取指定周友订单数据异常', []);
            } else if ($response['httpCode'] == 200) {
                $result = json_decode($response['data'], true);
                $customersOrderInfo = $result['data'];
            }
            $wechatUserInfo = WechatUsers::with('customers')->select('id', 'unionid', 'openid', 'authorize_at')->where('id', $id)->first();
            $wechatUserInfo->customer_id = $wechatUserInfo->family_name = $wechatUserInfo->first_name = $wechatUserInfo->gender = $wechatUserInfo->dateOfBirth = $wechatUserInfo->email = $wechatUserInfo->mobileNumber = $wechatUserInfo->residenceCountry = $wechatUserInfo->fromchannel = '/';
            $wechatUserInfo->orderCount = 0;
            $wechatUserInfo->orderMoney = 0;
            $wechatUserInfo->point = 0;
            $wechatUserInfo->available = '/';
            $wechatUserInfo->auth = $wechatUserInfo->authorize_at ? '是' : '否';

            if (!empty($wechatUserInfo->customers)) {
                $crmId = $wechatUserInfo->customers->customer_id;
                $authToken = new CrmAuthToken;
                // 获取悦享钱余额
                $balance_url = $authToken->domain . 'customers/' . $crmId . '/stardollar-balances';
                $balance_response = http_request($balance_url, [], $authToken->aHeader, 'GET', '获取周友悦享钱余额：');
                if ($balance_response['httpCode'] != 200) {
                    $wechatUserInfo->point = 0;

                } else {
                    $balance_result = json_decode($balance_response['data'], true);
                    $wechatUserInfo->point = $balance_result['usableStarDollar'];
                }
                $wechatUserInfo->customer_id = $crmId;
                $wechatUserInfo->family_name = $wechatUserInfo->customers->family_name;
                $wechatUserInfo->first_name = $wechatUserInfo->customers->first_name;
                $wechatUserInfo->gender = $wechatUserInfo->customers->gender;
                $wechatUserInfo->dateOfBirth = !empty($wechatUserInfo->customers->date_of_birth) ? $wechatUserInfo->customers->date_of_birth : '-';

                $wechatUserInfo->email = $wechatUserInfo->customers->email;
                $wechatUserInfo->mobileNumber = '+' . $wechatUserInfo->customers->mobile_country_code . ' ' . $wechatUserInfo->customers->mobile_number;
                $wechatUserInfo->residenceCountry = $wechatUserInfo->customers->residence_country;
                $wechatUserInfo->available = $wechatUserInfo->customers->available == 1 ? '激活' : '冻结';
                $wechatUserInfo->fromchannel = $wechatUserInfo->customers->fromchannel;

                if ($customersOrderInfo) {
                    $wechatUserInfo->orderCount = array_key_exists($wechatUserInfo->customers->customer_id, $customersOrderInfo) ? $customersOrderInfo[$wechatUserInfo->customers->customer_id]['count'] : 0;
                    $wechatUserInfo->orderMoney = array_key_exists($wechatUserInfo->customers->customer_id, $customersOrderInfo) ? $customersOrderInfo[$wechatUserInfo->customers->customer_id]['money'] : 0;
                }

            }
            unset($wechatUserInfo->customers);
            return response()->api($wechatUserInfo);
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }

    public function destroy(Request $request)
    {
        $param = $request->all();
        $id = array_get($param, 'id');
        $crmCustomers = CrmCustomers::where('wechat_user_id', $id)->first();
        if (!$crmCustomers) {
            $message = '不得对游客身份进行操作';
            return response()->errorApi($message);

        } else {
            if ($crmCustomers->available == 1) {
                $crmCustomers->available = 2;
            } else {
                $crmCustomers->available = 1;
            }
            $crmCustomers->save();
            $message = '会员状态更新成功';
        }

        $data = [];
        $data['message'] = $message;

        return response()->api($data);
    }

    // 使用优惠券
    public function useConpon(Request $request)
    {
        try {
            $wechatUserId = $request->input('wechat_user_id');
            $couponId = $request->input('coupon_id');

            if (!$couponId) {
                throw new Exception("优惠券ID不得为空！", 0);
            }
            $couponInfo = WechatCoupons::where('wechat_user_id', $wechatUserId)->where('coupon_id', $couponId)->first();

            if (!$couponInfo) {

                throw new Exception("优惠券不存在", 0);
            } else if ($couponInfo && $couponInfo->used_at) {

                throw new Exception("优惠券已使用", 0);
            } else if ($couponInfo && !$couponInfo->used_at) {

                $couponInfo->used_at = date('Y-m-d H:i:s');
                $couponInfo->save();
            }

            return response()->ajax('使用优惠券成功');
        } catch (Exception $e) {
            return response()->errorAjax($e);
        }
    }


    public function getSlaveAndMasterMember(Request $request)
    {
        $master_id = $request->master_id;
        $slave_id = $request->slave_id;
        if (!$slave_id || !$master_id) return $this->error("参数缺失");
        $users = Users::whereIn('id', [$master_id, $slave_id])->get()->toArray();
        if (!$users || (count($users) != 2)) return $this->error("用户ID错误");
        $data = [];
        $fields = array_keys(self::CAN_MERGE_FIELDS);
        foreach ($users as $user) {
            foreach ($user as $k => $v) {
                if (in_array($k, $fields)) {
                    $data[$k][] = $v;
                    $data[$k] = array_filter(array_unique($data[$k]));
                }
            }
        }
        if ($data) $data['address'] = ["{$master_id}的地址", "{$slave_id}的地址"];

        return $this->success('获取成功', ['fields' => self::CAN_MERGE_FIELDS, 'infos' => $data]);
    }

    public function mergeSlaveMemberIntoMasterMember(Request $request)
    {
        $master_id = $request->master_id;
        $slave_id = $request->slave_id;
        $all = $request->all();
        if (!$slave_id || !$master_id) return $this->error("参数缺失");
        $users = Users::whereIn('id', [$master_id, $slave_id])->get()->toArray();
        if (!$users || (count($users) != 2)) return $this->error("用户ID错误");
        $users = array_combine(array_column($users, 'id'), $users);
        $master = $users[$master_id];
        $slave = $users[$slave_id];
        $upData = [];
        $fields = array_keys(self::CAN_MERGE_FIELDS);
        foreach ($fields as $field) {
            if (empty($all[$field])) continue;
            if ($field == 'address') continue;   //地址 单独处理
            if ($all[$field] == 'm') $upData[$field] = $master[$field];
            if ($all[$field] == 's') $upData[$field] = $slave[$field];
        }
        $exception = DB::transaction(function () use ($upData, $master_id, $slave_id, $all) {
            if (!empty($all['address'])) {   //地址转移
                Address::delUserAddress(($all['address'] == 'm') ? 's' : 'm');
                if ($all['address'] == 's')
                    Address::transferUserAddress($slave_id, $master_id);
            }
            DB::insert("insert into tb_users_deleted select * from tb_users where id = {$slave_id}");
            Users::where('id', $slave_id)->delete();
            Users::where('id', $master_id)->update($upData);
        });
        if (is_null($exception)) return $this->success('合并成功');
        return $this->error('合并失败');

    }


    public function getUserInfo(Request $request)
    {
        $crmService = new CrmUsersService();
        $crm_user_info = $crmService->userInfo(['pos_id' => $request->pos_id]);
        if ($crm_user_info) {
            return $this->success('success', $crm_user_info);
        }
        return $this->error('fail');
    }

    public function getUserInfoByOpenid(Request $request)
    {
        $userInfo = Users::query()->where('open_id', $request->get('openid'))->first();
        return $this->success('success', $userInfo);
    }
    public function getUserInfoByUserId(Request $request)
    {
        $userInfo = Users::query()->where('id', $request->get('id'))->first();
        return $this->success('success', $userInfo);
    }
}
