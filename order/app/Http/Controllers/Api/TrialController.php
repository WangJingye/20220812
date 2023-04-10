<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\{TrialServices};
use App\Exceptions\ApiPlaintextException;
use Illuminate\Support\Facades\DB;
use App\Model\FreeTrial;

class TrialController extends ApiController
{
    /**
     * [ShipFreeList 付邮列表]
     * @Author   Peien
     * @DateTime 2020-07-23T16:36:18+0800
     */
    public function list(Request $request)
    {
    	return TrialServices::getList($request->all());
    }

    /**
     * [goodsList 付邮活动商品列表]
     * @Author   Peien
     * @DateTime 2020-08-01T11:58:15+0800
     * @param    Request                  $request [description]
     * @return   [type]                            [description]
     */
    public function goodsList(Request $request)
    {
    	return TrialServices::getGoodsList($request->all());
    }

    public function dataList()
    {
    	$name = request('name');
        $status =request('status');
        $limit = request('limit', 10);
        $page = request('page');
        $model = DB::table('free_trial');
        if($name)
        {
            $model->where('display_name','like', '%'.$name.'%');
        }
        if($status)
        {
            $model->where('status', $status);
        }
        $res = $model->orderBy('id','desc')->paginate($limit)->toArray();
        $new_item = [];
        foreach($res['data'] as $item){
            //$type = $item['type'];
            $new['datetime_period'] = $item->start_time .'-' . $item->end_time;
            //$item['type_label'] = $promotion_type[$type]??'';
            $status = '待激活';
            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="active">激活</a>';
            if($item->status ==2){
                $status = '激活';
                $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="unactive">禁用</a>';
            }
            if(time()>strtotime($item->end_time) ){//过期了
                $status = '禁用';
                $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a>';
            }
            if($item->status == 3){//禁用
                $status = '禁用';
                $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a>';
            }
            $new['status'] = $status;
            $new['action'] = $action;
            $new['id']     = $item->id;
            $new['name']     = $item->display_name;
            $new['start_time']     = $item->start_time;
            $new['end_time']     = $item->end_time;
            $new['limited_qty']     = $item->limited_qty;
            $new['add_sku']     = $item->add_sku;
            $new['money']     = $item->money;
            //$new['max_num']     = $item->max_num;
            //$new['step']     = $item->step;
            //$new['value_id']     = $item->value_id;
            //$new['type']     = $item->type== 1 ? '赠送优惠卷': '赠送优惠码';
            $new_item[] = $new;
        }
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $new_item;
        return $response;
    }

    public function detail()
    {
    	$id = request('id');
    	$res = get_object_vars(DB::table('free_trial')->where('id', $id)->first());
    	return ['code' => 0, 'data' => $res];
    }

    public function add()
    {
        $params = [
            'display_name' =>request('display_name'),
            'money' =>request('money'),
            'add_sku' =>preg_replace("/(\n)|(\s)|(\t)|(\')|(')|(，)|(\.)/",',',request('add_sku')),
            'limited_qty' =>request('limited_qty'),
            'start_time' =>request('start_time'),
            'end_time' =>request('end_time'),
            'image' =>request('image'),
            'status' =>1,
            'created_at' =>date('Y-m-d H:i:s')
        ];
        if(request('id'))
        {

            $result = DB::table('free_trial')->where('id', request('id'))->update($params);
        }else
        {
            $result = DB::table('free_trial')->insertGetId($params);
        }
        //后台更新后刷新缓存
        FreeTrial::cacheAllData();
        return ['code' => 1,'data'=>$result];
    }

    /**
     * [active 激活/禁用]
     * @Author   Peien
     * @DateTime 2020-08-02T17:36:47+0800
     * @return   [type]                   [description]
     */
    public  function active()
    {
        $result = DB::table('free_trial')->where('id', request('id'))->update(['status'=>request('status')]);
        //后台更新后刷新缓存
        FreeTrial::cacheAllData();
        return ['code' => 1,'data'=>$result];
    }

}
