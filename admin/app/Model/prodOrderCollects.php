<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class prodOrderCollects extends Model
{
    protected $connection='order' ;
    
    protected $table="prod_order_collects";
    
    protected $primaryKey = 'id';
    
    protected $guarded=[];
    
    
}
