<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunShellCommand extends Command
{
    protected $signature = 'run:shell {option}';

    protected $description = 'Run shell';

    public function __construct(){
        parent::__construct();
    }

    public function handle()
    {
        try{
            $option = $this->argument('option');
            $allow_options = ['makedir','cacheFileClean','redisclear'];
            if(in_array($option,$allow_options)){
                call_user_func([__CLASS__,$option]);
            }
            $this->line('All done');
        }catch (\Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
     * 生成目录
     */
    protected function makedir(){
        $upload_path = public_path().config('arden.upload').'/';
        $cache_path = $upload_path.'cache/';
        $this->mkdir($cache_path);
        $log_path = public_path().'/logs/';
        $this->mkdir($log_path);
    }

    private function mkdir($path,$mode = 0744){
        if(!is_dir($path)){
            mkdir($path,$mode,true);
        }
    }

    /**
     * 清除上传图片的缓存
     */
    protected function cachefileclean(){
        $cache_path = public_path().config('arden.upload').'/cache/';
        $files = scandir($cache_path);
        chdir($cache_path);
        $time = time();
        foreach($files as $file){
            //删除一周前的缓存文件
            if(($time-filemtime($file))>3600*24*7){
                @unlink($file);
            }
        }
    }

    /**
     * 清空Redis中相应的数据
     */
    protected function redisclear(){
        $redis = app('redis.connection');
        //清除类目的数据
        $redis->hdel(env('APP_NAME').'_Taxonomy');
    }
}