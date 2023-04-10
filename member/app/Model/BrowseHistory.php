<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BrowseHistory extends Model
{
    // 浏览足迹
    protected $table = 'browse_history';

    /**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
    protected $hidden = [
        'deleted_at',
    ];

}
