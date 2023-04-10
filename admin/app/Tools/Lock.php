<?php
/**
 *  ===========================================
 *  File Name   Http.php
 *  Class Name  Http
 *  Date        2020-07-28 14:13
 *  Created by  William Ji
 *  ===========================================
 **/

namespace App\Tools;

class Lock
{
    /**
     * 进程锁
     */
    public static function processLock(String $fname = '')
    {
        if (empty($fname)) {
            $fname = basename(__FILE__);
        }
        $lock = fopen(public_path() . '/PLocks' . '/' . $fname . '.lock', 'w+');

        return flock($lock, LOCK_EX | LOCK_NB);
    }
}
