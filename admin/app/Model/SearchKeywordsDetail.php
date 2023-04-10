<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/2/9
 * Time: 15:25
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class SearchKeywordsDetail extends Model
{
    protected $table = "prod_keywords_statistics";

    //不允许更新的字段
    protected $guarded = [];
}