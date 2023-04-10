<?php
namespace App\Model;
use Illuminate\Database\Eloquent\Model;

class CatViewStatistics extends Model
{
    protected $table = "prod_view_by_cat_statistics";

    //不允许更新的字段
    protected $guarded = [];
}