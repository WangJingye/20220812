<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/5/16
 * Time: 11:44
 */

namespace App\Services\Api;

use App\Repositories\{OrderCreateRepository};

class MakeCartServices
{
    public static function makeOrderId()
    {

        return OrderCreateRepository::incrementOrderId();
    }
}
