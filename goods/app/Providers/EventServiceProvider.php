<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Model\Goods\Sku;
use App\Observers\GoodsSku;
use App\Model\Goods\Spu;
use App\Observers\GoodsSpu;
use App\Model\Goods\Category;
use App\Observers\GoodsCateGory;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
        Sku::observe(GoodsSku::class);
        Spu::observe(GoodsSpu::class);
        Category::observe(GoodsCateGory::class);
    }
}
