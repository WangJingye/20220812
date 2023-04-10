<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OrderMail extends Model
{
    protected $connection='order' ;

    protected $table="order_emails";

    protected $primaryKey = 'id';
    
    protected $guarded=[];


}
