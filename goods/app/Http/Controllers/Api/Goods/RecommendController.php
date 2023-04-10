<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Recommend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecommendController extends Controller
{

    public function list(Request $request){
        $limit = $request->limit ?: 10;
        $model = new Recommend();
        $model = $model->orderBy('id','desc');    //删除商品不展示

//        if(!empty($params['colle_name']))
//            $model = $model->where('colle_name',  'like', '%' . $params['colle_name'] . '%');
//        if(!empty($params['status']))
//            $model = $model->where('status', $params['status']);

        $deProdData = $model->paginate($limit)->toArray();

        $return = [];
        $return['pageData'] = $deProdData['data'];
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    public function addRec(Request $request){

        $fields = [
            'cat_id' => 'required',
            'flag' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
//            'string'     => ':attribute 为字符串',
//            'min'       => ':attribute至少:min位字符长度'
            ]
        );
        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $insertData['cat_id'] = $request->cat_id;
        $insertData['flag'] = $request->flag;
        $insertData['rec_desc'] = $request->rec_desc;

        $insertId = Recommend::insertGetId(
            $insertData
        );
        if(!$insertId) return $this->error("创建商品集合失败");
        return $this->success("创建商品集合成功");
    }

    /**
     * 修改产品上下架状态.
     */
    public function changeStatus(Request $request)
    {
        $id = $request->id;
        $allData = $request->all();
        $upData = [];
        foreach($allData as $k=>$v){
            if(in_array($k,Recommend::$fields)){
                $upData[$k] = $v;
            }
        }

        $upNum = Recommend::updateById($id,$upData);
//        if($upNum){
//            $pService = new ProductService();
//            $res = $pService->updateColletioCache($colleId,$upData);
//        }
        if(empty($upNum)){
            return $this->error(0,'更新失败');
        }else{
            return $this->success([]);
        }
    }

}
