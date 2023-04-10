<?php

namespace App\Console\Commands;

use App\Model\Element;
use App\Model\Pages;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CmsOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:online';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cms Auto Onlone.';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->published();
        $this->offine();

    }


    protected function published(){
        $list=Pages::withCount('element')->with(['element'=>function(HasMany $query){
            $query
                ->where([
                    ['published_at','<',date("Y-m-d H:i:s")],
                    ['published','0'],
                ])
            ;
        }])->get()->filter(function($item,$key){
            return $item->element->count() > 0 ;
        });
        dump($list->toArray());
        foreach($list as $page){
            foreach ($page->element as $element){
                if($page->share_image){
                    $page->share_image =  $ossDomain=env('OSS_DOMAIN').'/cms'.$page->share_image;
                }
                if($element['published_at'] < date('Y-m-d H:i:s')){
                    $status = 1;
                    DB::table('element_published')->where('id',$element['id'])->update(['published'=>1,'status'=>$status]);
                    $this->createAPI($page,$element,$status);
                }
            }
        }
    }

    protected function offine(){
        $list=Pages::withCount('element')->with(['element'=>function(HasMany $query){
            $query
                ->where([
                    ['offline_at','<',date("Y-m-d H:i:s")],
                    ['offline','0'],
                ])
            ;
        }])->get()->filter(function($item,$key){
            return $item->element->count() > 0 ;
        });
        dump($list->toArray());
        foreach($list as $page){
            foreach ($page->element as $element){
                if($page->share_image){
                    $page->share_image =  $ossDomain=env('OSS_DOMAIN').'/cms'.$page->share_image;
                }
                if($element['published_at'] < date('Y-m-d H:i:s')){
                    $status = 0;
                    DB::table('element_published')->where('id',$element['id'])->update(['offline'=>1,'status'=>$status]);
                    $this->createAPI($page,$element,$status);
                }
            }
        }
    }

    protected function createAPI($page,$element,$status){
        $saveType=$element['type'];
        if($saveType ==='h5') {
            $ossFileName='os';
        }elseif($saveType=='wechat'){
            $ossFileName='wechat';
        }else{
            throw new \Exception('oss保存错误！');
        }
        $ossClient = new \App\Lib\Oss();
        $path= 'cms/page/'.$page->key.'/'.$ossFileName.'.json';
        $nodes=json_decode($element['content'],true);
        unset($page->element_count);
        unset($page->element);
        $page->nodes=$nodes??"[]";
        $page->status=$status;
        $content = json_encode($page);
        $ossClient->putObject($path,$content);
    }



}
