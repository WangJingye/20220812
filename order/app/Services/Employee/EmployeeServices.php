<?php
namespace App\Service;

use App\Model\Employee;
use Illuminate\Support\Facades\Log;
use App\Model\OrderGoods;

//导购分享
class EmployeeService
{
    public $validate_days = 30; 
    
    
    //处理导购分享
    public function processGuid($order){
        try {
            $this->getGuid($order);
        } catch (\Exception $e) {
            Log::info("processGuide Fail:".$e->getMessage());
        }
    }
    
    //获取是否有导购
    public function getGuid($order){
        $wechat_id = $order['wechat_id'];
        $validate_seconds = (int) $this->validate_days * 60*60 * 24 ; 
        if(env('EMPLOYEE_SECONDS')){
            $validate_seconds = env('EMPLOYEE_SECONDS');
        }
        $validate_seconds = time() - $validate_seconds;
        $validate_date = date('Y-m-d H:i:s',$validate_seconds);
        $where = [
            ['wechat_id','=',$wechat_id],
            ['created_at','>',$validate_date],
            ['ordered_flag','=','0'],
        ];
        \Log::info('processGuid where'. json_encode($where));
        $employee = Employee::where($where)->orderBy('created_at','desc')->orderBy('id','desc')->get()->toArray();
        if(!$employee){
            \Log::info('没有导购');
            return ; //没有导购
        }
        foreach ($order['goods'] as $key => $value) {
            $pdt_id[$value['pdt_id']]['pdt_id'][] =$value['pdt_id'];
            $pdt_id[$value['pdt_id']]['id'][] =$value['id'];
        }
        $pdt_id = array_values($pdt_id);
         \Log::info('导购订单信息'.json_encode($order));
        foreach ($pdt_id as $key1 => $value1) {
        $latest_employee = $this->getLatestEmployee($employee, $value1['pdt_id'][0]);
        if($latest_employee){//这个spu有导购
                $order_goods_id =implode(',', $value1['id']);
                Employee::where('id',$latest_employee['id'])->update(['ordered_flag'=>1,'order_goods_id'=>$order_goods_id]);//清除导购关系
                OrderGoods::whereIn('id',$value1['id'])->update(['guide'=>$latest_employee['empid'],'store_code'=>$latest_employee['store_code']]);//更新订单行是有导购的
            }  
        }
    }
    //取最近的一个导购
    public function getLatestEmployee($employee,$spu){
        $return = '';
        foreach($employee as $item){
            if($item['spuid'] == $spu ){//取created_at时间倒序第一个，是最近的
                $return = $item;
                break;
            }
        }
        return $return;
    }
    
    
    
}

