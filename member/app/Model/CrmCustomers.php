<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CrmCustomers extends Model
{
    use ModelTrait;
    
    // 性别
    CONST GENDER = [
        'M' => '男',
        'F' => '女'
    ];

    // 入会来源
    CONST CHANNEL = [
        '1' => '电商入会',
        '2' => '老周友',
        '3' => '分店导购入会'
    ];

    // 会员等级
    CONST MEMBER_CLASS = [
        'FS' => '基本会员',
        'A1' => '尊尚会员',
        'A8' => '尊尚会员',
        'AA' => '高级会员',
        '01' => '尊尚会员',
        '02' => '员工会员',
        '06' => '过渡员工会员'
    ];

    // 称谓 
    CONST SALUTE = [
        '01' => '先生',
        '02' => '小姐',
        '03' => '女士',
        '04' => '太太',
    ];

    protected $table = 'crm_customers';

    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'deleted_at',
    ];
    // 性别
    public function getGenderAttribute($value)
    {
        return self::GENDER[$value];
    }

    // 会员等级
    public function getfromchannelAttribute($value)
    {
        return self::CHANNEL[$value];
    }

    // 会员等级
    public function getMemberClassAttribute($value)
    {
        return self::MEMBER_CLASS[$value];
    }

    // 会员等级
    public function getSaluteAttribute($value)
    {
        return self::SALUTE[$value];
    }

    public function wechatUser()
    {
        return $this->hasOne('App\Model\WechatUsers', 'id', 'wechat_user_id');
    }
}
