<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Api\Wms\WmsNeedSynchServices;
use App\Http\Controllers\Api\Pos\sendMailController;


class WmsNeedSynch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wms:needSynch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find Wms Have New File To Synchronize';

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
            $wmsSynch = new WmsNeedSynchServices();
            $wms_new_file = $wmsSynch->getFileList();
            if($wms_new_file){
                $sendEmail = new sendMailController();
                $sendEmail->WmsFileEmail();
            }
//            $sendEmail = new sendMailController();
//            $sendEmail->newMail();
        }
        catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
