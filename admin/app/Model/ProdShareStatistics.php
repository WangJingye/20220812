<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/10
 * Time: 17:29
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ProdShareStatistics extends Model
{
    protected $table = "prod_share_statistics";

    //不允许更新的字段
    protected $guarded = [];
}