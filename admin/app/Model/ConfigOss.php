<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ConfigOss extends Model
{
	 /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = "config_oss";

    /**
     * 不可被批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = ['access_key_id', 'access_key_secret', 'endpoint', 'bucket', 'active'];


    public static function checkWork()
    {
        $data = ConfigOss::find(1);
        $status = 0;
        if($data) {
            $status = $data->active;
        }
        // 验证OSS可用状态
        return $status;
    }

}
