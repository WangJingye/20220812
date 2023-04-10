<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\Config;
use App\Model\RedisModel;
use Illuminate\Support\Facades\DB;
use Swoole;

class ImportController extends Controller
{


    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('backend.config.import.list');
    }

    function dataList()
    {
        // return ['数据库33344：'=>env('DB_DATABASE')];


        $limit = request('limit', 10);

        $res = DB::table('import_log')
            ->select(DB::raw("import_log.*,concat ( left ((success + error) / count *100,5),'%') as percent"))
            ->orderBy('id','desc')
            ->paginate($limit)
            ->toArray();


        $response['code'] = 0;
        $response['msg'] = "获取规则列表成功.";
        $response['count'] = $res['total'];
        $response['data'] = $res['data'];

        return $response;
    }

    public function ajaxUpload(){
        $file=$_FILES['file'];
        if(isset($file['error'])){
            $error=$file['error'];
            if($error> 0  && $error !=4){
                $errorMessage=[
                    0,
                    '文件大小比php.ini中upload_max_filesize指定值要大',
                    '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
                    '文件只有部分被上传',
                    '没有文件被上传',
                    '上传文件大小为',
                ];
                return [
                    'status'=>false,
                    'message'=>'错误代码：'.$errorMessage[$error]
                ];
            }
        }
        if(isset($file['tmp_name'])){
            if($file['tmp_name']){
                $ext=pathinfo($file['name']);
                $string=uniqid();
                $dir='import';
                $new='/upload/'. $dir.'/' .$string.'.csv';
                if(!is_dir(base_path('public/upload/'.$dir))){
                    mkdir(base_path('public/upload/'.$dir), 0777, true);
                }
                move_uploaded_file($file['tmp_name'], '.'.$new);
                $csvFile =realpath('./'.$new);
                $result = $this->apply($csvFile);

            }
        }
        return [
            'status'=>true,
            'file'=>realpath('./'.$new),
        ];
    }

    public function apply($csvFile){
            $command ='php ../artisan import:do --filename='.$csvFile;
            $result=shell_exec($command);
    }

}
