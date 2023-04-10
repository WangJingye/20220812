<?php

namespace App\Model\Goods;

class CateToProd extends Model
{
    /**
     * 该模型是否被自动维护时间戳.
     *
     * @var bool
     */
    public $timestamps = false;
    //指定表名
    protected $table = 'css_cate_prod_relation';
    protected $guarded = [];
}
