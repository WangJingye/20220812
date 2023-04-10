<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\Pos\sendMailController;
use App\Http\Controllers\Api\Pos\salesExcelController;

class SendPosEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:posEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Vip & Sales File Email';

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
        $salesExcel = new salesExcelController();
        $salesExcel->makeSalesExcel();
        $sendEmail = new sendMailController();
        $sendEmail->newMail();
    }
}
