<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Model\Ad\Location;
use App\Model\Ad\Item;

class LocationController extends Controller
{

    /**
     * SKU列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;
        $query = new Location();
        if ($request->loc_id) {
            $query = $query->where('id',$request->loc_id);
        }
        if ($title = $request->title) {
//            $locs = explode(',',$locs);
            $query = $query->where('title',$title);
        }

        $deProdData = $query->orderBy('id','desc')->paginate($limit)->toArray();
        $return = [];
        $data = $deProdData['data'];
        foreach($data as &$record){
            $record['start_time'] = date("Y-m-d H:i:s",$record['start_time']);
            $record['end_time'] = date("Y-m-d H:i:s",$record['end_time']);
        }
        $return['pageData'] =  $data ;
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    /**
     * 获取SKU.
     */
    public function getLoc(Request $request)
    {
        $id = $request->id;
        if(!$id) return $this->success("参数缺失");
        $query = new Location();
        $record = $query->where('id',$id)->first()->toArray();
        if($record){
            $record['start_time'] = date('Y-m-d H:i:s',$record['start_time']);
            $record['end_time'] = date('Y-m-d H:i:s',$record['end_time']);
        }

        return $this->success($record??[]);
    }

    public function insert(Request $request){
        $all = $request->all();

        $fields = [
            'title' => 'required',
            'start_time' => 'required',
            'end_time'  => 'required'
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

        $data = [
            'title'=>$all['title'],
            'start_time'=>strtotime($all['start_time']),
            'end_time'=>strtotime($all['end_time']),
            'remark'=>$all['remark']??"",
            'userid'=>$all['userid']??0,
        ];
//        try{
            $id = Location::insertGetId($data);
            if($id) return $this->success("创建商品集合成功");
            return $this->error("新增失败");
//        }catch (\Exception $e){
//            return $this->error("新增失败了");
//        }





//        if(!$children) $this->error("参数缺失");
//        $records = $this->_checkCollectionChildren($children);
//        if(!$records) $this->error("参数校验失败");
//
//        $insertData['colle_name'] = $request->colle_name;
//        $insertData['colle_desc'] = $request->colle_desc;
//
//        $insertId = Collection::insertGetId(
//            $insertData
//        );
//        if(!$insertId) return $this->error("创建商品集合失败");
//        $records = array_values($records);
//        $res = CollectionRelation::insertCollectionRelation($insertId,$records);
//        if(!$res) $this->error("创建商品关联失败");
//        return $this->success("创建商品集合成功");
    }

    public function update(Request $request){
        $all = $request->all();

        $fields = [
            'id' => 'required',
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

        foreach($all as $k=>$v){
            if(in_array($k,Location::$fields)){
                if(in_array($k,['start_time','end_time'])) $v = strtotime($v);
                $upData[$k] = $v;
            }
        }

//        $data = [
//            'title'=>$all['title'],
//            'start_time'=>strtotime($all['start_time']),
//            'end_time'=>strtotime($all['end_time']),
//            'remark'=>$all['remark']??"",
//        ];
//        try{
            $upNum = Location::updateById($all['id'],$upData);
            if($upNum) return $this->success("更新成功");
            return $this->error("更新失败");
//        }catch (\Exception $e){
//            return $this->error("更新失败了");
//        }
    }
}
