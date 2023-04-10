<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Api\Controller;
use App\Model\Promotion\Shipfee;


class ShipfeeController extends Controller
{
    public function list(Request $request)
    {
    	$province = $request->get('province');
        $limit = request('limit', 10);
        $model = Shipfee::query();
        if($province){
            $model->where('province','like', '%'.$province.'%');
        }
        $res = $model->where('province','<>',Shipfee::$default)->orderBy('id','asc')->paginate($limit)->toArray();
        $new_item = [];
        foreach($res['data'] as $item){
            $new['id']     = $item['id'];
            $new['province']     = $item['province'];
            $new['ship_fee']     = $item['ship_fee']?:0;
            $new['free_limit']     = $item['free_limit']?:0;
            $new['is_free']     = $item['is_free']?'是':'否';

            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a>';
            $new['action'] = $action;
            $new_item[] = $new;
        }
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $new_item;
        return $response;
    }

    public function detail(Request $request)
    {
        $id = $request->get('id');
        $is_default = $request->get('is_default');
        if($is_default==1){
            $res = Shipfee::query()->where('province', Shipfee::$default)->first();
        }elseif($id){
            $res = Shipfee::query()->where('id', $id)->first();
        }
    	return ['code' => 1, 'data' => $res??[]];
    }

    public function update(Request $request)
    {
        $data = [
            'province'=>$request->get('province'),
            'ship_fee'=>$request->get('ship_fee'),
            'free_limit'=>$request->get('free_limit'),
            'is_free'=>$request->get('is_free'),
        ];
        $is_default = $request->get('is_default');
        $id = $request->get('id');
        if($is_default==1){
            //默认配置
            $default = Shipfee::query()->where('province',Shipfee::$default)->count();
            if($default){
                unset($data['province']);
                $result = Shipfee::query()->where('province',Shipfee::$default)->update($data);
            }else{
                $data['province'] = Shipfee::$default;
                $result = Shipfee::query()->insertGetId($data);
            }
        }elseif($id){
            $has = Shipfee::query()
                ->where('province',$data['province'])
                ->where('id','<>', $request->get('id'))
                ->count();
            if($has){
                return ['code' => 0,'message'=>'省已存在'];
            }
            $result = Shipfee::query()->where('id', $request->get('id'))->update($data);
        }else{
            $has = Shipfee::query()->where('province',$data['province'])->count();
            if($has){
                return ['code' => 0,'message'=>'省已存在'];
            }
            $result = Shipfee::query()->insertGetId($data);
        }
        //刷新缓存
        Shipfee::cacheclean();
        return ['code' => 1,'data'=>$result];
    }

    public function del(Request $request)
    {
        $id = $request->get('id');
        $shipfee = Shipfee::query()->find($id)->first();
        if($shipfee){
            $province = $shipfee->province;
            $shipfee->delete();
            Redis::hdel(Shipfee::CacheKey,$province);
        }
        return ['code' => 1];
    }

}
