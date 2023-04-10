<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Footprint extends Model
{
//    use SoftDeletes;
    // 收藏列表
    protected $table = 'tb_footprint';

    protected $fillable = ['user_id', 'product_idx', 'create_at','update_at','type'];

    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
//    protected $hidden = [
//        'deleted_at',
//    ];

    //获取用户收藏
    public static function getUserFootprint($user_id){
        $data = self::where('user_id',$user_id)->orderBy('updated_at','desc')->get();
        return json_decode(json_encode($data),true);
    }

}
