<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class CategoryController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        return view('backend.goods.category.index');
    }

    public function add(Request $request)
    {
        $prodTypeList = $this->curl('goods/common/getProdTypelist');

        return view('backend.goods.category.add', ['prodTypeList' => array_to_object($prodTypeList)]);
    }

    public function create(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['tag_1'], $postData['relatedItemsJson'], $postData['file']);
            $result = $this->curl('goods/category/addCate', $postData);

            return $result;
        }
    }

    public function get(Request $request)
    {
        $params = $request->all();
        $params['is_all'] = 1;
        $detail = $this->curl('goods/category/getCate', $params);
        $data = &$detail['data'];
        return view('backend.goods.category.edit', ['detail' => array_to_object($data)]);
    }

    public function relate(Request $request){
        $all = $request->all();
        unset($all['_url']);
        return view('backend.goods.category.relate',
            [
                'query_string'=>http_build_query($all),
                'cat_id'=>$all['cat_id']
            ]);
    }

    public function batchChangeSort(Request $request){
        $response = $this->curl('goods/category/batchChangeSort', $request->all());
        return $response??[];
    }

    public function getCatProdAndColleList(Request $request){
        $response = $this->curl('goods/spu/getCatProdAndColleList', $request->all());
        return $response??[];
    }

    public function updateCatRelation(Request $request){
        $response = $this->curl('goods/spu/updateCatRelation', $request->all());
        return $response??[];
    }

    public function getProdAndCollList(Request $request){
        $response = $this->curl('goods/spu/getProdAndCollList', $request->all());
        return $response??[];
    }

    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
//            unset($postData['_token'], $postData['tag_1'], $postData['relatedItemsJson'], $postData['file']);
            $response = $this->curl('goods/category/editCate', $postData);

            return $response;
        }
    }

    public function addProducts(Request $request){
        $result = $this->curl('goods/category/addProducts', $request->all());
        return $result;
    }

    public function delProduct(Request $request){
        $result = $this->curl('goods/category/delProduct', $request->all());
        return $result;
    }

    public function look(Request $request)
    {
        $prodTypeList = $this->curl('goods/common/getProdTypelist');
        $detail = $this->curl('goods/category/getCate', $request->all());
        $data = &$detail['data'];
        $relatedItems = $data['relatedItems'] ?: [];
        $relatedItemsArr = [];
        foreach ($relatedItems as $relatedItem) {
            $relatedItemsArr[] = $relatedItem['master_catalog_item'];
        }
        $relatedItemsStr = implode(',', $relatedItemsArr);
        $selectedItems = [];
        if ($data['selected_items']) {
            $selectedItemsRaw = explode(',', $data['selected_items']);
            foreach ($selectedItemsRaw as $selKeyRaw => $selectedItemRaw) {
                $selectedItems[$selKeyRaw] = ['master_catalog_item' => $selectedItemRaw];
            }
            unset($selectedItemsRaw);
        } else {
            $selectedItems = [];
        }
        $selectedItemsJson = json_encode($selectedItems);
        $data['selectedItems'] = $selectedItems;
        $data['selectedItemsJson'] = $selectedItemsJson;
        $data['selectedItemsStr'] = $data['selected_items'];
        $data['relatedItemsStr'] = $relatedItemsStr;
        $data['relatedItemsJson'] = json_encode($relatedItems);
        $data['initProdsArr'] = empty($data['init_prods']) ? [] : explode(',', $data['init_prods']);
        $data['customProdType'] = empty($data['custom_prod_type']) ? [] : explode(',', $data['custom_prod_type']);

        return view('backend.goods.category.look', ['detail' => array_to_object($data), 'prodTypeList' => array_to_object($prodTypeList)]);
    }

    public function calculateProdIds(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            $response = $this->curl('goods/category/calculateProds', $postData);

            return json_encode($response['data']);
        }
    }

    public function relateProds(Request $request)
    {
        $detail = $this->curl('goods/category/relateProds', request()->all());

        return view('backend.goods.category.relate', ['detail' => json_encode($detail['data'])]);
    }

    public function pCateListNoSub(Request $request)
    {
        $response = $this->curl('goods/category/pCateListNoSub', request()->all());
        return $response['data'];
    }

    public function editRelateProds(Request $request)
    {
        $response = $this->curl('goods/category/editRelateProds', request()->all());

        return $response;
    }

    public function list(Request $request)
    {
        $response = $this->curl('goods/category/list', request()->all());

        return $response;
    }

    public function getProdTypelist(Request $request)
    {
        $response = $this->curl('goods/common/getProdTypelist', request()->all());

        return $response;
    }

    public function prodList(Request $request)
    {
        $response = $this->curl('goods/spu/list', request()->all());

        return $response['data'];
    }

    public function pCateList(Request $request)
    {
        $response = $this->curl('goods/category/pCateList', request()->all());

        return $response;
    }

    public function offCat(Request $request)
    {
        $response = $this->curl('goods/category/offCat', request()->all());

        return $response;
    }

    public function upCat(Request $request)
    {
        $response = $this->curl('goods/category/upCat', request()->all());

        return $response;
    }

    public function handleCatSortCsv(Request $request)
    {
        try{
            $file = $_FILES;
            $excel_file_path = $file['file']['tmp_name'];
            if(!$excel_file_path) return [];
            $content = file_get_contents($excel_file_path);
            $content_data = explode("\r\n", $content);
            $method = '';
            $data = [];
            foreach($content_data as $k=>$record){
                $record = str_replace('\n','',$record);
                $fields = explode(',',$record);
                if($k == 0) {
//                    $tmp = explode(' ',trim($fields[0]));
//                    if(strpos($tmp[0],'Spu') !== false) $method = 'handleSpuCsv';
//                    if(strpos($tmp[0],'Sku') !== false) $method = 'handleSkuCsv';
//                    if(strpos($tmp[0],'Spec') !== false) $method = 'handleSpecCsv';
                    continue;
//                    $method = 'handle'.$tmp[0].'Csv';
                };
                $data[] = $fields;
            }

//            if(!$data || !$method) return ['code'=>0];
            $response = $this->curl('goods/category/handleCatSortCsv', ['data'=>json_encode($data)]);
//            $response = $this->curl('goods/spu/handleCsv', ['handleMethod'=>$method,'data'=>json_encode($data)]);
            return $response;
        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }
    }

}
