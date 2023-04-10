<?php namespace App\Service\Dlc;

use App\Model\Users;
use App\User;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Bus\Dispatcher;

class UsersService
{
    /**
     * 授权根据openid保存用户信息
     * @param $open_id
     * @param array $values
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function userUpdateOrInsertByOpenId($open_id, array $values = []){
        Users::query()->updateOrInsert($open_id,$values);
        return Users::query()->where('open_id',$open_id)->first();
    }

    /**
     * 用户入会
     * @param $open_id
     * @param $phone
     * @return mixed
     * @throws \Exception
     */
    public static function userRegister($open_id,$phone){
        //检查该手机号是否已被使用(排除自己)
        $isUsed = Users::query()->where('phone',$phone)
            ->where('open_id','<>',$open_id)->count();
        if($isUsed){
            throw new \Exception('该手机号已被绑定');
        }
        //本地保存
        $user = Users::query()->where('open_id',$open_id)->first();
        $isNew = $user->phone?0:1;
        $updateData = [
            'phone'=>$phone,
            'pos_id'=>$phone,
        ];
        if($isNew==1){
            $updateData['reg_time'] = date('Y-m-d H:i:s');
        }
        $user->update($updateData);
        //pos_id保存到缓存里
        self::setPosId($user->id,$phone);
        //判断是否是导购并且绑定（根据手机号）
        \App\Model\SaList::bindAll(compact('phone'));
        //发送新人券
        if($isNew==1){
            if(class_exists(\App\Dlc\Coupon\Service\UserService::class)){
                \App\Dlc\Coupon\Service\UserService::getInstance()->addNew($user->id);
            }
        }
        return $user->id;
    }

    /**
     * @param $uid
     * @return array
     * @throws \Exception
     */
    public static function getUserProfile($uid){
        $user = Users::query()->find($uid);
        if($user){
            return [
                'nickName'=>$user->nickname,
                'mobile'=>$user->phone,
                'name'=>$user->name,
                'gender'=>$user->sex,
                'birthday'=>$user->birth,
                'email'=>$user->email,
            ];
        }throw new \Exception('用户不存在');
    }

    /**
     * 更新用户信息
     * @param $uid
     * @param $name
     * @param $gender
     * @param $birthday
     * @param string $email
     * @return bool
     * @throws \Exception
     */
    public static function updateUserProfile($uid,$name,$gender,$birthday,$email=''){
        $user = Users::query()->find($uid);
        if($user){
            $params = [
                'name'=>$name,
                'sex'=>$gender,
                'birth'=>$birthday,
                'email'=>$email,
            ];
            if(empty($user->birth)){
                $params['birth'] = $birthday;
            }
            self::userUpdateOrInsertByOpenId(['open_id'=>$user->open_id],$params);
            return true;
        }throw new \Exception('用户不存在');
    }

    public static function getUserInfoByOpenid($open_id)
    {
        return Users::query()->where('open_id',$open_id)->first();
    }

    /**
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public static function getUserById($uid){
        return Users::query()->find($uid);
    }

    /**
     * 获取用户类型 (0 游客, 1 官网会员, 2 全渠道会员)
     * @param \Illuminate\Database\Eloquent\Model $user
     * @return int
     */
    public static function getUserType(\Illuminate\Database\Eloquent\Model $user=null){
        if(!empty($user)){
            return $user->pos_id?2:1;
        }return 0;
    }

    public static function getUserAuthor(\Illuminate\Database\Eloquent\Model $user=null){
        return empty($user->avatar_url)?0:1;
    }

    /**
     * 获取用户类型 (0 游客, 1 官网会员, 2 全渠道会员)
     * @param int $uid
     * @return int
     */
    public static function getUserTypeByUid($uid = 0){
        if($uid){
            $posId = self::getPosId($uid);
            if($posId){
                return 2;
            }return 1;
        }return 0;
    }

    public static function getPosIdByUid($uid){
        return self::getPosId($uid);
    }

    public static function getUserCenter(\Illuminate\Database\Eloquent\Model $user){
        $nextLevelRemaining = sprintf("%.2f",$nextLevelRemaining??1);
        $nextLevelNeed = sprintf("%.2f",$nextLevelNeed??1);
        $info = [
            'name'=>$user->nickname,
            'avatarUrl'=>$user->avatar_url,
            'nextLevelRemaining'=>$nextLevelRemaining??1,//下一个等级剩余的金额
            'nextLevelNeed'=>$nextLevelNeed??1,//下一个等级需要的金额
            'level'=>$level??1,
        ];
        //获取指定订单状态的订单数量
        $respOrderStatusCount = app('ApiRequestInner')->request('getOrderStatusCount','POST',[
            'uid'=>$user->id
        ]);
        $orderCount = [
            'pending'=>0,
            'paid'=>0,
            'shipped'=>0,
            'finished'=>0,
        ];
        if($respOrderStatusCount['code']==1 && !empty($respOrderStatusCount['data'])){
            $orderStatusCount = $respOrderStatusCount['data'];
            $orderCount['pending'] = array_get($orderStatusCount,1)?:0;
            $orderCount['paid'] = array_get($orderStatusCount,3)?:0;
            $orderCount['paid'] += array_get($orderStatusCount,12)?:0;
            $orderCount['shipped'] = array_get($orderStatusCount,4)?:0;
            $orderCount['finished'] = array_get($orderStatusCount,9)?:0;
            $orderCount['finished'] = array_get($orderStatusCount,10)?:0;

            //数量大于等于100则显示99+
            foreach($orderCount as &$count){
                if($count>=100){
                    $count = '99+';
                }
            }
        }
        return compact('info','orderCount');
    }

    /**
     * 获取用户中心广告位
     * @param $userType
     * @return mixed
     */
    public static function getCenterAd($userType){
        $ad_name = [
            0=>'account_center_guest',
            1=>'account_center_vip',
            2=>'account_center_vvip',
        ];
        $ad = HelperService::getAd(array_get($ad_name,$userType));
        return array_reduce($ad?:[],function($result,$item){
            $result[] = [
                'image'=>$item['img'],
                'link'=>$item['link'],
            ];
            return $result;
        },[]);
    }

    const POS_ID='member:pos_id';
    /**
     * 设置member code(用户授权手机号时)
     * @param $uid
     * @param $posId
     * @return bool
     */
    public static function setPosId($uid,$posId){
        Redis::hset(config('app.name').':'.self::POS_ID,$uid,$posId);
        return true;
    }

    /**
     * 获取member code
     * @param $uid
     * @return mixed|string
     */
    public static function getPosId($uid){
        $posId = Redis::hget(config('app.name').':'.self::POS_ID,$uid);
        if(empty($posId)){
            //如果没有则去mysql中获取
            $posId = Users::query()->where('id',$uid)->value('pos_id');
            if(empty($posId)){
                $posId = 'null';
            }
            self::setPosId($uid,$posId);
        }return ($posId=='null')?'':$posId;
    }

    public static function exportMember(){
        $csv[] = ['OPENID','UNIONID','微信昵称','手机号','生日','创建时间'];
        $list = Users::query()->get(['open_id','union_id','nickname','phone','birth','created_at']);
        foreach ($list as $item) {
            $csv[] = [
                $item->open_id,
                $item->union_id,
                $item->nickname,
                $item->phone,
                $item->birth,
                $item->created_at->format('Y-m-d H:i:s')
            ];
        }
        return $csv;
    }

    public static function getUserInfo($uid){
        return Users::query()->find($uid);
    }


}