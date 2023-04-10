<?php

namespace App\Model\Promotion;


use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use Illuminate\Support\Facades\DB;

class Gift extends Model
{
    //指定表名
    protected $table = 'gift';
    protected $guarded = [];


    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
   
}
