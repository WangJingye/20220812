<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OmsStatusCircle extends Model
{
    protected $table = 'oms_status_circle';

    /**
     * 功能：判断状态变更合法性
     * @param int $current
     * @param int $next
     * @return bool
     */
    public static function checkStatus(int $current, int $next) : bool
    {
        $self = new static();
        $response = false;
        $self->where('current_status_id', $current)->get()->each(function($item, $key) use($next, &$response){
            if($item->next_status_id == $next) {
                $response = true;
                return false;
            }
        });
        return $response;
    }
}