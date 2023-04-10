<?php

namespace App\Observers;

use App\Model\Goods\Spu;
use Illuminate\Support\Facades\Log;

class GoodsSpu
{
    /**
     * Handle the nick users "created" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function created(Spu $spu)
    {
        Log::info('testAddSpu');
        $changed = $spu->isDirty() ? $spu->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'id':
                        if($spu->id) {
                            Spu::taskSpu($spu->id);
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
    public function updated(Spu $spu)
    {
        Log::info('testAddSpuUp');
        $changed = $spu->isDirty() ? $spu->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'product_name':
                        if($spu->product_name) {
                            Spu::taskSpu($spu->id);
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
