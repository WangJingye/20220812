<?php

namespace App\Model\Promotion;


use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    //指定表名
    protected $table = 'log';
    protected $guarded = [];


    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
   
}
