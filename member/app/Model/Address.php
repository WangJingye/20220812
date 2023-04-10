<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
//    use SoftDeletes;
    // 收藏列表
    protected $table = 'tb_user_address';

    const MAX_ADDRESS_NUM = 15;

    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'deleted_at',
    ];

    protected $fillable = ['user_id', 'zip_code', 'sex','name','mobile','city','province','area','address','is_default'];

    //获取用户收藏
    public static function getUserAddress($user_id){
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

    /*
     * 转移地址
     * @params:
     *  将$slave_uid的账号转移到$master_uid
     * */
    public static function transferUserAddress($master_uid,$slave_uid){
        $upNum = self::where('user_id',$slave_uid)->update(['user_id'=>$master_uid]);
        return $upNum?:0;
    }

}
