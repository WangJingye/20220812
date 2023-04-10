<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\ThrAccessInfo;
use App\Service\BaiWangAuthApi;
use App\Tools\Lock;

/**
 * ╔═════════════╦══════════════════════════════════════════
 * ║File Name    ║   RefreshAuthrizerToken.php
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Class Name   ║   RefreshAuthrizerToken
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created Date ║   2020-07-31
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Created By   ║   william.ji@connext.com.cn
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Copy Right   ║   CONNEXT
 * ╠═ ═ ═ ═ ═ ═ ═╬═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═
 * ║Use For      ║   刷新token
 * ╚═════════════╩══════════════════════════════════════════
 */
class RefreshAuthrizerToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauthToken:refresh';

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
     * 刷新客户即将过期的Token
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        if (Lock::processLock('oauthToken_refresh')) {
            $timeOutAuthRows = ThrAccessInfo::where('expired_at', '<=', date('Y-m-d H:i:s'))->get()->toArray();
            if (!empty($timeOutAuthRows)) {
                foreach ($timeOutAuthRows as $timeOutAuthRow) {
                    try {
                        BaiWangAuthApi::getInstance()->refreshAccessToken($timeOutAuthRow['authorizer_appid'], $timeOutAuthRow);
                        echo '[Refresh Token]#' . $timeOutAuthRow['authorizer_appid'] . '#Success#' . date('Y-m-d H:i:s') . "\r\n";
                    } catch (Exception $e) {
                        echo '[Refresh Token]#' . $timeOutAuthRow['authorizer_appid'] . '#' . $e->getMessage() . '#' . date('Y-m-d H:i:s') . "\r\n";
                    }
                }
            } else {
                echo '[Refresh Token]##No Expired#' . date('Y-m-d H:i:s') . "\r\n";
            }
        } else {
            echo '[Refresh Token]##Last Process Still Running#' . date('Y-m-d H:i:s') . "\r\n";
        }
    }
}
