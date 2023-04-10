<?php

namespace App\Http\Controllers\Api\Promotion;

use App\Http\Controllers\Api\Promotion\CartController;
use App\Model\Promotion\Code;

class CodeController extends CartController
{
//     protected $_model = Coupon::class;
	   
    protected  function addFilter($model){
        $label = request('name');
        if($label){
            $model = $model->where('name','like','%'.$label.'%');
        }
        $model = $model->whereIn('type',['code_full_reduction_of_order',
            'code_order_n_discount',
            'code_n_piece_n_discount',
            'code_gift',
            'code_product_discount',
        ]);
        return $model;
    }
    
    public function post()
    {
        try {
            $id = request()->input('id');
            $length= request('code_length');
            $coupon=new Code();
            $model = $this->getModel();
            $data = request()->only($model->getTableColumns());
            $cats = implode(',',array_keys(request('cats')));
            $addrule = implode(',',array_keys(request('addrule')));
            $data['cids'] = $cats;
            $data['addrules'] = $addrule;
            $result=$coupon->generatePool($length,1);
            $code_code = $result[0];
            $data['code_code'] = $code_code;
            if ($id) {
                $model->find($id)->update($data);
            } else {
                $model = $model::create($data);
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
    
    function dataList()
    {
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
