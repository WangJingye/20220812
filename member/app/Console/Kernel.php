<?php

namespace App\Console;

use App\Console\Commands\PointmallCancel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\ImportUser',
        'App\Console\Commands\SendMasterToPos',
        'App\Console\Commands\LoadUserLevel',
        'App\Console\Commands\YouShuCommand',
        PointmallCancel::class,
        Commands\setFissionRank::class,
        Commands\updateStoreMap::class,
        Commands\MemberShell::class,
        Commands\TestShell::class,
        Commands\RunShell::class,
        Commands\UserBalanceShell::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //新人优惠券发放脚本
        $schedule->command('coupon:shell sendnewcoupon')->everyMinute();
        //随单券发放脚本
        $schedule->command('coupon:shell orderTriggerCouponSend')->dailyAt('04:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
