<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Diff extends Model
{
    protected $connection='order' ;
    
    protected $table="diff";
    
    protected $primaryKey = 'id';
    
    protected $guarded=[];
    
    
}
