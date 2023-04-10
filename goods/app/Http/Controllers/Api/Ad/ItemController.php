<?php

namespace App\Http\Controllers\Api\Ad;

use App\Http\Controllers\Api\Controller;

use App\Service\Goods\AdService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Ad\Item;

use Illuminate\Support\Facades\Validator;
use App\Model\Ad\Location;


class ItemController extends Controller
{
    protected $_model = Item::class;

    /**
     * SKU列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = 1000;
        $loc_id = $request->loc_id;
        $loc = $request->loc;
        if(!$loc_id && $loc){
            $loc_info = Location::where('title',$loc)->first();
            if($loc_info) $loc_id = $loc_info['id'];
        }

        if(!$loc_id) return $this->error("参数缺失");
        $query = new Item();
        $query = $query->where('loc_id',$loc_id);
        if ($loc_ids = $request->loc_ids) {
            $loc_ids = explode(',',$loc_ids);
            $query = $query->whereIn('loc_id',$loc_ids);
        }

        $deProdData = $query->orderBy('asort','desc')->orderBy('update_stamp','desc')->paginate($limit)->toArray();
        $return = [];
        $data = $deProdData['data'];
        foreach($data as &$record){
            $record['start_time'] = date("Y-m-d H:i:s",$record['start_time']);
            $record['end_time'] = date("Y-m-d H:i:s",$record['end_time']);
        }
        $return['pageData'] =  $data ;
        $return['loc_id'] =  $loc_id ;
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    public function insert(Request $request){
        $all = $request->all();

        $fields = [
            'loc_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }
        $loc_id = $all['loc_id'];
        $loc = Location::where('id',$loc_id)->first();
        if(!$loc){
            return $this->error("标示位不存在");
        }

        $data = [
            'loc_id'=>$loc_id,
            'start_time'=>$loc['start_time'],
            'end_time'=>$loc['end_time'],
            'userid'=>$loc['userid']??0,
        ];
        $id = Item::insertGetId($data);
        if($id) return $this->success([
            'id'=>$id,
            'start_time'=>date('Y-m-d H:i:s',$loc['start_time']),
            'end_time'=>date('Y-m-d H:i:s',$loc['end_time'])
        ]);
        return $this->error("新增失败了");
    }

    /**
     * 修改产品上下架状态.
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $loc_id = $request->loc_id??0;
        $allData = $request->all();
        $upData = [];
        foreach($allData as $k=>$v){
            if(in_array($k,Item::$fields)){
                if(in_array($k,['start_time','end_time'])) $v = strtotime($v);
                $upData[$k] = $v;
            }
        }
        $upNum = Item::updateById($id,$upData);
        if($upNum && $loc_id){
            $adService = new AdService();
            $adService->cacheLocAds($loc_id);
        }
        return $this->success([]);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $loc_id = $request->loc_id??0;
        $result = Item::deleteById($id);
        if($result){
            $adService = new AdService();
            $adService->cacheLocAds($loc_id);
        }
        return $this->success([]);
    }
}
