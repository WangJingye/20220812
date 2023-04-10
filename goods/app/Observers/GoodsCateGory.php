<?php

namespace App\Observers;

use App\Model\Goods\Category;
use Illuminate\Support\Facades\Log;

class GoodsCateGory
{
    /**
     * Handle the nick users "created" event.
     *
     * @param  \App\Model\Sku  $sku
     * @return void
     */
    public function created(Category $category)
    {
        Log::info('taskProductCategories');
        $changed = $category->isDirty() ? $category->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'id':
                        if($category->id) {
                            Category::taskProductCategories($category->id);
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
    public function updated(Category $category)
    {
        Log::info('taskProductCategories2');
        $changed = $category->isDirty() ? $category->getDirty() : false;
        if($changed) {
            foreach($changed as $attr => $val){
                $break = false;
                switch ($attr){
                    case 'cat_name': case 'parent_cat_id':
                        //触发更改
                        if($category->id) {
                            Category::taskProductCategories($category->id);
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
