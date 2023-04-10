<?php

namespace App\Http\Controllers\Api\Advert;

use Illuminate\Http\Request;
use App\Model\Employee;
use App\Model\EmployeeCode;
use App\Model\EmployeeMember;
use App\Model\EmployeeUserShare;
use App\Service\Guide\EmployeeService;
use App\Http\Controllers\Controller;

class EmployeeController extends Controller
{
    public function __construct(Request $request)
    {

        parent::__construct();
        if (!$this->user_id) {
            return $this->error("未登陆");
        }
    }
    public function testGuid(Request $request){
//         (new PayService())->processGuide(260);
    }
   
    public function recordEmpId(Request $request){
        $params = request()->all();
        $store_code =0;
        if(isset($params['e']))
        {
            $store_code = 1;
            $empId = $params['e'];
            $empInfo = EmployeeCode::where('id',$empId)->get()->toArray();
        }
        else
        {
            $empId = $params['u'];
            $empInfo = EmployeeMember::where('id',$empId)->get()->toArray();
        }
        if(empty($empInfo))
        {
            \Log::info('导购不存在');
            return $this->success([],'success'); 
        }
        $validate_seconds = (int) 7 * 60*60 * 24 ; 
        if(env('EMPLOYEE_SECONDS')){
            $validate_seconds = env('EMPLOYEE_SECONDS');
        }
        $validate_seconds = time() - $validate_seconds;
        if($validate_seconds > (int)strtotime($empInfo[0]['created_at']))
        {
            \Log::info('导购已过期');
            return $this->success([],'success');
        }
        
        $employee_data = [
                'empid'=>$empId,
                'user_id' => $this->user_id ?? 1,
                'store_code'=>$store_code,
                'created_at'=> $empInfo[0]['created_at'],
            ];
        if($store_code > 0)
        {
            //导购
            Employee::create($employee_data); 
        }else
        {
            //普通用户
            EmployeeUserShare::create($employee_data);
        }
        return $this->success([],'success');
    }

    private function decodeRealWechatId($encryptString){
        try {
            $decryptedData = decrypt($encryptString);
            $wechat_id =  $decryptedData['wechatUserId'] ?? '';
        } catch (\Exception $e) {
            return '';
        }
        return $wechat_id;
    }

}
