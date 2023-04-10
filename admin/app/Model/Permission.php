<?php
namespace App\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    //子权限
    public function childs()
    {
        return $this->hasMany('App\Model\Permission','parent_id','id');
    }

}