<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Favorite extends Model
{
//    use SoftDeletes;
    // 收藏列表
    protected $table = 'tb_favorite';
    protected $fillable = ['user_id', 'product_idx', 'created_at','updated_at','type'];

    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
//    protected $hidden = [
//        'deleted_at',
//    ];

    //获取用户收藏
    public static function getUserFavorite($user_id){
        $data = self::where('user_id',$user_id)->orderBy('created_at','desc')->get();
        return json_decode(json_encode($data),true);
    }

}
