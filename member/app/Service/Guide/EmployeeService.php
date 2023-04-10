<?php
namespace App\Service\Guide;

use App\Model\Employee;
use App\Model\EmployeeCode;
use App\Model\EmployeeMember;
use App\Model\EmployeeUserShare;
use Illuminate\Support\Facades\Log;

//导购分享
class EmployeeService
{
    public $validate_days = 7; 
    
    public function getGuideInfo($user_id)
    {
        $guideInfo = $this->getGuid($user_id);
        $member_code = $this->getMemberGuide($user_id);
        $result = [
            'member_code' => $member_code['data']['empid'] ?? 0,
            'store_code'  => $guideInfo['data']['store_code'] ?? 0,
            'guide_code'  => $guideInfo['data']['guide_code'] ?? 0
        ];
        \Log::info('获取导购信息',[$result]);
        return $result;
    }

    //获取是否有导购
    public function getGuid($user_id){
        $validate_seconds = (int) $this->validate_days * 60*60 * 24 ; 
        if(env('EMPLOYEE_SECONDS')){
            $validate_seconds = env('EMPLOYEE_SECONDS');
        }
        $validate_seconds = time() - $validate_seconds;
        $validate_date = date('Y-m-d H:i:s',$validate_seconds);
        $where = [
            ['user_id','=',$user_id],
            ['created_at','>',$validate_date],
            ['ordered_flag','=','0'],
        ];
        \Log::info('processGuid where'. json_encode($where));
        $employee = Employee::where($where)->orderBy('created_at','ASC')->orderBy('id','ASC')->get()->toArray();
        if(!$employee){
            \Log::info('没有导购');
            return ['code' =>0 ,'data' =>[]];
        }
        //查询导购信息
        $info = EmployeeCode::where('id', $employee[0]['empid'])->select('guide_code','store_code')->first();
        return ['code' =>1 ,'data'=>$info];
    }

    public  function getMemberGuide($user_id)
    {
        $validate_seconds = (int) $this->validate_days * 60*60 * 24 ; 
        if(env('EMPLOYEE_SECONDS')){
            $validate_seconds = env('EMPLOYEE_SECONDS');
        }
        $validate_seconds = time() - $validate_seconds;
        $validate_date = date('Y-m-d H:i:s',$validate_seconds);
        $where = [
            ['user_id','=',$user_id],
            ['created_at','>',$validate_date],
            ['ordered_flag','=','0'],
        ];
        \Log::info('MemberGuide where'. json_encode($where));
        $employee = EmployeeUserShare::where($where)->orderBy('created_at','ASC')->orderBy('id','ASC')->get()->toArray();
        if(!$employee){
            \Log::info('没有导购');
            return ['code' =>0 ,'data' =>[]];
        }
        //查询会员id信息
        $info = EmployeeMember::where('id', $employee[0]['empid'])->select('empid')->first();
        return ['code' =>1 ,'data'=>$info];
    }

    /**
     * 获取导购信息
     */
    public static function getGuidInfo($phone= '')
    {
        $sa_list = \DB::table('sa_list')->where(['phone'=>$phone,'status'=>1])->select(['sid as guide_code','store_id as store_code','name'])->first()?? [];
        return $sa_list;
    }


    /**
     * todo:根据where条件获取全部店员（默认全部）
     * @param int $role_id
     * @param array $field
     */
    public static function getGuide($where = [], $field = ['id', 'really_name'])
    {
        $guideModel = new Employee();
        if ($where) {
            $guideModel = $guideModel->where($where);
        }
        $guides = $guideModel->get($field)->toArray();

        return $guides ? $guides : [];
    }


}

