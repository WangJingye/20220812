<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
	 /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = "config";

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];


}
