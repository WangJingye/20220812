<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/11
 * Time: 11:35
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ProdViewByTypeStatistics extends Model
{
    protected $table = "prod_view_by_type_statistics";

    //不允许更新的字段
    protected $guarded = [];
}