<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Api\Controller;
use App\Model\Promotion\Log;

class LogController extends Controller
{
    protected $_model = Log::class;
   
    function dataList()
    {
        $model = $this->getModel();
        $table = $model->getTable();
        
        $ruleId = request('ruleId')??'';
        $giftId = request('giftId')??'';
        $limit = request('limit', 10);
        $page = request('page');
        if($ruleId){
            $model = $model->where('ruleId',$ruleId);
        }
        if($giftId){
            $model = $model->where('giftId',$giftId);
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
}
