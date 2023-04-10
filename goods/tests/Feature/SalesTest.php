<?php

namespace Tests\Feature;

use App\Model\Goods\Spu;
use App\Model\Search\Product;
use App\Service\Dlc\UsersService;
use App\Service\Goods\ProductService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        (new \App\Jobs\OrderQueued)->salesVolume([
            ['sku'=>'skutest001','qty'=>1]
        ]);
        $this->assertTrue(true);
    }

}
