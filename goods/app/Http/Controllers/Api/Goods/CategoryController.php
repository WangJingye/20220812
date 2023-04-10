<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Model\Goods\Collection;
use App\Model\Goods\ProductCat;
use App\Service\Dlc\HelperService;
use App\Service\Goods\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\Category;
use App\Model\Goods\CateToProd;
use App\Model\Goods\Tree;
use App\Model\Goods\Spu;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    protected $_model = Category::class;

    /**
     * 分类列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        if ($request->cateName) {
            $cateList = Category::where('cat_name', 'like', '%' . $request->cateName . '%')->get()->toArray();
        } else {
            $cateList = Category::get()->toArray();
        }
        if (!empty($cateList)) {
            foreach ($cateList as &$cate) {
                $cate['_category_type'] = isset($cate['init_code']) && !empty($cate['init_code']) ? '自动更新' : '手动更新';
            }
        }
        $Tree = new Tree();
        $treeList = $Tree->getTreeData($cateList, 'tree', 'cat_name', 'id', 'parent_cat_id');
        $return = [];
        $return['pageData'] = array_values($treeList);

        return json_encode($return);
    }

    /**
     * 获取分类以及关联商品.
     */
    public function getCate(Request $request)
    {
        $cateIdx = $request->cateIdx;
        $is_all = $request->get('is_all')?:0;
        $cateSpusList = ProductCat::getProdAndColleById($cateIdx,2000,$is_all);
        $relatedItem = [];
        if(!empty($cateSpusList['all']))
            foreach ($cateSpusList['all'] as $spu) {
                $_tmpSpuArr = [];
                $_tmpSpuArr['id'] = (string)$spu['id'];
                $_tmpSpuArr['product_idx'] = (string)$spu['product_idx'];
                $_tmpSpuArr['product_type'] = (string)$spu['product_type'];
                $_tmpSpuArr['product_id'] = (string)$spu['product_id'];
    //            $_tmpSpuArr['master_catalog_item'] = $spu['master_catalog_item'];
                $relatedItem[] = $_tmpSpuArr;
            }
        $cateInfo = Category::where('id', $cateIdx)->first();
        if ($cateInfo) {
            $cateInfo['relatedItems'] = $relatedItem??[];
        }

        return $this->success($cateInfo??[]);
    }

    //下架类目
    public function offCat(Request $request){
        $cat_id = $request->cat_id;
        if(!$cat_id) return $this->error('参数缺失');
        $cats = [];
        Category::getChildrenByCatIds([$cat_id],$cats);
        $cat_ids = array_column($cats,'id');
        $cat_ids[] = $cat_id;
        $num = Category::batchOffCat($cat_ids);

        $pService = new ProductService();
        foreach ($cat_ids as $cat_id){
            $pService->cacheCatInfoById($cat_id);
        }

        return $this->success(['num'=>$num]);
    }

    //拖拽排序  批量修改商品排序
    public function batchChangeSort(Request $request){
        $cat_id = $request->cat_id;
        $ids = $request->ids;
        if(!$cat_id || !$ids) return $this->error('参数缺失');
        $ids = explode(',',$ids);

        $pnum = count($ids);
        $num = 0;
        foreach($ids as $k=>$id) {
            $sort = 3 * ($pnum - $k);
            ProductCat::where('id',$id)->update(['sort'=>$sort]);
            $num++;
        }

        return $this->success(['num'=>$num]);
    }

    //上架类目
    public function upCat(Request $request){
        $cat_id = $request->cat_id;
        if(!$cat_id) return $this->error('参数缺失');
        $num = Category::upCat($cat_id);

        $pService = new ProductService();
        $pService->cacheCatInfoById($cat_id);

        return $this->success(['num'=>$num]);
    }

    /**
     * 按层级获取分类.
     */
    public function pCateList(Request $request)
    {
        $level = $request->level ?: '1';
        $cateInfo = Category::select('id', 'category_id', 'category_name')->where('level', $level)->get()->toArray();
        $return = [];
        $return['data'] = $cateInfo;

        return json_encode($return);
    }

    /**
     * 获取非叶子分类.
     */
    public function pCateListNoSub(Request $request)
    {
//        $cat1s = Category::batchGetCatInfosByParentIds();
//        if(!$cat1s) $this->success([]);
//
//        $cat1_ids = array_column($cat1s,'id');
//        $cat2s = Category::batchGetCatInfosByParentIds($cat1_ids);
//        $cats = array_merge($cat1s,$cat2s);

        $cats = Category::all()->toArray();

//        $cateList = Category::select('id', 'p_cate_id as pid', 'category_id as cateId', 'category_name as name')->where('level', 1)->orWhere('level', 2)->get()->toArray();

        $Tree = new Tree();
        $tree = $Tree->getTreeData($cats, 'level', 'cat_name', 'id', 'parent_cat_id');

        $treeJson = str_replace('cat_name','name',json_encode($tree));
        $tree = json_decode($treeJson);

        return $this->success($tree);
    }

    /**
     * 获取关联商品关系.
     */
    public function relateProds(Request $request)
    {
        $cateIdx = $request->cateIdx;
        $cateSpusList = DB::table('css_cate_prod_relation')->leftJoin('css_products_info', 'css_cate_prod_relation.product_idx', '=', 'css_products_info.id')->where('css_cate_prod_relation.category_idx', $cateIdx)->orderBy('css_cate_prod_relation.sort', 'ASC')->get()->toArray();
        $return = [];
        $return['data'] = $cateSpusList;

        return json_encode($return);
    }

    /**
     * 编辑关联商品关系.
     */
    public function editRelateProds(Request $request)
    {
        $rawData = json_decode($request->data, true);
        try {
            DB::beginTransaction();
            foreach ($rawData as $key => $data) {
                $_tmpUData = [];
                $_tmpUData['category_idx'] = $data['category_idx'];
                $_tmpUData['product_idx'] = $data['product_idx'];
                $_tmpUData['hash'] = md5($_tmpUData['category_idx'] . '###' . $_tmpUData['product_idx']);
                $_tmpUData['sort'] = $key + 1;
                CateToProd::updateOrCreate(
                    ['hash' => $_tmpUData['hash']],
                    $_tmpUData
                );
            }
            DB::commit();

            return $this->success([]);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->error(0, $e->getMessage());
        }
    }

    public function addProducts(Request $request){
        $id = $request->id;
        if( ( empty($request->pids) && empty($request->cids) ) || empty($id)) return $this->error(0,'参数缺失');

        $add_products = [];
        if($request->pids){
            $add_pids = explode(',',$request->pids??"");
            $products = Spu::batchGetProductsInfoByPid($add_pids,false);
            $db_pids = array_keys($products);
            $final_pids = array_intersect($add_pids,$db_pids);
            if(empty($final_pids)) return $this->error(0,'商品ID不合法');
            $final_add_pids = ProductCat::batchInsertCatProducts($id,$final_pids);
            foreach ($final_add_pids as $pid){
                $add_products[] = [
                    'id'=>$pid,
//                    'product_id'=>$products[$pid]['product_id'],
                    'product_type'=>1
                ];
            }
        }
        if($request->cids){
            $add_cids = explode(',',$request->cids??"");
            foreach($add_cids as $cid){
                $colle = Collection::getCollectionInfoById($cid,false);
                if(!$colle) return $this->error(0,'集合ID不合法');
            }
//            $db_pids = array_keys($products);
//            $final_pids = array_intersect($add_pids,$db_pids);
//            if(empty($final_pids)) return $this->error(0,'商品ID不合法');
            $final_add_cids = ProductCat::batchInsertCatProducts($id,$add_cids,2);
            foreach ($final_add_cids as $pid){
                $add_products[] = [
                    'id'=>$pid,
                    'product_type'=>2
                ];
            }
        }
//        $add_pids = explode(',',$request->pids??"");
//        $products = Spu::batchGetProductsInfoByPid($add_pids,false);
//        $db_pids = array_keys($products);
//        $final_pids = array_intersect($add_pids,$db_pids);
//        if(empty($final_pids)) return $this->error(0,'商品ID不合法');



//        $final_add_pids = ProductCat::batchInsertCatProducts($id,$final_pids);
//        $add_products = [];
//        foreach ($final_add_pids as $pid){
//            $add_products[] = [
//                'id'=>$pid,
//                'product_id'=>$products[$pid]['product_id']
//            ];
//        }

//        foreach($final_pids as $pid){
//            try{
//                $insert_id = ProductCat::insertGetId(
//                    [
//                        'product_idx'=>$pid,
//                        'cat_id'=>$id,
//                    ]
//                );
//                if($insert_id) $add_products[] = [
//                    'id'=>$pid,
//                    'product_id'=>$products[$pid]['product_id']
//                ];
//            }catch(\Exception $e){
//                continue;
//            }
//        }
        if($add_products)  return $this->success($add_products,'添加成功');
        return $this->error(0,'插入失败');
    }


    public function delProduct(Request $request){
        $id = $request->id;
        $pid = $request->pid;
        $product_type = $request->product_type??1;
        if(empty($id) || empty($pid)) $this->error('参数缺失',[]);
        $num = ProductCat::where('cat_id',$id)->where('product_idx',$pid)->where('type',$product_type)->delete();
        if($num) return $this->success([],'删除成功');
        return $this->error(0,'删除失败');
    }

    /**
     * 编辑分类以及关联商品.
     */
    public function editCate(Request $request)
    {
        $id = $request->id;
        $all = $request->all();

        $fields = [
            'cat_name' => 'required',
            'id' => 'required'
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $cateGoryModel = Category::where('id', $id)->first();
        $cateGoryModel->cat_name = $all['cat_name'];
        $cateGoryModel->cat_name_en = $all['cat_name_en']??'';
        $cateGoryModel->cat_kv_image = $all['cat_kv_image']??'';
        $cateGoryModel->share_content = $all['share_content']??'';
        $cateGoryModel->share_image = $all['share_image']??'';
        $cateGoryModel->list_image = $all['list_image']??'';
        $cateGoryModel->cat_desc = $all['cat_desc']??'';
        $cateGoryModel->cat_type = $all['cat_type']??1;
        $cateGoryModel->parent_cat_id = isset($all['parent_cat_id'])? $all['parent_cat_id']: 0;
        $cateGoryModel->sort = $all['sort']??0;
        $cateGoryModel->status = 1;
        // $data = [
        //     'cat_name'=>$all['cat_name'],
        //     'cat_name_en'=>$all['cat_name_en']??'',
        //     'cat_kv_image'=>$all['cat_kv_image']??'',
        //     'share_content'=>$all['share_content']??'',
        //     'share_image'=>$all['share_image']??'',
        //     'cat_desc'=>$all['cat_desc']??'',
        //     'cat_type'=>$all['cat_type']??1,
        //     'sort'=>$all['sort']??0,
        //     'status'=>1,
        // ];
        // if(isset($all['parent_cat_id'])){
        //     $data['parent_cat_id'] = $all['parent_cat_id']?:0;
        // }
        try{
            // $upNum = Category::updateById($id,$data);
            if($cateGoryModel->save()) return $this->success("更新成功");
            return $this->error("更新失败");
        }catch (\Exception $e){
            return $this->error("更新失败了");
        }
    }

    /**
     * 新建分类以及关联商品.
     */
    public function addCate(Request $request)
    {
        $all = $request->all();

        $fields = [
            'cat_name' => 'required',
//            'cat_kv_image' => 'required',
        ];
        $validator = Validator::make($request->all(), $fields, [
                'required'   => '请输入:attribute', // :attribute 字段占位符表示字段名称
            ]
        );

        if($validator->fails()){
            return $this->error($validator->errors()->first());
        }

        $cateGoryModel = new Category();
        $cateGoryModel->cat_name = $all['cat_name'];
        $cateGoryModel->cat_name_en = $all['cat_name_en']??'';
        $cateGoryModel->cat_kv_image = $all['cat_kv_image']??'';
        $cateGoryModel->share_content = $all['share_content']??'';
        $cateGoryModel->share_image = $all['share_image']??'';
        $cateGoryModel->list_image = $all['list_image']??'';
        $cateGoryModel->cat_desc = $all['cat_desc']??'';
        $cateGoryModel->cat_type = $all['cat_type']??1;
        $cateGoryModel->parent_cat_id = $all['parent_cat_id']??0;
        $cateGoryModel->sort = $all['sort']??0;
        $cateGoryModel->status = 1;
        // $data = [
        //     'cat_name'=>$all['cat_name'],
        //     'cat_name_en'=>$all['cat_name_en']??'',
        //     'cat_kv_image'=>$all['cat_kv_image']??'',
        //     'share_content'=>$all['share_content']??'',
        //     'share_image'=>$all['share_image']??'',
        //     'parent_cat_id'=>$all['parent_cat_id']??0,
        //     'sort'=>$all['parent_cat_id']??0,
        //     'status'=>1,
        // ];
        try{
            // $id = Category::insertGetId($data);
            if($cateGoryModel->save()){
                $id = $cateGoryModel->id;
                $add_pids = explode(',',$request->pids??"");
                if($add_pids){  //添加类目商品
                    $products = Spu::batchGetProductsInfoByPid($add_pids,false);
                    $db_pids = array_keys($products);
                    $final_pids = array_intersect($add_pids,$db_pids);
                    if($final_pids)
                        ProductCat::batchInsertCatProducts($id,$final_pids);
                }

                if($request->cids){
                    $add_cids = explode(',',$request->cids??"");
                    foreach($add_cids as $cid){
                        $colle = Collection::getCollectionInfoById($cid,false);
                        if(!$colle) return $this->error(0,'集合ID不合法');
                    }
                    ProductCat::batchInsertCatProducts($id,$add_cids,2);
                }

                return $this->success("创建类目成功");
            }
            return $this->error("新增失败");
        }catch (\Exception $e){
            return $this->error("新增失败");
        }
    }

   public function handleCatSortCsv(Request $request){
       //        $method = $request->handleMethod;
       $data = $request->data;
//       $data = '[["22","669230"],["21","669230"],["22","669240"],["21","669260"]]';
       $data = json_decode($data,true);
//       $data = [
//           [22,'669321'],
//           [22,'TZ136055'],
//           [22,'632061'],
//       ];
       if(empty($data)) return $this->error(0,"参数缺失");
       $num = 0;

       $spus = Spu::all()->toArray();
       $colls = Collection::all()->toArray();
       $spu_map = array_combine(array_column($spus,'product_id'),array_column($spus,'id'));

       foreach($colls as $coll){
           $spu_map[$coll['colle_id']] = $coll['id'].'-2';
       }

//       $coll_map = array_combine(array_column($spus,'colle_id'),array_column($spus,'id'));

       foreach($data as $one){
           if(count($one)<2) continue;
           $catid = $one[0];
           $pid = $spu_map[$one[1]]??0;
           if(!$catid || !$pid) continue;

           $cats[$catid][] = $pid;
       }

       foreach($cats as $cid=>$pids){
           $product_ids = $coll_ids = [];
           $pnum = count($pids);
            foreach($pids as $k=>$pid){
                list($pid,$type) = ProductService::parsePid($pid);
                if($type == 1) $product_ids[] = $pid;
                if($type == 2) $coll_ids[] = $pid;
                $up = [
                    'product_idx'=>$pid,
                    'cat_id'=>$cid,
                    'type'=>$type,
                ];
                $ins = [
                    'product_idx'=>$pid,
                    'cat_id'=>$cid,
                    'type'=>$type,
                    'sort'=>3*($pnum - $k),
                    'created_at'=>date('Y-m-d H:i:s'),
                ];
                ProductCat::updateOrCreate($up,$ins);
                $num++;
            }

            ProductCat::where('cat_id',$cid)->whereNotIn('product_idx',$product_ids)->where('type',1)->delete();
            ProductCat::where('cat_id',$cid)->whereNotIn('product_idx',$coll_ids)->where('type',2)->delete();
       }
//        $num = $this->$method($data);
       return $this->success(['num'=>$num]);
   }

    /**
     * 导入商品类目（有数）历史数据
     */
    public function exportCategoriesHistory(Request $request)
    {
        $data = Category::exportCategoriesHistory();
        return $this->success($data, 'success');
    }

    /**
     * 获取分类树形结构
     * @param Request $request
     * @return array
     */
    public function getTreeList(Request $request)
    {
        $cateList = Category::query()
            ->select('id', 'parent_cat_id as pid', 'cat_name as label','cat_kv_image as image','share_content','share_image','list_image')
            ->where('status',1)
            ->where('cat_type',1)
            ->orderBy('sort','desc')->get()->toArray();

        $Tree = new Tree();
        $tree = $Tree->getTreeData($cateList, 'level', 'label', 'id', 'pid');

        return $this->success($tree);
    }

    public function getEvent(Request $request)
    {
        $event_key = config('app.name').':cats:event';
        $redis = ProductService::getRedis();
        $catslist = $redis->hgetall($event_key);
        $cats = [];
        if(count($catslist)){
            foreach($catslist as $cat_id=>$cat_name){
                $cats[] = [
                    'val'=>$cat_id,
                    'name'=>$cat_name,
                ];
            }
        }
        $adlist = HelperService::getAd('home_event');
        $adInfo = $adlist?array_reduce($adlist,function($result,$item){
            $result[] = [
                'text'=>$item['name'],
                'link'=>$item['link'],
            ];
            return $result;
        }):[];
        $dlcop = HelperService::getAd('home_event_pop');
        if($dlcop){
            $dlcop = reset($dlcop);
            $pop = [
                'img'=>$dlcop['img'],
                'link'=>$dlcop['link'],
            ];
        }
        $pop = $pop??null;
        return $this->success(compact('adInfo','cats','pop'));
    }

    public function getAd(Request $request)
    {
        $param = $request->all();
        $v = Validator::make($param, [
            'type' =>'required',
        ], [
            'required' => 'type不可为空',
        ]);
        if ($v->fails()) {
            return $this->error($v->errors()->first());
        }
        $list = HelperService::getAd($param['type']);
        if($list){
            $list = array_reduce($list,function($result,$item){
                $line = [
                    'title'=>$item['name']?:'',
                    'content'=>$item['data1']?:'',
                    'image'=>$item['img']?:'',
                ];
                $result[] = array_filter($line);
                return $result;
            });
        }
        return $this->success($list?:[]);
    }

    public function getStarProducts(Request $request)
    {
        $list = [];
        $adlist = HelperService::getAd('star_products');
        foreach($adlist as $ad){
            if($ad['name'] && $ad['img'] && $ad['data1']){
                $list[] = [
                    'name'=>$ad['name'],
                    'img'=>$ad['img'],
                    'sku'=>$ad['data1'],
                    'price'=>$ad['data2'],
                ];
            }
        }
        if(count($list)){
            $skus = array_unique(array_column($list,'sku'));
            $skus_str = implode(',',$skus);
            $request->offsetSet('sku_ids',$skus_str);
            $request->offsetSet('type','mini');
            $result = (new \App\Http\Controllers\Outward\Goods\ProductController)->getProductInfoBySkuIds($request);
            if(!empty($result['data'])){
                $products = $result['data'];
                foreach($list as $item){
                    $sku = $item['sku'];
                    $data[] = [
                        'product_name'=>$item['name'],
                        'sku'=>$sku,
                        'img'=>$item['img'],
                        'product_id'=>$products[$sku]['id'],
//                        'product_name'=>$products[$sku]['product_name'],
                        'product_desc'=>$products[$sku]['product_desc'],
                        'price'=>$item['price'],
                    ];
                }
            }
        }
        return $this->success($data??[]);
    }
}
