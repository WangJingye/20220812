<?php


namespace App\Service;

use App\Lib\Http;
use App\Model\Address;
use App\Model\Favorite;
use App\Model\Footprint;
use App\Model\Help;
use App\Model\SocialUsers;
use App\Model\UserCoupon;
use App\Model\Users;
use App\Service\FissionService;
use App\Support\Sms;
use Illuminate\Support\Facades\Log;
use Overtrue\Socialite\SocialiteManager;
use Illuminate\Support\Facades\Redis;
use App\Model\Redis as RedisM;
use Illuminate\Support\Facades\DB;

class UsersService
{

    static $login_fail = 1; //允许失败次数上限
    //社交登录 微博 支付宝 微信
    //测试服务器  用于设置社交账户 回调地址
    //密码验证规则
    //token  分发规则
    //短信发送 次数限制
    //个社交平台 'client_id'   'client_secret' => 'your-app-secret', 'redirect'
    //crm 注册及登录接口调用规则
    //登录同步
    public static function getWebLogin()
    {

    }

    /**
     * 用户注册
     * @param $phone
     * @param $msg_code
     * @param $valid_id
     * @param $valid_code
     * @param $password
     * @param $birthday
     * @param $email
     */
    public static function register($phone, $msg_code, $password, $birthday, $sex, $name, $email, $channel, $open_id = '', $parent_id = '', $param = [])
    {

        //验证手机短信
        list($status, $message, $data) = self::checkMsgCode($phone, $msg_code, 2);
        if (!$status) {
            return [false, $message, $data];
        }


        $exists = self::checkUserExist($phone);
        if ($exists) {
            return [false, '此号码已注册过Sisley会员，请登录', [
                'field' => 'phone',
            ]];
        }

        $crm = new CrmUsersService();

        $crm_uid = self::getUid();

        $sex = $sex == 1 ? 'M' : 'F';
        //todo 异步注册crm用户
        $userinfo = [
            'phone' => $phone,
            'sex' => $sex,
            'name' => $name,
            'pos_id' => $crm_uid,
            'email' => $email,
            'birth' => $birthday,

        ];
        $success = $crm->createUSer($userinfo);
        if (!$success) {
            return [false, '注册失败，请重试', ['field' => 'phone']];
        }
        $date = date('Y-m-d H:i:s');


        $user = [
            'phone' => $phone,
            'password' => self::encrypt_password($password),
            'sex' => $sex,
            'source_type' => 1,
            'channel' => $channel,
            'name' => $name,
            'birth' => $birthday,
            'pos_id' => $crm_uid,
            'parent_id' => $parent_id,
            'from_activity' => $param['from_activity'],
            'from_entrance' => $param['from_entrance'],
            'email' => $email,
            'created_at' => $date,
            'updated_at' => $date
        ];

        $user_id = Users::insertGetId($user);

        if ($user_id) {
            $redis = RedisM::getRedis();
            $key = RedisM::getKey('rk.user.account', ['account' => $email]);
            $key1 = RedisM::getKey('rk.user.phone', ['phone' => $phone]);
            $user['user_id'] = $user_id;
            $redis->setex($key, 1800, json_encode($user));
            $redis->setex($key1, 1800, json_encode($user));
            $data = [
                'user_id' => $user_id,
                'phone' => $phone,
                'sex' => $sex,
                'name' => $name,
            ];
            if ($open_id) {
                socialUsers::where('open_id', $open_id)->update([
                    'user_id' => $user_id,
                ]);
            }
            if ($parent_id > 0) {

                $fission = new FissionService();
                $fission->buildRelation([
                    'fission_id' => $parent_id,
                    'member_id' => $user_id,
                    'member_name' => $name,
                ]);
            }

            $redis = RedisM::getRedis();
            $key = RedisM::getKey('rk.user.account', ['account' => $phone]);
            $redis->del($key);
            $sms = new Sms();
            $sms->send($phone, 4, '');
            $key = 'member_' . date('Ymd');
            $redis->sadd($key, $data['user_id']);
            $client = new Http();
            $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $data['user_id']]);
            return [true, '注册成功', $data];

        } else {
            return [false, '注册失败，请重试', ['field' => 'phone']];
        }


    }

    /**
     * 用户注册
     * @param $phone
     * @param $msg_code
     * @param $valid_id
     * @param $valid_code
     * @param $password
     * @param $birthday
     * @param $email
     */
    public static function registerMiniApp($phone, $password, $birthday, $name, $sex, $openid, $parent_id = '', $param = [])
    {
        $exists = self::checkUserExist($phone);
        if ($exists) {
            return [false, '此号码已注册过Sisley会员，请登录', [
                'field' => 'phone',
            ]];
        }
        $socialObj = SocialUsers::where('open_id', $openid)->first();
        if (!$socialObj) {
            return [false, '授权信息不存在，请重新授权', []];
        }

        $crm = new CrmUsersService();
        $pos_id = self::getUid();


        $sex = $sex == 1 ? 'M' : 'F';
        $user = [
            'phone' => $phone,
            'password' => self::encrypt_password($password),
            'source_type' => 1,
            'name' => $name,
            'channel' => 1,
            'sex' => $sex,
            'pos_id' => $pos_id,
            'birth' => $birthday,
            'from_activity' => $param['from_activity'],
            'from_entrance' => $param['from_entrance'],
        ];

        $usersModel = new Users();
        $usersModel->phone = $phone;
        $usersModel->password = self::encrypt_password($password);
        $usersModel->source_type = 1;
        $usersModel->name = $name;
        $usersModel->channel = 1;
        $usersModel->sex = $sex;
        $usersModel->pos_id = $pos_id;
        $usersModel->birth = $birthday;
        $usersModel->from_activity = $param['from_activity'];
        $usersModel->from_entrance = $param['from_entrance'];

        $userinfo = [
            'phone' => $phone,
            'name' => $name,
            'sex' => $sex,
            'email' => '',
            'pos_id' => $pos_id,
            'birth' => $birthday,
        ];
        $true = $crm->createUSer($userinfo);
        if (!$true) {
            return [false, '注册异常，请重试', []];
        }
        $date = date('Y-m-d H:i:s');
        $arr = json_decode($socialObj['social_info']);
        if ($arr) {
            $usersModel->nickname = $arr['nickName'];
            $usersModel->pic = $arr['avatarUrl'];
            $usersModel->city = $arr['city'];
            $usersModel->country = $arr['country'];
            $usersModel->province = $arr['province'];
            $usersModel->open_id = $arr['openId'];
            $usersModel->created_at = $date;
            $usersModel->updated_at = $date;

        }

        $usersModel->open_id = $openid;
        $usersModel->save();
        $user_id = $usersModel->id;
        socialUsers::where('open_id', $openid)->update([
            'user_id' => $user_id,
        ]);

        $redis = RedisM::getRedis();
        $key1 = RedisM::getKey('rk.user.phone', ['phone' => $phone]);
        $redis->setex($key1, 1800, json_encode($user));
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.account', ['account' => $phone]);
        $redis->del($key);

        $data = self::getUserInfo('', ['phone' => $phone]);
        $sms = new Sms();
        $sms->send($phone, 4, '');
        $key = 'member_' . date('Ymd');
        $redis->sadd($key, $data['user_id']);
        if ($parent_id > 0) {
            $fission = new FissionService();
            $fission->buildRelation([
                'fission_id' => $parent_id,
                'member_id' => $data['user_id'],
                'member_name' => $name,
            ]);
        }
        $client = new Http();
        $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $data['user_id']]);
        return [true, '注册成功', $data];

    }

    public static function getTmpUserId($openid)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.tmp_uid', ['openid' => md5($openid)]);

        $uid = $redis->get($key);
        if (!$uid) {
            $uid = 0 - substr(str_replace('.', '', microtime(true)), 3);
            $redis->setex($key, config('common.a_month') * 2, $uid);
        }
        return $uid;

    }


    /**
     * 通过手机号/邮箱 密码登录
     * @param $phone
     * @param $email
     * @param $password
     * @param $login_type 1 手机号 2邮箱
     * @return array
     */
    public static function loginByPassword($account, $password, $login_type, $open_id = '', $channel)
    {
        $where = ['phone' => $account];
        if ($login_type == 2) {
            $where = ['email' => $account];
        } else {
            $exists = self::checkUserExist($account);

            if (!$exists) {
                return [false, '您不是Sisley会员，请注册', ['field' => 'account']];
            }
        }


        $userInfo = Users::select('id as user_id', 'password', 'open_id','source_type','updated_at')->where($where)->first();

        if (!$userInfo) {
            $crm = new CrmUsersService();
            $user = $crm->userExist($account);
            if ($user) {
                return [false, '您是Sisley会员，但未设置官网密码，请用手机验证码登录', ['field' => 'account']];
            }
            return [false, '您不是Sisley会员，请注册', ['field' => 'account']];
        }
        if ($userInfo && empty($userInfo['password'])) {
            return [false, '您是Sisley会员，但未设置官网密码，请用手机验证码登录', [
                'field' => 'account',
            ]];
        }

        $true = self::checkPassword($password, $userInfo['password']);
        if (!$true) {
            return [false, '账号或者密码不正确，请尝试手机验证码登录', ['field' => 'account']];
        }


        if ($open_id) {
            if ($userInfo['open_id'] && ($open_id == $userInfo['open_id'])) {
                $data = self::getUserInfo($userInfo['user_id']);
                self::updateLogin($userInfo['user_id'], $channel);
                if (empty($userInfo['updated_at']) && $userInfo['source_type'] == 0) {
                    $client = new Http();
                    $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $userInfo['user_id']]);
                }
                return [true, '登录成功', $data];

            }
            $social = socialUsers::where('open_id', $open_id)->first();
            if (!$social) {
                return [false, '授权信息不存在，请重新授权', []];
            }
            $arr = json_decode($social['social_info']);
            if ($arr) {
                $update = [
                    'nickname' => $arr['nickName'],
                    'pic' => $arr['avatarUrl'],
                    'city' => $arr['city'],
                    'sex' => $arr['gender'] == '1' ? 'M' : 'F',
                    'country' => $arr['country'],
                    'province' => $arr['province'],
                    'open_id' => $arr['openId'],

                ];
            }
            $update['open_id'] = $open_id;
            $exception = DB::transaction(function () use ($userInfo, $update, $open_id) {
                Users::where('id', $userInfo['user_id'])->update($update);
                socialUsers::where('open_id', $open_id)->update([
                    'user_id' => $userInfo['user_id'],
                ]);
            });
            if ($exception) {
                return [false, '登录异常', ['field' => 'account']];
            }

        }

        if (empty($userInfo['updated_at']) && $userInfo['source_type'] == 0) {
            $client = new Http();
            $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $userInfo['user_id']]);
        }
        self::updateLogin($userInfo['user_id'], $channel);
        Redis::del('login_fail.' . $account);
        $data = self::getUserInfo($userInfo['user_id']);
        return [true, '登录成功', $data];

    }

    /**
     * 通过手机号验证码登录或注册
     * @param $phone
     * @param $code
     */
    public static function loginByPhone($phone, $code, $channel, $open_id = '')
    {
        $exists = self::checkUserExist($phone);
        if (!$exists) {
            return [false, '您不是Sisley会员，请注册', ['field' => 'phone']];
        }

        //验证短信
        list($success, $message) = self::checkMsgCode($phone, $code, 1);
        if (!$success) {
            return [false, $message, [
                'field' => 'msg_code',
                'fail_mark' => 1,
            ]];
        }
        $is_first = 0;
        if (!Users::where('phone', $phone)->exists()) {
            $load_user = self::loadOtherUser($phone, $channel);
            if (!$load_user) {
                return [false, '登录失败，请重试或联系客服', [
                    'field' => 'phone',
                ]];
            }
            $is_first = 1;
        }
        if ($open_id) {
            $userInfo = Users::select('id as user_id', 'password', 'source_type','open_id','updated_at')->where('phone', $phone)->first();
            if ($userInfo['open_id'] && $open_id == $userInfo['open_id']) {
                $data = self::getUserInfo($userInfo['user_id']);
                self::updateLogin($userInfo['user_id'], $channel);
                if ($is_first == 1 or (empty($userInfo['updated_at']) && $userInfo['source_type'] == 0)) {
                    $client = new Http();
                    $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $userInfo['user_id']]);
                }
                return [true, '登录成功', $data];

            }
            $social = socialUsers::where('open_id', $open_id)->first();
            if (!$social) {
                return [false, '授权信息不存在，请重新授权', []];
            }
            $arr = json_decode($social['social_info']);
            if ($arr) {
                $update = [
                    'nickname' => $arr['nickName'],
                    'pic' => $arr['avatarUrl'],
                    'city' => $arr['city'],
//                    'sex' => $arr['gender'] == '1' ? 'M' : 'F',
                    'country' => $arr['country'],
                    'province' => $arr['province'],
                    'open_id' => $arr['openId'],

                ];
            }
            $update['open_id'] = $open_id;
            if(empty($userInfo['updated_at']) && $userInfo['source_type'] == 0){
                $is_first = 1;
            }
            $exception = DB::transaction(function () use ($userInfo, $update, $open_id) {
                Users::where('id', $userInfo['user_id'])->update($update);
                socialUsers::where('open_id', $open_id)->update([
                    'user_id' => $userInfo['user_id'],
                ]);
            });
            if ($exception) {
                return [false, '登录异常', ['field' => 'account']];
            }

        }
        $userInfo = Users::select('id as user_id', 'password','source_type','updated_at')->where('phone', $phone)->first();

        if (!$userInfo) {
            return [false, '您不是Sisley会员，请注册', ['field' => 'phone']];
        }
        if(empty($userInfo['updated_at']) && $userInfo['source_type'] == 0){
            $is_first = 1;
        }
        Log::info('is_first'.$is_first,$userInfo->toarray());
        $data = self::getUserInfo($userInfo['user_id']);
        Redis::del('login_fail.' . $phone);
        self::updateLogin($userInfo['user_id'], $channel);
        if ($is_first == 1) {
            $client = new Http();
            $client->curl('apiGrantCoupon', ['coupon_id' => config('common.new_user_coupon'), 'user_id' => $userInfo['user_id']]);
        }
        return [true, '登录成功', $data];

    }


    /**
     * 通过手机号验证码登录或注册
     * @param $phone
     * @param $code
     */
    public static function loginBySocial($phone, $open_id)
    {
        $social = socialUsers::where('open_id', $open_id)->first();
        if (!$social) {
            return [false, '授权信息不存在，请重新授权', []];
        }
        $exists = self::checkUserExist($phone);
        if (!$exists) {
            return [true, '请注册', ['is_complete' => -1, 'user_id' => 0]];
        }


        if (!Users::where('phone', $phone)->exists()) {
            $load_user = self::loadOtherUser($phone, 1);
            if (!$load_user) {
                return [false, '登录失败，请重试或联系客服', [
                    'field' => 'phone',
                ]];
            }
        }

        $uid = Users::where('phone', $phone)->value('id');
        if (!$uid) {
            return [true, '请注册', ['is_complete' => -1, 'user_id' => 0]];
        }

        $data = self::getUserInfo($uid);
        if ($data['user_id'] != 0) {
            socialUsers::where('open_id', $open_id)->update(
                ['user_id' => $data['user_id']]
            );
        }
        return [true, 'success', $data];


    }

    /**
     * 登录成功后，更新用户信息
     * @param $user_id
     * @param $channel
     */
    public static function updateLogin($user_id, $channel)
    {
        $update = [];
        $date = date('Y-m-d H:i:s');
        if ($channel == 1) {
            $update = [
                'mini_login_at' => $date
            ];
        }
        if ($channel == 2) {
            $update = [
                'mobile_login_at' => $date
            ];
        }
        if ($channel == 3) {
            $update = [
                'pc_login_at' => $date
            ];
        }
        Users::where('id', $user_id)->update($update);
    }

    /**
     * 同步其他渠道用户
     * @param $phone
     */
    public static function loadOtherUser($phone, $channel)
    {
        $crm = new CrmUsersService();
        $user = $crm->userInfo(['phone' => $phone]);
        if (!$user) {
            return false;
        }
        if ($user['Gender']) {
            if ($user['Gender'] == 'female') {
                $user['Gender'] = 'F';
            } else {
                if ($user['Gender'] == 'unknown') {
                    $user['Gender'] = '';
                } else {
                    $user['Gender'] = 'M';
                }
            }

        } else {
            $user['Gender'] = '';
        }

        if ($user['Birthday']) {
            $birth = date('ymd', strtotime($user['Birthday']));
        } else {
            $birth = null;
        }
        if (!$user['CustomerName']) {
            $user['CustomerName'] = '';
        }
        $data = [
            'phone' => $phone,
            'source_type' => 2,
            'level' => $user['CustomerType'] ?? 0,
            'points' => $user['AvailablePoints'] ?? 0,
            'name' => $user['CustomerName'],
            'channel' => $channel,
            'sex' => $user['Gender'],
            'pos_id' => $user['CustomerSID'],
            'birth' => $birth,
            'created_at' => $user['Registtime'],

        ];
        return Users::firstOrCreate(['phone' => $phone], $data);
    }


    /**
     * 同步其他渠道用户
     * @param $phone
     */
    public static function loadUser($pos_id, $phone, $user = [])
    {
        Log::info('loadUser1' . ':' . $pos_id . ':' . $phone, $user);
        $crm = new CrmUsersService();
        if (empty($user)) {
            $user = $crm->userInfo(['phone' => $phone, 'pos_id' => $pos_id]);
        }

        if (!$user) {
            return false;
        }
        if ($user['Gender']) {
            if ($user['Gender'] == 'female') {
                $user['Gender'] = 'F';
            } else {
                if ($user['Gender'] == 'unknown') {
                    $user['Gender'] = '';
                } else {
                    $user['Gender'] = 'M';
                }

            }

        } else {
            $user['Gender'] = '';
        }

        if ($user['Birthday']) {
            $birth = date('ymd', strtotime($user['Birthday']));
        } else {
            $birth = null;
        }
        if (!$user['CustomerName']) {
            $user['CustomerName'] = '';
        }
        if (!$user['CustomerName']) {
            $user['CustomerName'] = '';
        }
        $data = [
            'name' => $user['CustomerName'],
            'sex' => $user['Gender'],
            'level' => $user['CustomerType'] ?? 0,
            'points' => $user['AvailablePoints'] ?? 0,
            'pos_id' => $user['CustomerSID'],
            'birth' => $birth,
            'created_at' => $user['Registtime'],
        ];
        if ($user['CustomerSID'] != $pos_id) {
            Redis::rpush('dlc_combine_member', $pos_id . ':' . $user['CustomerSID']);
            Log::info('dlc_combine_member' . ':' . $pos_id . ':' . $user['CustomerSID'], []);
        }
        return Users::where('pos_id', $pos_id)->update($data);
    }


    /**
     *
     * 获取登录成功后用户信息
     * @param $uid
     * @return array
     */
    public static function getUserInfo($user_id = '', $where = [])
    {
        if ($user_id) {
            $userInfo = Users::select('id as user_id', 'pos_id', 'phone', 'sex', 'name', 'open_id', 'nickname', 'pic', 'password', 'birth', 'created_at')->where('id', $user_id)->first();

        } else {
            $userInfo = Users::select('id as user_id', 'phone', 'pos_id', 'sex', 'name', 'open_id', 'nickname', 'pic', 'password', 'birth', 'created_at')->where($where)->first();

        }

        if ($userInfo) {
            $userInfo['is_complete'] = 0;
            if (empty($userInfo['phone']) || empty($userInfo['sex']) || empty($userInfo['password']) || empty($userInfo['name']) || strtotime($userInfo['birth']) < -1546300800 || empty($userInfo['created_at'])) {
                self::loadUser($userInfo['pos_id'], $userInfo['phone']);
                $userInfo = Users::onWriteConnection()->select('id as user_id', 'pos_id', 'phone', 'sex', 'name', 'open_id', 'nickname', 'pic', 'password', 'birth')->where('pos_id', $userInfo['pos_id'])->first();
            }
            if (empty($userInfo['phone']) || empty($userInfo['sex']) || empty($userInfo['password']) || empty($userInfo['name']) || strtotime($userInfo['birth']) < -1546300800) {
//
                $userInfo['is_complete'] = 1;
                $userInfo['user_id'] = 0;
                $userInfo['sign'] = self::getCompleteSign($userInfo['pos_id']);
            }
            if ($userInfo['sex']) {
                $userInfo['sex'] = $userInfo['sex'] == 'M' ? 1 : 0;
            } else {
                $userInfo['sex'] = '';
            }

            $userInfo['password'] = $userInfo['password'] ? '1' : '';
            $birth_time = strtotime($userInfo['birth']);
            if ($birth_time < -1546300800 || $birth_time == -28800) {
                $userInfo['birth'] = '';
            }
        }

        return $userInfo;
    }

    /**
     * 手机号验证用户是否存在
     * @param $phone
     * @return mixed
     */
    public static function checkUserExist($phone)
    {
        $user = Users::where('phone', $phone)->exists();
        if (!$user) {
            $crm = new CrmUsersService();
            $user = $crm->userExist($phone);

        }
        return $user;
    }

    /**
     * 更新用户信息
     * @param $pos_id
     * @param $update
     * @return bool
     */
    public static function updateInfo($pos_id, $update, $open_id)
    {

        $crm = new CrmUsersService();
        $user = Users::where('pos_id', $pos_id)->first();
        if (!$user->phone) {
            $array['phone'] = $update['phone'];
        }
        if (!$user->sex) {
            $array['sex'] = $update['sex'];
        }
        if (strtotime($user->birth) < -1546300800) {
            $array['birth'] = $update['birth'];
        }
        if (!$user->name) {

            if (!preg_match("/^([a-zA-Z]|[\x{4e00}-\x{9fa5}])*$/u", $update['name'])) {
                return [false, '仅支持汉字或字母，不支持特殊字符、数字、空格', [
                    'field' => 'name'
                ]];
            }
            $array['name'] = $update['name'];
        }
        if (!$user->password) {
            $array['password'] = self::encrypt_password($update['password']);
        }
        Log::info('updateInfo1:', $update);
        Log::info('updateInfo:', $array);
        $success = $crm->updateInfo($pos_id, $array);
        if ($success) {
            if (isset($array['sex'])) {
                if ($array['sex'] == 1) {
                    $array['sex'] = 'M';
                } else {
                    $array['sex'] = 'F';
                }
            }
            Users::where('pos_id', $pos_id)->first()->update($array);
            if ($open_id) {
                socialUsers::where('open_id', $open_id)->update([
                    'user_id' => $user->id,
                ]);
            }
            return [true, 'success', ['uid' => $user->id]];
        }
        Log::error('updateInfo:', $array);
        return [false, '完善信息失败，请联系客服', ['field' => 'phone']];
    }

    /**
     * 获取完善信息加密的sign
     */
    public static function getCompleteSign($pos_id)
    {
        $sign = self::encrypt_password($pos_id);
        Redis::set('complete_sign' . $pos_id, $sign, 'ex', 60 * 60);
        return $sign;
    }


    /**
     * 获取完善信息加密的sign
     */
    public static function checkCompleteSign($pos_id, $sign)
    {


        $pos_sign = Redis::get('complete_sign' . $pos_id);

        if (!$pos_sign) {
            return 2;//校验码失效
        }
        if ($pos_sign == $sign) {
            return 1;//校验成功
        }
        return 0;//校验失败
    }

    /**
     * 外部用户id生成 2 +8位数
     * pos_id
     * @return array
     */
    public static function getUid()
    {
        $nums = Redis::incr('pos_id');
        return '20' . sprintf("%08d", $nums);
    }

    /**
     * 修改用户密码
     * @param $phone
     * @param $password
     * @return boolean
     */
    public static function changePassword($phone, $password)
    {

        $status = Users::where('phone', $phone)->update(array(
            'password' => self::encrypt_password($password)
        ));

        //todo 清空用户token
        return $status;
    }

    /**
     * 发送验证码
     * @param  $type $tel  1登录 2 注册 3 找回密码 4完善信息
     * @return [array]   array('code','message')
     */
    public static function sendMessage($mobile, $type = 1)
    {
        $redis = Redis::connection('default');
        $f = $redis->setnx("lock_sendmessage" . $mobile . $type, date("Y-m-d H:i:s"));
        if ($f == 1) {
            $redis->expire("lock_sendmessage" . $mobile . $type, 3);
        } else {
            return [false, '操作频繁,请稍后重试', [
                'field' => 'phone',
                'fail_mark' => 1,
            ]];
        }
        if ($type == 1) {
            $exist = self::checkUserExist($mobile);
            if (!$exist) {
                return array(false, '您不是Sisley会员，请注册', [
                    'field' => 'phone',
                ]);
            }
        }
        if ($type == 3) {
            $id = Users::where('phone', $mobile)->value('id');
            if (!$id) {
                return array(false, '手机号在该系统中不存在，请尝试手机号验证码登录', [
                    'field' => 'phone',
                ]);
            }
        }
        if ($type == 2) {
            $exist = self::checkUserExist($mobile);
            if ($exist) {
                return array(false, '此号码已注册过Sisley会员，请登录', [
                    'field' => 'phone',
                    'tag' => 1,
                ]);
            }
        }
        if ($type == 4) {
            $type = 5;
            $exist = self::checkUserExist($mobile);
            if ($exist) {
                return array(false, '该手机号已绑定会员，请联系官网在线客服', [
                    'field' => 'phone',
                ]);
            }
        }

        $min = 'mobile:' . $mobile . $type;
        $day = 'day:' . date('Ymd') . ':' . $mobile;
        $redis->expire($day, 60 * 60 * 24);
        if ($redis->get($day) > 100) {
            return array(false, '验证码发送失败,超过当天可发送次数', [
                'field' => 'phone',
                'fail_mark' => 1,
            ]);
        }
        $code = rand_code();

        try {

            $sms = new Sms();
            $status = $sms->send($mobile, $type, $code);
            if ($status) {
                $redis->set($min, $code);
                $redis->incr($day);
                $redis->expire($min, 300);
                return array(true, '验证码发送成功', []);
            }
            return array(false, '验证码发送失败', [
                'field' => 'phone',
                'fail_mark' => 1,
            ]);
        } catch (\Exception $e) {

            return array(false, '验证码发送失败', [
                'field' => 'phone',
                'fail_mark' => 1,
            ]);
        }
    }

    /**
     * 记录登录失败账户
     * @param $account
     */
    public static function markFailUser($account)
    {
        $redis = Redis::connection('default');
        $redis->incr('login_fail.' . $account);//记录登录失败
        $redis->expire('login_fail.' . $account, 3600);

    }

    /**
     * 获取登录账号失败标识
     * @param $account
     * @return bool
     */
    public static function getFailMark($account)
    {
        $login_fail = self::$login_fail;
        $redis = Redis::connection('default');
        if ($redis->get('login_fail.' . $account) > $login_fail) {
            return true;
        }
        return false;
    }

    /**
     * 获取登录账号失败标识
     * @param $account
     * @return bool
     */
    public static function getSendMsgMark($mobile)
    {

        $redis = Redis::connection('default');
        $day = 'day:' . date('Ymd') . ':' . $mobile;
        $redis->expire($day, 60 * 60 * 24);
        if ($redis->get($day) > 1) {
            return true;
        }
        return false;
    }

    /**
     * 验证手机号
     * @param $phone
     * @param $code
     * @return array
     */
    public static function checkMsgCode($mobile, $code, $type)
    {

        $min = 'mobile:' . $mobile . $type;
        $redis = Redis::connection('default');

        //添加重复验证锁机制,防止用户重复注册和修改密码

        $f = $redis->setnx("lock_checkoutcode_" . $mobile, date("Y-m-d H:i:s"));
        if ($f == 1) {
            $redis->expire("lock_checkoutcode_" . $mobile, 10);
        } else {
            return [false, '校验次数过多,请稍后重试', [
                'field' => 'msg_code',
                'fail_mark' => 1,
            ]];
        }
        $code_value = $redis->get($min);

        if ($code_value == $code) {
            Log::info('checkMsgCode' . $mobile . 'code' . $code . 'value' . $code_value);
            return [true, 'success', []];
        }
        self::markFailUser($mobile);
        return [false, '短信验证码校验失败，请重试', [
            'field' => 'msg_code',
            'fail_mark' => 1,
        ]];
    }


//获取收藏（被动缓存）
    public static function getFavsFromCache($user_id)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.favorite', ['user_id' => $user_id]);
        $data = $redis->zrange($key, 0, -1, 'withscores');
        $data = $data ?? [];
        $data = array_reverse($data, true);
        $ret = [];
        if ($data) {
            foreach ($data as $k => $v) {
                list($pid, $type) = Help::parsePid($k);
                $ret[] = [
                    'product_idx' => $pid,
                    'type' => $type ?? 1,
                    'user_id' => $user_id,
                    'created_at' => date('Y-m-d H:i:s', $v)
                ];
            }
        } else {
            $ret = self::cacheUserFavorite($user_id);
        }
        return $ret ?: [];
    }

    public static function cacheUserFavorite($user_id)
    {
        $favs = Favorite::getUserFavorite($user_id);
        if (!$favs) return false;
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.favorite', ['user_id' => $user_id]);
        foreach ($favs as $fav) {
            $redis->zadd($key, strtotime($fav['created_at']), $fav['product_idx'] . (($fav['type'] == 2) ? "-2" : ""));
        }
        return $favs;
    }

//获取收藏（被动缓存）
    public static function getFootsFromCache($user_id)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.footprint', ['user_id' => $user_id]);
//        $redis->del($key);
        $data = $redis->zrange($key, 0, -1, 'withscores');
        $data = $data ?? [];
        $data = array_reverse($data, true);
        $ret = [];
        if ($data) {
            foreach ($data as $k => $v) {
                list($pid, $type) = Help::parsePid($k);
                $ret[] = [
                    'product_idx' => $pid,
                    'type' => $type ?? 1,
                    'user_id' => $user_id,
                    'updated_at' => date('Y-m-d H:i:s', $v)
                ];
            }
        } else {
            $ret = self::cacheUserFootprint($user_id);
        }
        return $ret ?: [];
    }

    public static function cacheUserFootprint($user_id)
    {
        $favs = Footprint::getUserFootprint($user_id);
        if (!$favs) return false;
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.footprint', ['user_id' => $user_id]);
        foreach ($favs as $fav) {
            $redis->zadd($key, strtotime($fav['updated_at']), $fav['product_idx'] . (($fav['type'] == 2) ? "-2" : ""));
        }
        return $favs ?: [];
    }


    public static function getAddressFromCache($user_id)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.address', ['user_id' => $user_id]);
//        $redis->del($key);
        $data = $redis->get($key);
        $ret = [];
        if ($data) {
            $ret = json_decode($data, true);
        } else {
            $ret = self::cacheUserAddress($user_id);
        }
        return $ret;
    }

    public static function cacheUserAddress($user_id)
    {
        $adrs = Address::getUserAddress($user_id);
        $adrs = $adrs ?: [];
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.address', ['user_id' => $user_id]);
        $redis->setex($key, $adrs ? config('common.a_day') : config('common.five_minute'), json_encode($adrs));
        return $adrs;
    }

    //获取地址（被动缓存）
    public static function getCouponsFromCache($user_id)
    {
        //改为直接从数据库拿
//        return self::cacheUserCoupons($user_id);

        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.coupon', ['user_id' => $user_id]);
//        $redis->del($key);
        $data = $redis->get($key);
        $ret = [];
        if ($data) {
            $ret = json_decode($data, true);
        } else {
            $ret = self::cacheUserCoupons($user_id);
            Help::Log($user_id . '拿用户优惠券信息', $ret, 'im');
        }
        return $ret ?: [];
    }

    public static function cacheUserCoupons($user_id)
    {
        $adrs = UserCoupon::getUserCoupon($user_id);
        if (!$adrs) return [];
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.coupon', ['user_id' => $user_id]);
        $redis->setex($key, config('common.five_minute'), json_encode($adrs));
        return $adrs;
    }

    public static function clearUserCouponCache($user_id)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.coupon', ['user_id' => $user_id]);
        $redis->del($key);
        return true;
    }


//获取地址（被动缓存）
    public static function getUserInfoFromCache($user_id)
    {
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.info', ['user_id' => $user_id]);
//        $redis->del($key);
        $data = $redis->get($key);
        $ret = [];
        if ($data) {
            $ret = json_decode($data, true);
        } else {
            $ret = self::cacheUserInfo($user_id);
            Help::Log($user_id . '拿用户优惠券信息', $ret, 'im');
        }
        return $ret;
    }

    public static function cacheUserInfo($user_id)
    {
        $adrs = Users::getUserInfo($user_id);
        if (!$adrs) return false;
        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.info', ['user_id' => $user_id]);
        $redis->setex($key, 120, json_encode($adrs));
        return $adrs;
    }

    /**
     * 密码加密
     * @param $plain
     * @return string
     */
    public static function encrypt_password($plain)
    {
        $password = '';

        for ($i = 0; $i < 10; $i++) {
            $password .= mt_rand();
        }

        $salt = substr(md5($password), 0, 2);

        $password = md5($salt . $plain) . ':' . $salt;

        return $password;
    }

    /**
     * 验证密码
     * @param $uid
     * @param $password
     * @return array
     */
    public static function checkPassword($password, $crypt)
    {
        return self::decrypt_password($password, $crypt);

    }

    /**
     * 密码解密
     * @param $plain
     * @param $crypt
     * @return bool
     */
    public static function decrypt_password($plain, $crypt)
    {

        $crypt = explode(':', $crypt);
        if ($crypt[0] == md5($crypt[1] . $plain)) {
            return true;
        } else {
            return false;
        }
    }

    public static function grantCouponForUser($user_id, $coupon_id, $couponType = 2)
    {
        $wechatCoupons = new UserCoupon();
        $wechatCoupons->user_id = $user_id;
        $wechatCoupons->coupon_id = $coupon_id;
        $wechatCoupons->type = $couponType;
        $wechatCoupons->received_at = date('Y-m-d H:i:s');
        $wechatCoupons->save();
    }

    //coupon_ids 可以是数组 也可以是单个券
    public static function getCouponInfosFromApi($coupon_ids)
    {
        $req_id = getmypid();
        Help::Log('prod /promotion/coupon/allList start api:' . $req_id, [microtime(true)], 'im');
        $ori = $coupon_ids;
        if (!is_array($coupon_ids)) $coupon_ids = [$coupon_ids];
        $url = env('PROMOTION_DOMAIN') . '/promotion/coupon/allList';

        $headers = ['Content-Type: application/json'];
        // 通过$myCouponIds 获取优惠券信息
        $response = http_request($url, $coupon_ids, $headers, 'POST', '获取优惠券信息curl：');
        Help::Log('prod /promotion/coupon/allList retuurn:', $response, 'im');
        if ($response['httpCode'] !== 200) return [false, '获取优惠券信息异常'];

        $result = json_decode($response['data'], true);
        if (!$result['code']) return [false, $response['msg'] ?? '信息异常'];
        $data = $result['data'];
        if ($data && is_array($data)) {
            $infos = array_combine(array_column($data, 'id'), $data);
            Help::Log('prod /promotion/coupon/allList end api:' . $req_id, [microtime(true)], 'im');
            return [true, is_array($ori) ? $infos : ($infos[$ori] ?? [])];
//            foreach($coupon_ids as $coupon_id){
//                $info = $infos[$coupon_id]??[];
//                $coupon = $coupons[$coupon_id]??[];
//                if(!$info || !$coupon) continue;
//                $info['start'] = date('Y-m-d H:i:s',$info['start']);
//                $info['end'] = date('Y-m-d H:i:s',$info['end']);
//                if($coupon['used_at']) $ret['used_coupons'][] = $info;
//                elseif($info['status'] == 1 ) $ret['out_date_coupons'][] = $info;
//                elseif($info['status'] == 0 ) $ret['vaild_coupons'][] = $info;
//            }
        }
        return [false, '数据异常'];
    }

    //优惠券合法性校验
    public static function checkCouponLegal($coupon_info, $user_coupon_info = [], $checkStock = false)
    {
        if (empty($coupon_info)) return false;
        if ($coupon_info['status']) return false;
        if ($checkStock && empty($coupon_info['stock'])) return false;
        //按领取时间计时的
        if ($coupon_info['expire_days'] && $user_coupon_info && ((strtotime($user_coupon_info['received_at']) + $coupon_info['expire_days'] * 86400) <= time())) return false;
        return true;
    }

    public static function incrementCouponQtyFromApi($couponId)
    {
        // 优惠券，库存
        $url = env('PROMOTION_DOMAIN') . '/promotion/coupon/incrementCouponQty';
        $headers = ['Content-Type: application/json'];

        $response = http_request($url, [$couponId], $headers, 'POST', '更新优惠券库存和领取数量：');
        Help::Log("prom /promotion/coupon/incrementCouponQty", $response);
        $result = json_decode($response['data'] ?? '', true);
        if (($response['httpCode'] !== 200) || empty($result['code'])) {
            logger("更新优惠券库存和领取数量异常：", [$couponId]);
            return [false, '库存扣除失败'];
        }

        return [true, []];
    }

    public static function restoreCouponQty($couponId)
    {
        // 优惠券，库存
        $url = env('PROMOTION_DOMAIN') . '/promotion/coupon/restoreCouponQty';
        $headers = ['Content-Type: application/json'];

        $response = http_request($url, [$couponId], $headers, 'POST', '归还优惠券库存：');
        Help::Log("prom /promotion/coupon/restoreCouponQty", $response);
        $result = json_decode($response['data'] ?? '', true);
        if (($response['httpCode'] !== 200) || empty($result['code'])) {
            logger("更新优惠券库存和领取数量异常：", [$couponId]);
            return [false, '库存扣除失败'];
        }

        return [true, []];
    }

    /**
     * 通过手机号验证码登录或注册
     * @param $phone
     * @param $code
     */
    public static function socialLogin($phone, $code, $channel, $open_id)
    {


        //验证短信
        list($success, $message) = self::checkMsgCode($phone, $code, 1);
        if (!$success) {
            return [false, $message, [
                'field' => 'msg_code',
                'fail_mark' => 1,
            ]];
        }
        $exists = self::checkUserExist($phone);
        if (!$exists) {
            //注册
            return [1, '您不是Sisley会员，请注册', ['field' => 'phone']];
        }

        $social = socialUsers::where('open_id', $open_id)->first();
        if (!$social) {
            return [false, '授权信息不存在，请重新授权', []];
        }
        if (!Users::where('phone', $phone)->exists()) {
            $load_user = self::loadOtherUser($phone, $channel);
            if (!$load_user) {
                return [false, '绑定失败，请重试或联系客服', [
                    'field' => 'phone',
                ]];
            }
        }
        if ($open_id) {
            $userInfo = Users::select('id as user_id', 'password', 'open_id')->where('phone', $phone)->first();
            if ($userInfo['open_id'] && $open_id == $userInfo['open_id']) {
                $data = self::getUserInfo($userInfo['user_id']);
                self::updateLogin($userInfo['user_id'], $channel);
                return [true, '登录成功', $data];
            }

            $arr = json_decode($social['social_info']);
            if ($arr) {
                $update = [
                    'nickname' => $arr['nickName'],
                    'pic' => $arr['avatarUrl'],
                    'city' => $arr['city'],
//                    'sex' => $arr['gender'] == '1' ? 'M' : 'F',
//                    'country' => $arr['country'],
//                    'province' => $arr['province'],
                    'open_id' => $arr['openId'],

                ];
            }
            $update['open_id'] = $open_id;
            $exception = DB::transaction(function () use ($userInfo, $update, $open_id) {
                Users::where('id', $userInfo['user_id'])->update($update);
                socialUsers::where('open_id', $open_id)->update([
                    'user_id' => $userInfo['user_id'],
                ]);
            });
            if ($exception) {
                return [false, '登录异常', ['field' => 'account']];
            }

        }

        $redis = RedisM::getRedis();
        $key = RedisM::getKey('rk.user.phone', ['phone' => $phone]);
        $cacheUserInfo = $redis->get($key);
        if ($cacheUserInfo === null) {
            $userInfo = Users::select('id as user_id', 'password')->where('phone', $phone)->first();
            $expire = $userInfo ? 1800 : 300;
            $redis->setex($key, $expire, json_encode($userInfo ?? []));
        } else {
            $userInfo = json_decode($cacheUserInfo, true);
        }

        if (!$userInfo) {
            return [false, '您不是Sisley会员，请注册', ['field' => 'phone']];
        }
        $data = self::getUserInfo($userInfo['user_id']);
        Redis::del('login_fail.' . $phone);
        self::updateLogin($userInfo['user_id'], $channel);
        return [true, '登录成功', $data];


    }

    //归类用户优惠券
    public static function groupUserCoupons($userCoupons, $couponInfos = [])
    {
        if (!$couponInfos) {
            $coupon_ids = array_column($userCoupons, 'coupon_id');
            list($suc, $couponInfos) = self::getCouponInfosFromApi($coupon_ids);
            if (!$suc) return [];

//            $skus = [];
//            foreach ($couponInfos as $info) {
//                if ($info['coupon_type'] == 'product_coupon') {
//                    if (!empty($info['sku'])) $skus[] = $info['sku'];
//                }
//            }
//
//            if ($skus) {
//                $http = new Http();
//                $skus = array_unique($skus);
//
//                $products = $http->curl('outward/product/getProductInfoBySkuIds', ['sku_ids' => implode(',', $skus), 'simple' => 1]);
//                Help::Log('outward/product/getProductInfoBySkuIds:', ['sku_ids' => implode(',', $skus), 'products' => $products]);
//                $products = $products['data'] ?? [];
//            }
//
//            foreach ($couponInfos as $k => $info) {
//                if ($info['coupon_type'] == 'product_coupon') {
//                    $p = $products[$info['sku'] ?? ''] ?? [];
//                    $couponInfos[$k]['product_name'] = $p['product_name'] ?? '';
//                    $couponInfos[$k]['kv_image'] = $p['kv_image'] ?? '';
//                }
//            }
        }
        $ret = [];

        foreach ($userCoupons as $coupon) {
            $info = $couponInfos[$coupon['coupon_id']] ?? [];
            if (!$info || !$coupon) continue;
            $end = (!empty($info['expire_days'])) ? min((strtotime($coupon['received_at']) + $info['expire_days'] * 86400), intval($info['end'])) : intval($info['end']);
            $info['vaild_desc'] = (($info['end'] - $info['start']) > 86400 * 365 * 10) ? '永久有效' : '';   //大于10年 永久有效
            $info['start'] = date('Y-m-d H:i:s', intval($info['start']));
            $info['end'] = date('Y-m-d H:i:s', $end);
            if (!empty($info['active']) && ($info['active'] == 3))    //未激活
                $info['status'] = 1;    //过期
            else
                $info['status'] = ($end > time()) ? 0 : 1;    //expire_days 的存在

            if ($coupon['used_at']) $ret['used_coupons'][] = $info;
            elseif ($info['status'] == 1) $ret['out_date_coupons'][] = $info;
            elseif ($info['status'] == 0) $ret['vaild_coupons'][] = $info;
        }
        return $ret;
    }


}