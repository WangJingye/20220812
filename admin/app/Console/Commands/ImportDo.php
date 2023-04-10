<?php

namespace App\Console\Commands;

use function AlibabaCloud\Client\json;
use App\Model\Element;
use App\Model\Pages;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Lib\Http;
use Swoole;
use Swoole\Process;
use Swoole\Event;

class ImportDo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:do {--filename=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量导入商品图文主程序';


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


    public function handle(){
        try{
            $worker = new Process(function ($worker) {
                Event::add($worker->pipe, function ($pipe) use ($worker) {
                    $csvFile = $worker->read();
                    $artisan = base_path().'/artisan';
                    $command ='php '.$artisan.' import:product_detail --filename='.$csvFile;
                    $result=shell_exec($command);
                    Event::del($worker->pipe);
                    //退出子进程
                    $worker->exit();
                });
            },false,1,false);

            $worker->name('import_process');
            $worker->start();
            $csvFile=$this->option('filename');
            $worker->write($csvFile);
            Process::signal(SIGCHLD, function ($sig) {
                while ($ret = Process::wait(false)) {
                }
            });
            return [
                'status'=>1,
                'message'=>'请耐心等待程序处理完毕',
                'data'=>[
                    'file'=>$csvFile
                ]
            ];
        }catch (\Exception $e){
            return [
                'status'=>0,
                'message'=>$e->getMessage(),

            ];
        }


    }





}
