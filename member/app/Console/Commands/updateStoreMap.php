<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Service\Store\StoreService;

/**
 * 脚本设置裂变会员当月top10
 */
class updateStoreMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:map';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updateStoreMap';

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
       $result = StoreService::updateStoreMap();
       $this->info($result);
    }
}
