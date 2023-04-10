<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2020/6/10
 * Time: 16:04
 */

namespace App\Model\Oms;


use Illuminate\Database\Eloquent\Model;

class OmsOrderItems extends Model
{
    protected $connection='oms' ;

    protected $table="oms_order_items";

    protected $primaryKey = 'id';

    protected $guarded=[];
}