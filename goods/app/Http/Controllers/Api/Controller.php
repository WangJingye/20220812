<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use App\Support\Token;

class Controller extends BaseController
{
    protected $_model = '';
    public function success($data, $message = 'ok')
    {
        return [
            'code' => 1,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function error($code = 0, $message = 'fail', $errorData = [])
    {
        return [
            'code' => $code,
            'message' => $this->translate($code) ?: $message,
            'error' => $errorData,
        ];
    }

    public function translate($code = 0, $message = 'fail')
    {
        $mapping = [
            '2001' => '产品库存系统异常，请重试',
            '2002' => '产品库存不足',
            '2003' => '产品已下架',
            '2004' => '请输入正确的价格筛选区间',
            '2005' => '分类迷路了~',
            '2006' => '产品迷路了~',
            '2007' => '门店产品迷路了~',
        ];

        return isset($mapping[$code]) ? $mapping[$code] : '';
    }

    protected function addFilter($model)
    {
        return $model;
    }

    public function dataList()
    {
        //return ['数据库33344：'=>env('DB_DATABASE')];

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
//         return [get_class($model),get_class_methods($model)];
        $sql = $model->orderBy($table.'.id', 'desc')->toSql();
        $res = $model->orderBy($table.'.id', 'desc')->paginate($limit)->toArray();

        if (env('APP_DEBUG')) {
            $response['sql'] = $sql;
        }

        $response['code'] = 0;
        $response['msg'] = '获取规则列表成功.';
        $response['count'] = $res['total'];
        $response['data'] = $res['data'];

        return $response;
    }

    public function get()
    {
        $id = request('id');
        $item = $this->getModel()->find($id);
        if ($item) {
            return $this->success($item, '获取成功');
        } else {
            return $this->error('获取失败');
        }
    }

    public function post()
    {
        try {
            $id = request()->input('id');
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);
            }

            return ['code' => 0, 'msg' => '编辑成功'];
        } catch (\Exception $e) {
            return ['code' => -1, 'msg' => $e->getMessage()];
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

    /**
     * @return mixed
     */
    public function getUid(){
        $token = app('request')->header('token')??'';
        if($token){
            $uid = Token::getUidByToken($token);
            if($uid){
                return $uid;
            }
        }return false;
    }
}
