<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Api\Controller;
use App\Model\Promotion\Gift;
use App\Model\Promotion\Log;

class GiftController extends Controller
{
    protected $_model = Gift::class;
   
    
    public function active(){
        $id = (int) request('id');
        $where = [
            ['id','=',$id],
            ['status','<>','3']
        ];
        Gift::where($where)->update(['status'=>2]);
        $this->logPromotion('激活');
        return ['code'=>1];
    }
    public function unactive(){
        $id = (int) request('id');
        $where = ['id'=>$id];
        Gift::where($where)->update(['status'=>3]);
        $this->logPromotion('禁用');
        return ['code'=>1];
    }
    private function logPromotion($actionType=''){
        $id = (int) request('id');
        $userId = (int) request('userId')??'';
        $userEmail  = request('userEmail')??'';
        $data = [
            'giftId'=>$id,
            'userId'=>$userId,
            'userEmail'=>$userEmail,
            'actionType'=>$actionType,
        ];
        Log::create($data);
    }
	
    function dataList()
    {
        $model = $this->getModel();
        $table = $model->getTable();
        
        $name = request('name');
        $limit = request('limit', 10);
        $page = request('page');
        if($name){
            $model = $model->where('name','like','%'.$name.'%');
        }
        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        
        $model = $this->addFilter($model);
        
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        $res = $model->orderBy($table . '.id', 'desc')
        ->paginate($limit)
        ->toArray();
        $new_item = [];
        foreach($res['data'] as $item){
            $status = '待激活';
            $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="edit">编辑</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="active">激活</a>';
            if($item['status'] ==2){
                $status = '激活';
                $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a><a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="unactive">禁用</a>';
            }
            if($item['status'] == 3){//禁用
                $status = '已禁用';
                $action = '<a class="layui-btn layui-btn-primary layui-btn-xs" lay-event="view">查看</a>';
            }
            $item['status'] = $status;
            $item['action'] = $action;
            $new_item[] = $item;
        }
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $new_item;
        
        return $response;
    }
    
}
