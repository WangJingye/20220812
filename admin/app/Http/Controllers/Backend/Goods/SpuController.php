<?php

namespace App\Http\Controllers\Backend\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class SpuController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.goods.spu.index',[
            'query_string'=>http_build_query($all)
        ]);
    }

    public function get(Request $request)
    {
        $data = $this->curl('goods/spu/getProd', $request->all());
        $detail = $data['data'];
        $specs = $data['specs'];
        $cats = $data['cats'];
        return view('backend.goods.spu.edit', ['detail' => array_to_object($detail),'specs'=>$specs,'cats'=>$cats]);
    }

    public function import(Request $request){
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
            $response = $this->curl('goods/spu/handleCsv', ['data'=>json_encode($data)]);
//            $response = $this->curl('goods/spu/handleCsv', ['handleMethod'=>$method,'data'=>json_encode($data)]);
            return $response;
        }catch(\Exception $e){
            return $this->error($e->getMessage());
        }

    }

    public function export(Request $request){
        $params = [
            'limit'=>50,
            'retSku'=>1,
//            'retSpecs'=>1,
//            'product_id'=>'capacity_ml-66935120-46946',
        ];
        $response = $this->curl('goods/spu/list', array_merge(request()->all(),$params));
        if($response['code']){
            $data = $response['data']['pageData']??[];
            $content = '';
            foreach($data as $product){
                $spec_type = $product['display_type']??'';
//                dd($specs[$spec_type]);
                foreach($product['skus'] as $k=>$sku){
                    $one = [];
                    $spec_code = !empty($sku[$spec_type])?$sku[$spec_type]:'';
//                    $spec_desc = empty($sku['spec_'.$spec_type.'_code_desc'])?'':(empty($specs[$spec_type]['ffffff']['spec_desc'])?'':$specs[$spec_type]['ffffff']['spec_desc']);
//                    $spec_desc = empty($spec_desc)?$spec_code:$spec_desc;
//                    if(!empty($specs[$spec_type][$spec_code])){
//                        var_dump($specs[$spec_type][$spec_code]);
//                        exit;
//                    }

//                    $spec_type = $product['spec_type'];
                    $one[] = $sku['sku_id'];
                    $one[] = str_replace(["\r","\n",'"',','],'',$spec_code);
                    $one[] = $product['product_id'];
                    $one[] = $sku['ori_price'];
                    $one[] = $sku['revenue_type'];
                    $one[] = str_replace(',','，',str_replace(["\r","\n",'"',','],'',(($k == 0)?$product['product_name']:''))) ;
                    $one[] = str_replace(',','，',str_replace(["\r","\n",'"',','],'',(($k == 0)?$product['product_name_en']:'')));
                    $one[] = (($k == 0)?$spec_type:'');
                    $one[] = str_replace(',','，',(($k == 0)?$product['product_desc']:''));
                    $one[] = str_replace(',','，',(($k == 0)?$product['short_product_desc']:''));
                    $one[] = str_replace(["\r","\n",'"',','],'',$sku['spec_property']);
                    $one[] = $sku['size'];
                    $one[] = $product['id'];

//                    $one[] = [
//                        'sku_id'=>$sku['sku_id'],
//                        'product_idx'=>$product['id'],
//                        'ori_price'=>$sku['ori_price'],
//                        'revenue_type'=>$sku['revenue_type'],
//                        'size'=>$sku['size'],
//                    ];
                    $content .= implode(',',$one)."\r\n";

                }



            }


            Header( "Content-type:   application/octet-stream ");
            Header( "Accept-Ranges:   bytes ");
            header( "Content-Disposition:   attachment;   filename=dlc_product.csv ");
            header( "Expires:   0 ");
            header( "Cache-Control:   must-revalidate,   post-check=0,   pre-check=0 ");
            header( "Pragma:   public ");

            echo $content;
            exit;
        }
        return $this->error('获取商品失败');

    }



    public function edit(Request $request)
    {
        if ($request->isMethod('post')) {
            $postData = $request->all();
            unset($postData['file']);
            $response = $this->curl('goods/spu/editProd', $postData);
            return $response;
        }
    }

    public function add(Request $request)
    {
        return view('backend.goods.spu.add',[
//            'specs'=>['color'=>'色号','capacity_ml'=>'规格1','capacity_g'=>'规格2']
            'specs'=>['capacity_ml'=>'规格1','capacity_g'=>'规格2']
        ]);
    }

    public function insert(Request $request)
    {
        $postData = $request->all();
        unset($postData['file']);
        $response = $this->curl('goods/spu/add', $postData);

        return $response;
    }

    public function checkSpec(Request $request){
        $postData = $request->all();
        $response = $this->curl('goods/spu/checkSpec', $postData);
        return $response;
    }

    public function changeStatus(Request $request)
    {
//        if ($request->isMethod('post')) {
            $postData = $request->all();
            $changeBack = $this->curl('goods/spu/changeStatus', $postData);

            return $changeBack;
//        }
    }

    public function list()
    {
        $response = $this->curl('goods/spu/list', request()->all());
//        $response = $this->curl('goods/spu/backList', request()->all());

        return $response['data'];
    }



    public function relateSkus()
    {
        $detail = $this->curl('goods/spu/relateSkus', request()->all());

        return view('backend.goods.spu.relate', ['detail' => json_encode($detail['data'])]);
    }

    public function editRelateSkus(Request $request)
    {
        $response = $this->curl('goods/spu/editRelateSkus', request()->all());

        return $response;
    }

    public function uploadFile(Request $request)
    {
        $path = request()->file->store('config');
        $local_path = storage_path() . '/app/' . $path;

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        try {
            $spreadSheet = $reader->load($local_path);
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            die($e->getMessage());
        }

        $sheetCount = $spreadSheet->getSheetCount(); //获取sheet工作表总个数
        $sheetData = [];
        for ($i = 0; $i <= $sheetCount - 1; ++$i) {//循环sheet工作表的总个数
            $sheet = $spreadSheet->getSheet($i);
            $highestRow = $sheet->getHighestRow();  // 最大行数
            if ($highestRow <= 1) { // 因为students.xlsx表格数据是从第三行开始的
                exit('Excel sheet ' . $i . '没有任何数据');
            }
            $data = [];
            if (0 === $i) {
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $tempData = [];
                    $tempData['product_id'] = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $tempData['master_catalog_item'] = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $tempData['material'] = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                    $tempData['material_code'] = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                    $tempData['collection_name'] = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                    $tempData['collection_code'] = $sheet->getCellByColumnAndRow(6, $row)->getValue();
                    $tempData['sub_collection_name'] = $sheet->getCellByColumnAndRow(7, $row)->getValue();
                    $tempData['sub_collection_code'] = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                    $tempData['style_category_number'] = $sheet->getCellByColumnAndRow(9, $row)->getValue();
                    $tempData['product_description'] = $sheet->getCellByColumnAndRow(10, $row)->getValue();
                    $tempData['brand'] = $sheet->getCellByColumnAndRow(11, $row)->getValue();
                    $tempData['brand_code'] = $sheet->getCellByColumnAndRow(12, $row)->getValue();
                    $tempData['gold_type'] = $sheet->getCellByColumnAndRow(13, $row)->getValue();
                    $tempData['gold_type_code'] = $sheet->getCellByColumnAndRow(14, $row)->getValue();
                    $tempData['style_number'] = $sheet->getCellByColumnAndRow(15, $row)->getValue();
                    $tempData['usage_code'] = $sheet->getCellByColumnAndRow(16, $row)->getValue();
                    $tempData['usage'] = $sheet->getCellByColumnAndRow(17, $row)->getValue();
                    $tempData['product_type'] = $sheet->getCellByColumnAndRow(18, $row)->getValue();
                    $tempData['product_name'] = $sheet->getCellByColumnAndRow(19, $row)->getValue();
                    $tempData['custom_keyword'] = $sheet->getCellByColumnAndRow(20, $row)->getValue();
                    $tempData['price_type'] = $sheet->getCellByColumnAndRow(21, $row)->getValue();
                    $tempData['is_noauto'] = $sheet->getCellByColumnAndRow(22, $row)->getValue();
                    $tempData['special_type'] = $sheet->getCellByColumnAndRow(23, $row)->getValue();
                    $sheetData['spu'][] = $tempData;
                }
            } else {
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $tempData = [];
                    $tempData['sku'] = $sheet->getCellByColumnAndRow(1, $row)->getValue();
                    $tempData['master_catalog_item'] = $sheet->getCellByColumnAndRow(2, $row)->getValue();
                    $tempData['length'] = $sheet->getCellByColumnAndRow(3, $row)->getValue();
                    $tempData['style'] = $sheet->getCellByColumnAndRow(4, $row)->getValue();
                    $tempData['price'] = $sheet->getCellByColumnAndRow(5, $row)->getValue();
                    $tempData['product_type'] = $sheet->getCellByColumnAndRow(6, $row)->getValue();
                    $tempData['price_type'] = $sheet->getCellByColumnAndRow(7, $row)->getValue();
                    $tempData['labor_price'] = $sheet->getCellByColumnAndRow(8, $row)->getValue();
                    $tempData['weight'] = $sheet->getCellByColumnAndRow(9, $row)->getValue();
                    $tempData['special_type'] = $sheet->getCellByColumnAndRow(10, $row)->getValue();
                    $sheetData['sku'][] = $tempData;
                }
            }
        }

        $response = $this->curl('goods/spu/createCharme', ['sheetData' => $sheetData]);

        $code = 1 == $response ? 0 : 1;

        return ['code' => $code];
    }

    public function cms(){
        $result = $this->curl('goods/spu/getDetail',['id'=>request('id')]);

        $kv_images='[]';
        $pc='[]';
        $wechat='[]';
        if($result['code']==1 && isset($result['data']['kv_images'])){
            $kv_images=$result['data']['kv_images'];
        }
        if($result['code']==1 && isset($result['data']['pc'])){
            $pc=$result['data']['pc'];
        }
        if($result['code']==1 && isset($result['data']['wechat'])){
            $wechat=$result['data']['wechat'];
        }
        return view('backend.goods.spu.cms',compact('kv_images','pc','wechat'));
    }

    public function cmssave(){
        $data=[
            'id'=>request('id'),
            'wechat'=>request('content')['h5'],
            'pc'=>request('content')['h5'],
            'kv_images'=>request('kv_images',request('kv_images')),
        ];
        $result=$this->curl('goods/spu/saveDetail',$data);
        return $result;
    }

}
