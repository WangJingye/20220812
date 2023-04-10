<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/10
 * Time: 16:04
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class ProdFavoriteStatistics extends Model
{
    protected $table = "prod_favorite_statistics";

    //不允许更新的字段
    protected $guarded = [];
}