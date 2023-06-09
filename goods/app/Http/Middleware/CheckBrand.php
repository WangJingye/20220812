<?php

namespace App\Http\Middleware;

use Closure;
use Dotenv\Dotenv;

class CheckBrand
{


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        return $response;
    }

}