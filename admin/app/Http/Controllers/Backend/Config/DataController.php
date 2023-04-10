<?php namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Arr;

class DataController extends Controller
{
    private $config = [];
    public function __construct()
    {
        parent::__construct();
        $this->config = [
            'cat'=>[
                'length'=>6,
                'api'=>'goods/data/import',
            ],
            'spu'=>[
                'length'=>13,
                'api'=>'goods/data/import',
            ],
            'price'=>[
                'length'=>2,
                'api'=>'goods/data/import',
            ],
            'img'=>[
                'length'=>8,
                'api'=>'goods/data/import',
            ],
            'rel'=>[
                'length'=>2,
                'api'=>'goods/data/import',
            ],
            'key'=>[
                'length'=>2,
                'api'=>'goods/data/import',
            ],
            'sort'=>[
                'length'=>2,
                'api'=>'goods/data/import',
            ],
            'ship'=>[
                'length'=>3,
                'api'=>'oms/data/import',
            ],
            'stock'=>[
                'length'=>4,
                'api'=>'oms/data/import',
            ],
        ];
    }

    public function index(Request $request)
    {
        $all = $request->all();
        unset($all['_url']);
        return view('backend.config.data.index');
    }

    public function import(Request $request,$action)
    {
        $error = 0;
        try{
            $file = $request->file('file');
            if($file->isValid()){
                $originalName = $file->getClientOriginalName();
                $info = pathinfo($originalName);
                if(in_array($info['extension'],['xlsx','xls'])){
                    $realPath = $file->getRealPath();
                    $spreadsheet = IOFactory::load($realPath);
                    $data = $spreadsheet->getActiveSheet()->toArray();
                    if($length = Arr::get($this->config,"{$action}.length")){
                        $data = $this->sliceDatas($data,$length);
                    }else{
                        throw new \Exception('错误的格式');
                    }
                    if($data){
                        $api = app('ApiRequestInner');
                        $params = [
                            'action'=>$action,
                            'params'=>$data
                        ];
//                        print_r(json_encode($params));exit;
                        $resp = $api->request(Arr::get($this->config,"{$action}.api"),'POST',$params);
                        if($resp['code']==1){
                            return true;
                        }throw new \Exception($resp['message']);
                    }throw new \Exception('数据为空');
                }throw new \Exception('格式错误,只支持xlsx格式');
            }throw new \Exception('无效的文件');
        } catch (\Throwable $e){
            $error = $e->getMessage();
        } finally {
            $result = $error?['code'=>0,'message'=>$error]:['code'=>1,'message'=>'成功'];
            Log::info('后台商品数据导入:',[
                'action'=>$action,
                'error'=>$error
            ]);
            return $result;
        }
    }

    /**
     * @param $ori_data
     * @param $len
     * @return array
     */
    private function sliceDatas($ori_data,$len){
        $data = [];
        if(is_array($ori_data) && count($ori_data)){
            //去除首行
            if(array_key_exists(0,$ori_data)){
                unset($ori_data[0]);
            }
            foreach($ori_data as $line){
                //null转成空
                foreach($line as &$l){
                    if(is_null($l)){
                        $l = '';
                    }
                }
                //如果第一列为empty则跳出
                if(empty($line[0])){
                    break;
                }
                $data[] = array_slice($line,0,$len);
            }
        }return $data;
    }

}
