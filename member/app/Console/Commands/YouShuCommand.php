<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Users;

class YouShuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youshu:shell {option} {params?}';

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
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            $params = $params ?? '';

            if ($this->processLock("YouShuShell-{$option}")) {
                call_user_func_array([__CLASS__, $option], [$params]);
            }
            
            $this->line('All done');
        } catch (Exception $e) {
            $this->info($e->getMessage() . ' Method not found！');
        }
    }

    /**
     * The lock of process
     * @param String $fname
     * @return bool
     * @throws Exception
     */
    protected function processLock(String $fname)
    {
        if (empty($fname)) {
            throw new Exception("The name of lock file can not be empty.", 600);
        }
        $this->lock = fopen( __DIR__ . "/PLocks" . '/' . $fname . '.lock', "w+");
        if (flock($this->lock, LOCK_EX | LOCK_NB)) {
            return true;
        }
        else {
            throw new Exception("$fname process is exists.", 600);
        }
    }

    public function addWxappVisitPage()
    {
        try {
            $result = Users::addWxappVisitPage();
            var_export($result);
        } catch (Exception $e) {
            var_export($e->getMessage());
        }
    }
}
