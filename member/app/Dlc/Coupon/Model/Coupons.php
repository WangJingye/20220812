<?php

namespace App\Dlc\Coupon\Model;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Coupons extends Model
{
    protected $table = 'coupons';
    protected $guarded = ['id'];

    protected function serializeDate(DateTimeInterface $date){
        return $date->format($date->toDateTimeString());
    }

}
