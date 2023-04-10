<?php namespace App\Service\Dlc;

use App\Model\{Users,SaList,EmployeeMember,EmployeeCode};
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Bus\Dispatcher;

class GuideService
{
    /**
     * 绑定导购与sku的关系 并返回前端需要的参数
     * @param $sid
     * @param $sku
     * @return array
     */
    public static function bindGuide($sid,$sku){
        $sa_info = SaList::query()->where(['sid'=>$sid,'status'=>1])->first();
        $date = date('Y-m-d,H:i:s');
        if($sa_info){
            $id = EmployeeCode::query()->insertGetId([
                'guide_code' => $sid,
                'sku_id'     => $sku,
                'created_at'=>$date,
                'updated_at'=>$date,
            ]);
            $return['e'] = $id;
        }else{
            $id = EmployeeMember::query()->insertGetId([
                'empid' => $sid,
                'sku_id'     => $sku,
                'created_at'=>$date,
                'updated_at'=>$date,
            ]);
            $return['u'] = $id;
        }
        return $return;
    }


}