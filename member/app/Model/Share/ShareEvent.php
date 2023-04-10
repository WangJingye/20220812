<?php namespace App\Model\Share;

use Illuminate\Database\Eloquent\Model;

class ShareEvent extends Model
{
    protected $table = 'share_event';

    protected $guarded = ['id'];

    const TYPE_REGISTER = 1;
    const TYPE_PAY = 2;

    protected $appends = ['type_name','status_name'];

    public function getTypeNameAttribute(){
        $type_names = [
            self::TYPE_REGISTER=>'邀新裂变',
            self::TYPE_PAY=>'下单裂变',
        ];
        return array_get($type_names,$this->type);
    }

    public function getStatusNameAttribute($value){
        return $this->status==1?'启用':'关闭';
    }
}
