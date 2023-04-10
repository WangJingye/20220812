<?php
namespace App\Service\DashBoard;

use Illuminate\Support\Facades\DB;

class DashBoard
{
    public $user_table = 'tb_users';

    //获取注册的总数据
    public function getRegisterTotal($start_time,$end_time){
        $mini_register_count = DB::table($this->user_table)
                    ->where('source_type',1)
                    ->where('channel',1)
                    ->whereRaw('created_at >? and created_at < ?',[$start_time,$end_time])
                    ->count();
        $h5_register_count = DB::table($this->user_table)
            ->where('source_type',1)
            ->where('channel',2)
            ->whereRaw('created_at >? and created_at < ?',[$start_time,$end_time])
            ->count();
        $pc_register_count = DB::table($this->user_table)
            ->where('source_type',1)
            ->where('channel',3)
            ->whereRaw('created_at >? and created_at < ?',[$start_time,$end_time])
            ->count();
        $fission_register_count = DB::table($this->user_table)
            ->where('source_type',1)
            ->where('share_from','>',1)
            ->whereRaw('created_at >? and created_at < ?',[$start_time,$end_time])
            ->count();
        $guide_register_count = DB::table($this->user_table)
            ->where('source_type',1)
            ->where('guide_id','>',1)
            ->whereRaw('created_at >? and created_at < ?',[$start_time,$end_time])
            ->count();
        $data['register_count'] = [
            'mini'=>$mini_register_count,
            'h5'=>$h5_register_count,
            'pc'=>$pc_register_count,
            'fission'=>$fission_register_count,//裂变分享注册
            'guide'=>$guide_register_count,//导购分享
        ];
        $total_register = bcadd($mini_register_count , $h5_register_count);
        $total_register = bcadd($total_register,$pc_register_count);
        if($total_register < 1){
            $data['register_percent'] = [
                'mini'=>0,
                'h5'=>0,
                'pc'=>0,
            ];
            return $data;
        }
        $mini_percent = (int) (bcdiv($mini_register_count,$total_register,2) * 100) ;
        $h5_percent = (int) (bcdiv($h5_register_count,$total_register,2) * 100) ;
        $pc_percent = (int) bcsub(100,$mini_percent)  ;
        $pc_percent = (int) bcsub($pc_percent,$h5_percent)  ;
//        $mini_percent = $mini_percent . '%';
//        $h5_percent = $h5_percent . '%';
//        $pc_percent = $pc_percent . '%';
        $data['register_percent'] = [
            'mini'=>$mini_percent,
            'h5'=>$h5_percent,
            'pc'=>$pc_percent,
        ];
        return $data;
    }

    //登录数据
    public function getLoginData($start_time,$end_time){
        $mini_login_count = DB::table($this->user_table)
            ->whereRaw('mini_login_at >? and mini_login_at < ?',[$start_time,$end_time])
            ->count();
        $mobile_login_count = DB::table($this->user_table)
            ->whereRaw('mobile_login_at >? and mobile_login_at < ?',[$start_time,$end_time])
            ->count();
        $pc_login_count = DB::table($this->user_table)
            ->whereRaw('pc_login_at >? and pc_login_at < ?',[$start_time,$end_time])
            ->count();
        $data['login_count'] = [
            'mini'=>$mini_login_count,
            'h5'=>$mobile_login_count,
            'pc'=>$pc_login_count,
        ];
        $total_login = bcadd($mini_login_count,$mobile_login_count);
        $total_login = bcadd($total_login,$pc_login_count);
        if($total_login < 1){
            $data['login_percent'] = [
                'mini'=>0,
                'h5'=>0,
                'pc'=>0,
            ];
            return $data;
        }
        $mini_percent = (int) (bcdiv($mini_login_count,$total_login,2) * 100);
        $h5_percent = (int) (bcdiv($mobile_login_count,$total_login,2) * 100);
        $pc_percent = (int) (bcsub(100,$mini_percent) );
        $pc_percent = (int) bcsub($pc_percent,$h5_percent);
//        $mini_percent = $mini_percent . '%';
//        $h5_percent = $h5_percent . '%';
//        $pc_percent = $pc_percent . '%';
        $data['login_percent'] = [
            'mini'=>$mini_percent,
            'h5'=>$h5_percent,
            'pc'=>$pc_percent,
        ];
        return $data;
    }
}