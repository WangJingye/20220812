<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsersTrue extends Model
{

    protected $table = 'tb_users_true';
//    public $timestamps = true;
//    protected $fillable = ['pos_id', 'phone', 'email','password','respect_type','nickname','name','birth','sex'];
    protected $guarded = ['id'];
//    public function hasManySocialUsers()
//    {
//        return $this->hasMany('App\Model\SocialUsers', 'user_id', 'id');
//    }
//
//    //获取用户收藏
//    public static function getUserInfo($user_id){
//        $data = self::where('id',$user_id)->first();
//        return json_decode(json_encode($data),true);
//    }

}
