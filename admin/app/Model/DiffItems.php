<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class DiffItems extends Model
{
    protected $connection='order' ;
    
    protected $table="diff_items";
    
    protected $primaryKey = 'id';
    
    protected $guarded=[];
    
    
}
