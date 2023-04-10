<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Collection;
use App\Model\Goods\CollectionDetail;
use App\Model\Goods\CollectionRelation;
use App\Model\Goods\ProductCat;
use App\Model\Goods\RedisModel;
use App\Model\Goods\WechatHelp;
use App\Service\Goods\ProductService;
use function Couchbase\passthruEncoder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\Sku;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{

    public function list(Request $request){
        $limit = $request->limit ?: 10;
        $page = $request->page ?: 1;
//        $deProdData = Collection::paginate($limit)->toArray();
        $params = $request->all();
        $model = new Collection();
        $model = $model->where('status','!=',-1)->orderBy('id','desc');    //删除商品不展示

        if(!empty($params['colle_name']))
            $model = $model->where('colle_name',  'like', '%' . $params['colle_name'] . '%');
        if(!is_null($request->status) && ($request->status !== '') ){
            $model = $model->where('status', $request->status);
        }

        $deProdData = $model->paginate($limit)->toArray();
        $data = $deProdData['data']??[];
        foreach($data as $k=>$one){
            $pService = new ProductService();
            $data[$k] = $pService->formatCollection($one);
        }

        $return = [];
        $return['pageData'] = $data;
        $return['count'] = $deProdData['total'];

        return $this->success($return);
    }

    public function getFormatedProductList(Request $request){
        $res = $this->list($request);
        if(!empty($res['data']['pageData'])){
            $pService = new ProductService();
            foreach($res['data']['pageData'] as $k=>$one){
                $res['data']['pageData'][$k] = $pService->formatCollection($one);
            }
        }
        return $res;
    }

    public function add(Request $request){
        $children = $request->get("chunks");
        $all = $request->all();

        if(!$children) return $this->error("参数缺失");
        $records = $this->_checkCollectionChildren($children);
        if(!$records) return $this->error("参数校验失败");

        $insertData['colle_name'] = $request->colle_name;
        $insertData['rec_cat_id'] = $request->rec_cat_id??'';
        $insertData['colle_id'] = $request->colle_id??'';
        $insertData['colle_desc'] = $request->colle_desc;
        $insertData['can_search'] = $request->can_search??0;
        $insertData['display_start_time'] = !empty($all['display_start_time'])?strtotime($all['display_start_time']):0;
        $insertData['display_end_time'] = !empty($all['display_end_time'])?strtotime($all['display_end_time']):0;
        $insertData['short_colle_desc'] = $request->short_colle_desc;

        $insertId = Collection::insertGetId(
            $insertData
        );
        if(!$insertId) return $this->error(0,"创建商品集合失败");
        $records = array_values($records);
        $res = CollectionRelation::insertCollectionRelation($insertId,$records);
        if(!$res) $this->error(0,"创建商品关联失败");
        return $this->success("创建商品集合成功");
    }

    public function detail(Request $request){
        $id = $request->id;
        $res = Collection::getCollectionInfoById($id);
        $productCats = ProductCat::getProductCatsByPidx($id,2);
        $res['cats'] = $productCats;
        return $this->success($res);
    }

    public function update(Request $request){
        $children = $request->get("chunks");
        $all = $request->all();
        $id = $request->id;
        if(!$children || !$id) return $this->error("参数缺失");
        $records = $this->_checkCollectionChildren($children);
        if(!$records) return $this->error("参数校验失败");

        $upData['colle_name'] = $request->colle_name;
        $upData['colle_desc'] = $request->colle_desc;
        $upData['rec_cat_id'] = $request->rec_cat_id??'';
        $upData['colle_id'] = $request->colle_id;
        $upData['can_search'] = $request->can_search??0;
        $upData['display_start_time'] = !empty($all['display_start_time'])?strtotime($all['display_start_time']):0;
        $upData['display_end_time'] = !empty($all['display_end_time'])?strtotime($all['display_end_time']):0;
        $upData['short_colle_desc'] = $request->short_colle_desc;
        $upData['priority_cat_id'] = $request->priority_cat_id??0;

        $upSum = Collection::updateById($id,$upData);
        if(!$upSum) return $this->error(0,"更新商品集合失败");

        $records = array_values($records);

        $res = DB::transaction(function ()use($id,$records) {
            $delNum = CollectionRelation::deleteById($id);
            if(!$delNum) return false;
            $ret = CollectionRelation::insertCollectionRelation($id,$records);
            if(!$ret) return false;
            return true;
        });
        if(!$res) return $this->error("更新商品关联失败");

        $colleId = $request->id;
        $pService = new ProductService();
        $pService->cacheCollectionInfoById($colleId);

        return $this->success("更新商品集合成功");
    }

    //检查collection下skus的合法性
    protected function _checkCollectionChildren($children){
        $allSkus = $records = [];
        $children = $children?:[];
        foreach($children as $k=>$child){
            $skus = $child['skus']??[];
//            $productId = $child['product_id'];
            if(!$skus || !is_array($skus)) continue;
            $records[] = [
                'skus'=>$skus,
                'is_freebie'=>empty($child['is_freebie'])?0:1,
            ];
            $allSkus = array_merge($allSkus,$skus);
        }
        if(!$records) return false;

        $skus = Sku::batchGetSkuInfoBySkuId($allSkus,false);

        //数据校验
        foreach($records as $k=>$record){
            foreach($record['skus'] as $i=>$skuid){
                //skuid 不存在的 剔除出去
//                dd(in_array('sku1',$skus));
                if(empty($skus[$skuid])){
//            if(!$skus[$record['sku_id']] || ( ((array)$skus[$record['sku_id']])['product_id'] != $record['product_id']) ){
                    unset($records[$k]['skus'][$i]);
                }
            }
            if(!$records[$k]['skus']) unset($records[$k]);
        }
        if(!$records) return false;
        return $records;
    }

    /**
     * 修改产品上下架状态.
     */
    public function changeStatus(Request $request)
    {
        $colleId = $request->id;
        $allData = $request->all();
        $upData = [];
        foreach($allData as $k=>$v){
            if(($k == 'status') && ($v == 1) ){
                $spu = Collection::getCollectionInfoById($colleId,true);
                if(empty($spu['products'])) return $this->error(0,'products 为空，无法上架');
            }

            if(in_array($k,Collection::$fields)){
                $upData[$k] = $v;
            }
        }

        $res = false;

        $upNum = Collection::updateById($colleId,$upData);
        if($upNum){
           $pService = new ProductService();
           $res = $pService->updateColletioCache($colleId,$upData);
        }
        if(empty($res)){
            return $this->error(0,'更新失败');
        }else{
            return $this->success([]);
        }
    }

    public function saveDetail(Request $request){
        $all = $request->all();

        $fields = [
            'kv_images' => 'required',
            'id' => 'required',
            'wechat'  => 'required',
            'pc'  => 'required'
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $id = $all['id'];
        $kv_images = json_decode($all['kv_images'],true)??[];
//        if(!empty($all['kv_video'])) $kv_video = json_decode($all['kv_video'],true);
        $wechat = $all['wechat'];
        $pc = $all['pc'];

        $db_kv_images = $kv_images??[];
//        if(!empty($kv_video)) array_push($db_kv_images,$kv_video);
        Collection::where('id',$id)->update(['kv_images'=>json_encode($db_kv_images)]);
        CollectionDetail::updateOrCreate(
            ['channel'=>'wechat','product_idx'=>$id],
            ['detail'=>$wechat]
        );
        CollectionDetail::updateOrCreate(
            ['channel'=>'pc','product_idx'=>$id],
            ['detail'=>$pc]
        );
        return $this->success("",'更新成功');

    }

    public function getDetail(Request $request){

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

        $detail = CollectionDetail::getDetailsByPid($request->id);

        return $this->success($detail,'更新成功');

    }

}
