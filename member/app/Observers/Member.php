<?php

namespace App\Observers;

use App\Model\Users;
use Illuminate\Support\Facades\Log;

class Member
{
    /**
     * Handle the nick users "created" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function created(Users $users)
    {
    }

    /**
     * Handle the nick users "updated" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function updated(Users $users)
    {

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
