<?php namespace App\Service;

use Illuminate\Support\Facades\Redis;
use Exception;
use App\Model\Fission;
use App\Lib\Http;
class FissionService
{
    public function __construct()
    {

        $this->fissionModel = new Fission();
        $this->http =new Http();

    }
    public function getList($params =[])
    { 
        $result = \DB::table('fission')->get();
        return $result;

    }

    public function edit()
    {

    }

    public function add($params =[])
    {
        //return json_encode($params);
        $result = \DB::table('fission')->insertGetId($params);
        return $result;
    }

    /**
     * [buildRelation description]
     * @Author   Peien
     * @DateTime 2020-07-30T20:00:07+0800
     * @param    array                    $params [description]
     * @return   [type]                           [description]
     */
    public function buildRelation($params =[])
    {
        //查询当前的有效的裂变活动
        $curr_time = date('Y-m-d H:i:s');
        //查询maxNum 
        $where = [
            ['status','=',2],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $result = Fission::where($where)->orderBy('id','desc')->first();
        if(!$result)return ['code'=>0,'msg' => '无裂变的规则'];
        $resultArray = $result->toArray();
        //$maxNum= Redis::HGET('fission',$resultArray['id'])?? 0;
        //\Log::info('redis fission maxNum'. $resultArray['id'] .'='. $maxNum. '&限制条件='.  $resultArray['max_num']);
        //插入关系表中  并增加maxNum
        $pic = \DB::table('tb_users')->where('id', $params['member_id'])->select('pic')->first();
        $data = [
            'pid' => $params['fission_id'],
            'sub_id' => $params['member_id'],
            'fission_id' => $resultArray['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'pic'        =>  $pic->pic ?? '' 
        ];
        //$params['maxNum'] = $maxNum+1;
        \DB::table('member_relation')->insertGetId($data);
        //设置用户在此次裂变活动中裂变了多少人
        Redis::INCR('fission_id'.$resultArray['id'].'user_id'.$params['fission_id']);
        /*if($maxNum >= $resultArray['max_num'])
        {
            \Log::info($resultArray['id']. '已到达赠送限制条件');
            return ['code'=>0,'msg' => '已到达赠送限制条件'];
        }
        else
        {*/
            //处理赠送优惠卷和优惠码的
            $result = $this->giveOut($params, $resultArray);
            return $result;
        /*}*/
    }

    /**
     * [giveOut 送券]
     * @Author   Peien
     * @DateTime 2020-07-31T10:26:26+0800
     * @param    array                    $params  [description]
     * @param    array                    $fission [description]
     * @return   [type]                            [description]
     */
    public  function  giveOut($params = [], $fission =[])
    {
        $coupon_id = $this->getUserCoupon($fission,$params);

        \Log::info('获取优惠券数量',[$params,$fission]);
        //检测用户是否已经拥有优惠卷或者优惠码
        $couponResult= $this->http->curl('apiGetUserCouponInfo',['coupon_id' => $coupon_id, 'user_id' => $params['fission_id']]);
        $couponCount = 0;
        if(isset($couponResult) && $couponResult['code'] == 1)
        {
            $couponCount = $couponResult['data']['num'];
        }
        \Log::info('用户获取优惠券数量',[$couponResult]);
         if($couponCount) return ['code' => 0 ,'msg' => '你的优惠券领取已达上限'];
        /*//此裂变是否是赠送单张
        if($fission['step'] == 1)
        {
            if($couponCount) return ['code' => 0 ,'msg' => '你的优惠券领取已达上限'];
        }else
        {
            $count = \DB::table('member_relation')->where(['pid'=> $params['fission_id'], 'fission_id'=> $fission['id']])->count();
            $fission_num = intval($count/$fission['condition_value']);
            if($couponCount >= $fission_num) return ['code' => 0 ,'msg' => '你的优惠券领取已达上限'];
        }*/
        $result= $this->http->curl('apiGrantCoupon',['coupon_id' => $coupon_id, 'user_id' => $params['fission_id']]);
        if(isset($result))
        {
            
            if($result['code'] != 1){
                 \Log::info('优惠券发放失败');
                return ['code' => 0,'msg' => '优惠券发放失败'];
            }
        }
        else
        {
            return ['code' => 0,'msg' => '优惠券发放失败'];
        }
        \Log::info('发放优惠券接口结果',[$result]);
        //发放优惠卷 //TODO
        //记录一个发放记录
        $name = \DB::table('tb_users')->where('id', $params['fission_id'])->select('phone')->first();
        $data = [
            'fission_id' => $fission['id'],
            'code_id'    => $coupon_id,
            'type'       => $fission['type'],
            'member_id'  => $params['fission_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'member_name' => $name->phone ?? ' 匿名账户',
        ];
        //Redis::HSET('fission',$fission['id'],$params['maxNum']);
        \DB::table('fission_award_log')->insertGetId($data);
        return true;
    }

    /**
     * [getFissionRank 获取排行榜 top10]
     * @Author   Peien
     * @DateTime 2020-07-31T10:28:42+0800
     * @return   [type]                   [description]
     */
    public static function getFissionRank()
    {
        $result = Redis::HGET('fission_rand',date('Y-m-d'));
        return json_decode($result,true);
    }
    /**
     * [setFissionRank 设置排行榜 top10]
     * @Author   Peien
     * @DateTime 2020-07-31T10:29:27+0800
     */
    public  static function setFissionRank()
    {
        //查询最新一笔获奖记录-用来获取裂变id
        $fission_id = \DB::table('fission_award_log')->select('fission_id')->orderBy('id','desc')->limit(1)->get()->toArray();
        \Log::info($fission_id);
        if(empty($fission_id))
            return ['code' => 0, 'msg' => '无列表'];
        $fission_id =$fission_id[0]->fission_id;
        //查询当月的数据
        $month = date('m');
        /*if(date('d') == '01')
        {
            $month = date('m-1');
        }*/
        $result = \DB::table('fission_award_log')->whereYear('created_at',date('Y'))->whereMonth('created_at',$month)->where('fission_id', $fission_id)->select(\DB::raw('count(*) as c,member_id,member_name'))->groupBy('member_id','member_name')->orderBy('c','desc')->limit(10)->get()->toArray();
        \Log::info('每天统计当月排行榜',[$result]);
        if($result)
        {   
            //设置redis
            Redis::HSET('fission_rand',date('Y-m-d'),json_encode($result));
        }
    }


    /**
     * [getUserCoupon 计算用户应该获得的优惠券]
     * @Author   Peien
     * @DateTime 2020-08-22T17:32:03+0800
     * @param    [type]                   $info [description]
     * @return   [type]                         [description]
     */
    public function getUserCoupon($info, $params)
    {
        $user_count = Redis::GET('fission_id'.$info['id'].'user_id'.$params['fission_id']);
        $condition_value_array = explode(',', $info['condition_value']);
        $value_id_array    = explode(',', $info['value_id']);
        $list = [];
        foreach ($condition_value_array as $key => $value) {
            if($value <= $user_count)
            {
                $list[$key]['coupon'] = $value_id_array[$key];
                $list[$key]['id'] = $value;
            }
        }
        if(empty($list)) return 0;
        $last_names = array_column(array_values($list),'id');
         array_multisort($last_names,SORT_DESC,$list);
         \Log::info('优惠券id'.$list[0]['coupon']);
        return $list[0]['coupon'];
    }


    public function getFissionInfo($params)
    {
        //查询当前的有效的裂变活动
        $curr_time = date('Y-m-d H:i:s');
        //查询maxNum 
        $where = [
            ['status','=',2],['start_time','<',$curr_time],['end_time','>',$curr_time],//激活，有效的
        ];
        $result = Fission::where($where)->orderBy('id','desc')->first();
        if(!$result)return ['code'=>0,'msg' => '无裂变的规则'];
        $resultArray = $result->toArray();
        $condition_value_array = explode(',', $resultArray['condition_value']);
        $value_id_array    = explode(',', $resultArray['value_id']);
        $list = [];
        foreach ($condition_value_array as $key => $value) {
                $list[$key]['coupon'] = $value_id_array[$key];
                $list[$key]['id'] = $value;
                $list[$key]['desc'] = '成功邀请'.$value.'人得';
        }
        if(empty($list)) return 0;
        $last_names = array_column(array_values($list),'id');
        array_multisort($last_names,SORT_ASC,$list);
        foreach ($list as $value) {
            $coupon[] = $value['coupon'];
        }
        \Log::info('优惠券xinxi',[$coupon]);
        $couponResult= $this->http->curl('promotion/cart/productList',$coupon);
        \Log::info('优惠券xinxi',[$couponResult]);
        foreach ($couponResult['data'] as $key => $value) {
            $couponCount = false;
            $couponExist= $this->http->curl('apiGetUserCouponInfo',['coupon_id' => $value['id'], 'user_id' => $params['user_id']]);
            if(isset($couponExist) && $couponExist['code'] == 1)
            {
                $couponCount = $couponExist['data']['num'] ? true :false;
            }
            $list[$key]['name'] = $value['desc'];
            $list[$key]['sku'] = $value['sku'];
            $list[$key]['type'] = $value['coupon_type']== 'coupon' ? '优惠券类': '实物券';
            $list[$key]['coupon_id'] = $value['id'];
            $list[$key]['couponExist'] = $couponCount;
        }
        $detail['coupon'] = $list;
        $detail['count'] = 0;
        $detail['list'] = [];
        //裂变人员信息
        $member_relation = \DB::table('member_relation')->where(['fission_id' => $resultArray['id'], 'pid' => $params['user_id']])->get()->toArray();
        if(!empty($member_relation))
        {
            foreach ($member_relation as $value) {
                $pic[] = $value->pic;
            }
            $detail['list'] = $pic;
            $detail['count'] = count($member_relation);
        }

        return ['code' => 1, 'data' => $detail];






    }











}
