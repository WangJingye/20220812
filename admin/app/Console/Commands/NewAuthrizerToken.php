<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\BaiWangAuthApi;

/**
 * ╔═════════════╦══════════════════════════════════════════
 * ║File Name    ║   NewAuthrizerToken.php
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Class Name   ║   NewAuthrizerToken
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created Date ║   2020-07-31
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created By   ║   william.ji@connext.com.cn
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Copy Right   ║   CONNEXT
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Use For      ║   获取token
 * ╚═════════════╩══════════════════════════════════════════
 */
class NewAuthrizerToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauthToken:new {appKey}';

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
     *
     * 获取客户的Token
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        $appKey = $this->argument('appKey');
        BaiWangAuthApi::getInstance()->newAccessToken($appKey);
    }
}
