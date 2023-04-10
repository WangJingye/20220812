<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function uploadPic()
    {
//        $path = request()->file->store('dlc_statics');
//        $local_path = storage_path().'/app/'.$path;
//        $localClient = new \App\Lib\Local();
//        return ['code' => 0, 'path' => $localClient->upload($path, $local_path)];

        $path = request()->file->store('images');
        $local_path = storage_path().'/app/'.$path;
        $ossClient = new \App\Lib\Oss();
        $ossClient->upload($path, $local_path);

        return ['code' => 0, 'path' => env('OSS_DOMAIN').'/'.$path];
    }
}
