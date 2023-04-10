<?php
namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{

    protected $_model = '';

    function __construct()
    {
        header('Access-Control-Allow-Origin:*');
    }

    function success($data, $message = "ok")
    {
        return [
            'code' => 1,
            'data' => $data
        ];
    }

    function error($message = "fail")
    {
        return [
            'code' => 0,
            'error' => [
                'code' => 500,
                'type' => 'NORMAL',
                'message' => 'fail'
            ]
        ];
    }

    protected function addFilter($model)
    {
        return $model;
    }

    function dataList()
    {
        // return ['数据库33344：'=>env('DB_DATABASE')];
        $model = $this->getModel();
        $table = $model->getTable();
        
        $name = request('name');
        $limit = request('limit', 10);
        $page = request('page');
        
        if (method_exists($model, 'addTable')) {
            $model = $model->addTable();
        } else {
            $model = $model->newQuery();
        }
        
        $model = $this->addFilter($model);
        
        if (method_exists($model->getModel(), 'addField')) {
            $model = $model->getModel()->addField($model);
        }
        // return [get_class($model),get_class_methods($model)];
        $sql = $model->orderBy($table . '.id', 'desc')->toSql();
        $res = $model->orderBy($table . '.id', 'desc')
            ->paginate($limit)
            ->toArray();
        
        if (env('APP_DEBUG')) {
            $response['sql'] = $sql;
        }
        
        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $res['data'];
        
        return $response;
    }

    function get()
    {
        $id = request('id');
        $item = $this->getModel()->find($id);
        if ($item) {
            return $this->success($item, '获取成功');
        } else {
            return $this->error('获取失败');
        }
    }

    function post()
    {
        try {
            $id = request()->input('id');
            ;
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);
                ;
            }
            return [
                'code' => 1,
                'msg' => '编辑成功',
                'data'=>$model->toArray()
            ];
        } catch (\Exception $e) {
            return ([
                'code' => 0,
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = request('id');
            if ($id) {
                $this->getModel()->destroy($id);
            }
            return $this->success([], '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function export()
    {
        $list = $this->getModel()->get();
        return $this->success($list, '导出成功');
    }

    protected function getModel()
    {
        return new $this->_model();
    }
}
