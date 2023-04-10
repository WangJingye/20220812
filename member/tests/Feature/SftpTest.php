<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Service\Dlc\Sftp;

class SftpTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $sftp = new Sftp;
//        $remote = '/upload/Testing/in/test.txt';
//        $local_path = storage_path('sftp');
//        if(!is_dir($local_path)){
//            mkdir($local_path,0777,true);
//        }
//        $local = $local_path.'/'.date('YmdHis').rand(100,999).'.txt';
        $path = '/upload/Testing/in';
        try{
//            $dir = $sftp->scan($path);
//            print_r($dir);
            $sftp->unlink($path.'/test.txt');
        }catch (\Exception $e){
            echo $e->getMessage();
        }
        $this->assertTrue(true);
    }

}
