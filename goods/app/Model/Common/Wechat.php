<?php

namespace App\Model\Common;

class Wechat extends \Illuminate\Database\Eloquent\Model
{
    //指定表名
    protected $table = 'wechat_access_token';
    protected $guarded = [];
}
