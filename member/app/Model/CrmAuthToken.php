<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CrmAuthToken extends Model
{
    use CrmTokenTrait;

    protected $table = 'crm_auth_token';

    const STATUS_VALID = 0;

    const STATUS_USED = 1;

    public function scopeActive($query)
    {
    	return $query->where('status', self::STATUS_USED);
    }

    public function scopeExpiredAt($query)
    {
    	return $query->where('expired_at', '<', time());
    }
}