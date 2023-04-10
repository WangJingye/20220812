<?php

namespace App\Model\Promotion;


class Model extends \Illuminate\Database\Eloquent\Model
{
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }
    
    
   
    

    

}
