<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/8/17
 * Time: 15:49
 */

namespace App\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Model\Users;

class MasterVipService
{
    static $sex_list = [
        'F' => "女士",
        'M' => "先生"
    ];

    public function makeMasterVipData(){
        $redis = Redis::connection();
        $makeTime = date("Ymd_His");
        $member_id_redis = 'member_'.date("Ymd", strtotime("-1 day"));;
        $member_count = $redis->scard($member_id_redis);
        if($member_count > 0){
                $member_id_list  = $redis->smembers($member_id_redis);
                foreach ($member_id_list as $user_id){
                    $this->makeMemberInfoData($user_id, $makeTime);
                }
        }
    }

    private function makeMemberInfoData($user_id, $makeTime)
    {
        try {
            $member_info = Users::getUserInfo($user_id);
            if (sizeof($member_info)) {
                $path = base_path();
                $master_vip_name = $path . "/public/EC_VIP_" . $makeTime . ".txt";
                if(env("APP_ENV") !== "local"){
                    $master_vip_name = "/opt/ecToPos/EC_VIP_" . $makeTime . ".txt";
                }
                $member_detail_line_tmp = $this->makeMemberDetailTmp($member_info);
                file_put_contents($master_vip_name, $member_detail_line_tmp, FILE_APPEND);
            } //Log::Info(date("Y-m-d H:i:s" ) . "订单生成txt成功");

            else {
                Log::Info(date("Y-m-d H:i:s" ) . "当天无member信息生成");
            }
        }
        catch (\Exception $exception){
            throw $exception;
        }
        return "success";
    }



    private function makeMemberDetailTmp($member_info)
    {
        $newMasterVipInfo = [];
        $newMasterVipInfo['posId'] = $member_info['pos_id']; //POS ID, posid为10位，你们从2000000001开始一直使用到2999999999，共10亿个会员数
        $newMasterVipInfo['gender'] = @self::$sex_list[$member_info['sex']]; //性别
        $newMasterVipInfo['CustomerName'] = $member_info['name'];//客户姓名
        $newMasterVipInfo['address'] = ''; //地址,实际上我们的用户信息表中没有这个字段
        $newMasterVipInfo['zip_code'] = ''; //邮编
        $newMasterVipInfo['city'] = $member_info['city']; //城市
        $newMasterVipInfo['province'] = $member_info['province']; //省份
        $newMasterVipInfo['HomePhone'] = '';//家庭电话，可以为空
        $newMasterVipInfo['YearBirth'] = date("Y",strtotime($member_info['birth']));//生日的年份
        $newMasterVipInfo['BirthdayDT'] = date("md",strtotime($member_info['birth']));//生日的月份和日期
        $newMasterVipInfo['Email'] = $member_info['email'];//出库时间在下单时不存在
        $newMasterVipInfo['GenderCode'] = '';//性别，非必填
        $newMasterVipInfo['CreationStore'] = '5004';//门店号，固定为5004
        $newMasterVipInfo['CreationBA'] = '5004';//BA号固定为5004
        $newMasterVipInfo['OfficeTelePhone'] = '';//办公室电话，默认为空就好
        $newMasterVipInfo['Mobile'] = $member_info['phone'];//客人注册的电话
        $JoinDate = isset($member_info['created_at']) ? $member_info['created_at'] : date("Y-m-d H:i:s",strtotime("-1 day"));
        $newMasterVipInfo['JoinDate'] = date("Y-m-d H:i:s",strtotime($JoinDate));//成为会员的日期，非必填
        $newMasterVipInfo['TransDate'] = '';//首次交易日期
        $newMasterVipInfo['CustomerIdentifier'] = $member_info['phone'];//识别码，目前使用的是手机号
        $newMasterVipInfo['ecard'] = '';//会员线上卡号，非必填
        $vipInfo_tmp = implode('^' , $newMasterVipInfo);
        return  $vipInfo_tmp . PHP_EOL;
        return iconv('utf-8', 'UTF-8', $vipInfo_tmp . PHP_EOL);
    }
}