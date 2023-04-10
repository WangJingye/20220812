<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/10
 * Time: 14:07
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;


class ProdAddCartStatistics extends Model
{
    protected $table = "prod_add_cart_statistics";

    //不允许更新的字段
    protected $guarded = [];
}