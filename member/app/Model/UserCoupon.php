<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCoupon extends Model
{
//    use SoftDeletes;
    // 收藏列表
    protected $table = 'tb_user_coupon';


    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = ['user_id', 'coupon_id','type','received_at', 'created_at','updated_at'];

    //获取用户收藏
    public static function getUserCoupon($user_id){
        $data = self::where('user_id',$user_id)->orderBy('id','desc')->get();
        return json_decode(json_encode($data),true);
    }

    public static function cancelDefaultAddress($user_id){
//        self::where('user_id',)
    }

    public static function delUserAddress($user_id){
        $delNum = self::where('user_id',$user_id)->delete();
        return $delNum?:0;
    }


}
