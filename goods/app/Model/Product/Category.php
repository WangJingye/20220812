<?php

namespace App\Model\Product;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //指定表名
    protected $table = 'tb_categoty';
    protected $guarded = [];
}
