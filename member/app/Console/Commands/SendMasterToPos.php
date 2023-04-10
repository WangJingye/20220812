<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\MasterVipService;


class SendMasterToPos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:masterToPos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Sisley Member Into From EC To Pos';

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
        //
        try{
            $masterVipService = new MasterVipService();
            $masterVipService->makeMasterVipData();

        }  catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
