<?php
namespace App\Lib;

class Local{
    public function upload($path, $local_path){
        $p_path = public_path('dlc_statics');
        if(!is_dir($p_path)){
            mkdir($p_path,0777,true);
        }
        copy($local_path,public_path($path));
        return env('OSS_DOMAIN').'/'.$path;
    }
}