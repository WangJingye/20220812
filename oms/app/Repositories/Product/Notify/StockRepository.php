<?php namespace App\Repositories\Product\Notify;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Exceptions\ApiPlaintextException;

class StockRepository
{
    public $stock_notify_table = 'connext_stock_notify';
    
    public function save($data){
        try {
            return DB::table($this->stock_notify_table)->insert($data);
        } catch (\Exception $e) {
            return '该商品您已设置了到货通知';
        }
        
    }
    
    //到货通知顾客
    public function notify($sku){
        $items = DB::table($this->stock_notify_table)
            ->select('mobile','id')
            ->where('send_flag',0)
            ->whereIn('sku',$sku)
            ->get();
        foreach($items as $item){
            $m = $item->mobile;
            $id = $item->id;
            $this->sendSms($m,'');
            DB::table($this->stock_notify_table)->where('id',$id)->update(['send_flag'=>1,'sended_at'=>date('y-m-d H:i:s'),'updated_at'=>date('y-m-d H:i:s')]);
        }
    }
    
    public function sendSms($mobile,$content=''){
        
    }
}
