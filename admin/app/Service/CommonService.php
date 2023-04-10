<?php
/**
 *  ===========================================
 *  File Name   CommonService.php
 *  Class Name  wx-thr-server
 *  Date:       2019-10-17 09:59
 *  Created by
 *  Use for:
 *  ===========================================
 **/

namespace App\Service;

use Illuminate\Support\Facades\Log;

class CommonService
{
    public static function putCommonLog(string $msg = '', array $data = [], $level = 'success')
    {
        //监听调用信息
        $debugInfo = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        //记录错误信息、记录位置
        $msg .= '====》' . $debugInfo[1]['class'] . '/' . $debugInfo[1]['function'] . '():[Line:' . $debugInfo[0]['line'] . ']';
        if (app()->runningInConsole()) {
            //命令行模式下，加前缀cli
            $data['cli'] = true;
        } else {
            $data['cli'] = false;
        }

        if ($level === 'error') {
            Log::error($msg, $data);
        } else {
            Log::info($msg, $data);
        }

    }
}
