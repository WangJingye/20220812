<?php

namespace App\Http\Controllers\Backend\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;

class SpuController extends Controller
{


    protected $prod_default='miniStore/pdt-detail/prod_default.jpg';
    protected $model_default='miniStore/pdt-detail/model_default.jpg';

    public function index(Request $request)
    {
        return view('backend.config.spu',['prod_default'=>$this->prod_default,'model_default'=>$this->model_default]);
    }

    public function upload($img){
        $file=$_FILES['file'];
        if(isset($file['type'])){
            $type=$file['type'];
            if($type && !in_array($type, ['image/jpeg','image/pjpeg'])){
                return [
                    'status'=>false,
                    'message'=>'图片只支持jpg,不支持'.$type
                ];
            }
        }

        if(isset($file['size'])){
            $size=$file['size'];
            if($size> 1024*512){
                return [
                    'status'=>false,
                    'message'=>'图片尺寸不能大于512K'
                ];
            }
        }


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



        $ossClient=new \App\Lib\Oss();
        if($img == 'prod_default'){
            $remotePath= $this->prod_default;
        }

        if($img == 'model_default'){
            $remotePath= $this->model_default;
        }
        $filePath=realpath($file['tmp_name']);
        $true=$ossClient->upload($remotePath,$filePath);
        $cdn = new \App\Lib\CDN();
        $cdn->refreshFile(env('OSS_DOMAIN').'/'.$remotePath);



        if($true){
            return [
                'status'=>true,
                'file'=>$remotePath.'?tockenId='.microtime(true)
            ];
        }else{
            return [
                'status'=>false,
                'message'=>'oss上传错误'
            ];
        }

    }


}
