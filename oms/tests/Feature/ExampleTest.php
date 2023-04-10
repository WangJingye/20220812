<?php

namespace Tests\Feature;

use App\Jobs\OrderQueued as Queued;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Contracts\Bus\Dispatcher;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        app(Dispatcher::class)->dispatch(new Queued(
                'pendingRemind',
                ['orderId'=>1],300)
        );
    }
}
