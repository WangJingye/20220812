<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SocialUsersBak extends Model
{
    protected $table = 'tb_social_relations_bak';
    protected $fillable = ['user_id', 'open_id', 'social_type','union_id'];


    public function belongsToUsers()
    {
        return $this->belongsTo('App\Model\Users', 'user_id', 'id');
    }



}
