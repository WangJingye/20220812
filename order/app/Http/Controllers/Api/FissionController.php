<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\{FissionServices};
use App\Exceptions\ApiPlaintextException;
use Illuminate\Support\Facades\DB;

class FissionController extends ApiController
{   protected $_model = Fission::class;
    protected $cartServices;
    protected $from;

    /**
     * CartController constructor.
     * @throws ApiPlaintextException
     */
    public function __construct(){
        $this->fissionServices = new FissionServices;
    }

    /**
     * [dataList description]
     * @Author   Peien
     * @DateTime 2020-07-30T12:15:56+0800
     * @return   [type]                   [description]
     */
    function dataList(Request $request)
    {
        $name = request('name');
        $status =request('status');
        $limit = request('limit', 10);
        $page = request('page');
        $model = DB::table('fission');
        if($name)
        {
            $model->where('display_name','like', '%'.$name.'%');
        }
        if(isset($status) && $status != "")
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
            $new['name']     = $item->name;
            $new['start_time']     = $item->start_time;
            $new['end_time']     = $item->end_time;
            $new['condition_value']     = $item->condition_value;
            $new['max_num']     = $item->max_num;
            $new['step']     = $item->step;
            $new['value_id']     = $item->value_id;
            $new['type']     = $item->type== 1 ? '优惠码': '优惠卷';
            $new_item[] = $new;
        }
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $new_item;
        
        return $response;
    }

    /**
     * [add 增加裂变]
     * @Author   Peien
     * @DateTime 2020-07-30T17:32:06+0800
     * @param    Request                  $request [description]
     */
    function add(Request $request)
    {
        $info = $request->all();
        $params =
        [
            'name' => $info['name']?? '',//'裂变名称',
            'start_time' => $info['start_time'] ?? '',//'开始时间',
            'end_time' => $info['end_time'] ?? '',//'结束时间',
            'status' => 1,//'1:未激活，2：激活,3:禁用',
            'created_at' => date('Y-m-d H:i:s'),
            'condition_value' => $info['condition_value'] ?? 1,// '条件值',
            'max_num' => $info['max_num']?? 1,// '赠送上线',
            'step' => $info['step']?? 1,// '是否多增   1赠1个      2多赠',
            'value_id' => $info['value_id']?? '',// '优惠券/优惠码id',
            'type' => $info['type'] ?? 0,// '类型   1   优惠码  2   优惠卷',
        ];
        if(isset($info['id']))
        {
            $res = \DB::table('fission')->where('id', $info['id'])->update($params);
            $response['code'] = 1;
            $response['msg'] = "修改成功";

        }
        else
        {
            $res = \DB::table('fission')->insertGetId($params);
            $response['code'] = 1;
            $response['msg'] = "新增成功";  
        }
        
        return json_encode($response);
    }


    /**
     * [detail 裂变详情]
     * @Author   Peien
     * @DateTime 2020-07-30T17:32:24+0800
     * @param    Request                  $request [description]
     * @return   [type]                            [description]
     */
    function detail(Request $request)
    {
        $info = $request->all();
        $model = DB::table('fission');
        if($info['id']){

            $model->where('id', $info['id']);
        }
        $res = $model->get()->toArray();
        $response['code'] = 1;
        $response['msg'] = '查询成功';
        $response['data'] = $res;
        return json_encode($response);
    }

    public function edit(Request $request)
    {
    }
    public function registerAdd(Request $request)
    {
        $params = $request->all();
        //插入表
        $result = $this->fissionServices->buildRelation($params);
        return $result;
    }

    public function getFissionRank()
    {
        $result =  $this->fissionServices::getFissionRank();
        $response['code'] = 1;
        $response['msg'] = '查询成功';
        $response['count'] = 10;
        $response['data'] = $result ?? [];
        return json_encode($response);
    }


    /**
     * [active 激活/禁用]
     * @Author   Peien
     * @DateTime 2020-08-02T17:36:47+0800
     * @return   [type]                   [description]
     */
    public  function active(Request $request)
    {
        //同一时期内不能有两个裂变活动
        if(request('status') ==2)
        {   
            //查询当前的有效的裂变活动
            $curr_time = date('Y-m-d H:i:s');
            //查询maxNum 
            $where = [
                ['status','=',2,],['start_time','<',$curr_time],['end_time','>',$curr_time], ['id', '<>',request('id')]//激活，有效的
            ];
            $count = DB::table('fission')->where($where)->count();
            if($count)
            {
                $response['code'] = 0;
                $response['msg'] = '同一时间内不能有两个裂变活动';
                return json_encode($response);
            }
        }
        $result = DB::table('fission')->where('id', request('id'))->update(['status'=>request('status')]);

        $response['code'] = 1;
        $response['msg'] = '激活成功';
        $response['data'] = $result ?? [];
        return json_encode($response);
    }

}
