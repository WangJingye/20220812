<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Pages extends Model
{
    protected $table="page";
    
    protected $guarded=[];

    public function selectCollection()
    {
    	return $this->hasOne('App\Model\Collection', 'page_id', 'id');
    	
    }
    public function element()
    {
    	return $this->hasMany('App\Model\Element', 'page_id', 'id');

    }

}
