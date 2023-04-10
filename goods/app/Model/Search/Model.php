<?php
/**
 * Created by PhpStorm.
 * User: JIAN112
 * Date: 2019/10/31
 * Time: 21:42
 */

namespace App\Model\Search;


class Model extends \Illuminate\Database\Eloquent\Model
{
    public function getTableColumns()
    {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
    }

}