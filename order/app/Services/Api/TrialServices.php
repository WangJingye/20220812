<?php namespace App\Services\Api;

use Illuminate\Support\Facades\Redis;
use App\Exceptions\ApiPlaintextException;
use Illuminate\Support\Facades\DB;
use App\Model\FreeTrial;

class TrialServices
{
    public function __construct()
    {

    }
    public static function getList($params =[])
    {
        $id = $params['id'] ?? 0;
        $curr_time = date('Y-m-d H:i:s');
        $data = FreeTrial::getAllCacheData();
        $collect_data = collect($data)->where('status',2)
            ->where('start_time','<',$curr_time)
            ->where('end_time','>',$curr_time);
        if($id){
            $result = $collect_data->where('id', $id)->toArray();
        }
        else{
            $result = $collect_data->toArray();
        }
        if(!$result)return ['code'=>0,'msg' => '无付邮试用活动'];
        return ['code'=>1, 'msg' => '成功返回', 'data'=>array_values($result)];
    }

    public static function getGoodsList($params)
    {
        $trialId= $params['id'];

        $curr_time = date('Y-m-d H:i:s');
        $where = [
            ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time],['id', '=', $trialId]//激活，有效的
        ];

        $result = DB::table('free_trial')->where($where)->first();
        if(!$result)return ['code'=>0,'msg' => '无付邮试用活动'];
        $api = app('ApiRequestInner',['module'=>'goods']);
        $goodsList = $api->request('outward/product/getProductInfoBySkuIds','GET',['sku_ids'=>$result->add_sku]);
        $list = array_values($goodsList['data']);
        $resultArray['goodsList'] = $list;
        $resultArray['count'] = count($list);
        $resultArray['name'] = $result->display_name;
        $resultArray['limited_qty'] = $result->limited_qty;
        $resultArray['money'] = $result->money;


        return ['code'=>1, 'msg'=> '成功', 'data'=> $resultArray];
    }









}
