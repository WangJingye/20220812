<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\Dlc\SftpFile;

class SftpFileTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $sftp = new SftpFile;
//        $remote = '/upload/Testing/in/test.txt';
        $local_path = storage_path('file/img/601792b337082.jpg');
//        if(!is_dir($local_path)){
//            mkdir($local_path,0777,true);
//        }
        $local = $local_path;
        $path = 'tp_upload/static/qrcode/test.jpg';
        try{
//            $result = $sftp->is_file($path)?'111':'000';
//            $path = '/';
//            $result = $sftp->scan($path);
            $result = $sftp->upload($local,$path);
            print_r($result);
//            $sftp->unlink($path.'/test.txt');
        }catch (\Exception $e){
            echo $e->getMessage();
        }
        $this->assertTrue(true);
    }

}
