<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class RunShell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:shell {option} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     */
    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            call_user_func_array([__CLASS__, $option], [$params]);
            $this->line('['.date('Y-m-d H:i:s').']Successful');
        } catch (\Exception $e) {
            $this->line($e->getMessage().',line:'.$e->getLine());
        }$this->line('['.date('Y-m-d H:i:s').']End');
    }

    public function employeebind() {
        \App\Model\SaList::bindAll();
    }


}
