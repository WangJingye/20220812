<?php

namespace App\Http\Middleware;

use Closure;

class ResponseMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $content = $response->getContent();
        
        $decodeContent = json_decode($content);

        if(is_array($decodeContent)){

        }


        return $response;
    }
}
