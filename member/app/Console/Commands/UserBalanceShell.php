<?php

namespace App\Console\Commands;

use App\Model\User;
use App\Model\UserBalance;
use App\Service\BalanceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Model\Users;
use App\Model\MemberMergeRecord;
use App\Service\Dlc\UsersService;
use App\Service\Dlc\Sftp;
use Illuminate\Support\Facades\Redis;

class UserBalanceShell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:shell {option} {params?}';

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
            $this->line('[' . date('Y-m-d H:i:s') . ']Successful');
        } catch (\Exception $e) {
            $this->line($e->getMessage());
        }
        $this->line('[' . date('Y-m-d H:i:s') . ']End');
    }

    private function overtime()
    {
        $userBalances = UserBalance::query()
            ->whereRaw('(status = 1 or status = 2)')
            ->where('end_time', '<=', date('Y-m-d H:i:s'))
            ->get()->toArray();
        $balanceService = new BalanceService();
        foreach ($userBalances as $userBalance) {
            $this->line('[' . date('Y-m-d H:i:s') . '] 购物卡过期，id:' . $userBalance['id']);
            try {
                DB::beginTransaction();
                $userInfo = Users::query()->find($userBalance['user_id']);
                $userInfo['balance'] -= $userBalance['amount'] - $userBalance['used_amount'];
                $userInfo->save();
                $params = [
                    'id' => $userBalance['id'],
                    'order_sn' => $userBalance['order_sn'],
                    'order_title' => $userBalance['gold_name'],
                ];
                $balanceService->refundBalanceCard($params);
                DB::commit();
            } catch (\Exception $e) {
                $this->line('[' . date('Y-m-d H:i:s') . '] 购物卡id【' . $userBalance['id'] . '】过期修改失败：' . $e->getMessage());
            }
        }
    }

}
