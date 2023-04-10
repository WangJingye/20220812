<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;


class Member extends Model
{
    protected $connection='customer' ;

    protected $table="crm_customers";

    protected $primaryKey = 'id';
    
    protected $guarded=[];

}
