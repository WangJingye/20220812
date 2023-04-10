<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/10
 * Time: 17:30
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ProdViewStatistics extends Model
{
    protected $table = "prod_view_statistics";

    //不允许更新的字段
    protected $guarded = [];
}