<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SaList extends Model
{

    protected $table = 'sa_list';
    protected $guarded = ['id'];

    public static function bindAll($params=[]){
        $model = self::query();
        if($phone = array_get($params,'phone')){
            $model->where('phone',$phone);
        }
        if($sid = array_get($params,'sid')){
            $model->where('sid',$sid);
        }
        $sa_list = $model->pluck('sid','phone');
        if($sa_list && ($sa_list = $sa_list->toArray())){
            DB::beginTransaction();
            try{
                $phones = array_keys($sa_list);
                $real_phones = [];
                \App\Model\Users::query()->whereIn('phone',$phones)->get()->each(function($item) use($sa_list,&$real_phones){
                    $item->update(['guid_id'=>$sa_list[$item->phone]]);
                    $real_phones[] = $item->phone;
                });
                self::query()->whereIn('phone',$real_phones)->update(['is_bind'=>1]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                return $e->getMessage();
            }
        }return true;
    }
}
