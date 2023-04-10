<?php namespace App\Model\Dlc;

/**
 * @author Steven
 * Class DlcOmsSyncLog
 * @package App\Model
 */
class DlcOmsSyncLog extends \Illuminate\Database\Eloquent\Model
{
    //指定表名
    protected $table = 'oms_sync_log';
    protected $guarded = ['id'];

    const TYPE = [
        'price'=>'price',
        'stock'=>'stock',
    ];
}
