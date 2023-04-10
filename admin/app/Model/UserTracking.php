<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserTracking extends Model
{
    protected $table = "user_tracking_info";

    //不允许更新的字段
    protected $guarded = [];

}
