<?php

namespace App\Observers;

use App\Model\Goods\Sku;
use Illuminate\Support\Facades\Log;

class GoodsSku
{
    /**
     * Handle the nick users "created" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function created(Sku $sku)
    {
        Log::info('testAddSku');
        $changed = $sku->isDirty() ? $sku->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'sku_id':
                        if($sku->sku_id) {
                            Sku::taskSku($sku->id);
                            Sku::taskSalesInfo($sku->id);
                        }
                        break;
                    default:
                        //TODO
                }
                if ($break)  break;
            }
        }
    }

    /**
     * Handle the nick users "updated" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function updated(Sku $sku)
    {
        Log::info('testAddSku2');
        $changed = $sku->isDirty() ? $sku->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'sku_id':
                        //触发更改
                        if($sku->sku_id) {
                            Sku::taskSku($sku->id);
                        }
                        break;
                    default:
                        //TODO
                }
                if ($break)  break;
            }
        }
    }

    /**
     * Handle the nick users "deleted" event.
     *
     * @param  \App\Model\NickUsers  $nickUsers
     * @return void
     */
    public function deleted(Purchased $purchased)
    {
        //
    }

    /**
     * Handle the nick users "restored" event.
     *
     * @param  \App\Model\NickUsers  $nickUsers
     * @return void
     */
    public function restored(Purchased $purchased)
    {
        //
    }

    /**
     * Handle the nick users "force deleted" event.
     *
     * @param  \App\Model\NickUsers  $nickUsers
     * @return void
     */
    public function forceDeleted(Purchased $purchased)
    {
        //
    }
}
