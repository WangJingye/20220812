<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $origin = $request->server('HTTP_ORIGIN');
        $allowOrigin = [
            'https://apiuat.dlc.com.cn',
            'https://www.dlc.com.cn',
            'http://localhost:8080',
            'https://uat.dlc.com.cn'
        ];
        $headers = [
           'Access-Control-Allow-Origin' => in_array($origin,$allowOrigin)?$origin:'',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
             'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => 'Origin, No-Cache, X-Requested-With, If-Modified-Since, Pragma, Last-Modified, Cache-Control, Expires, Content-Type, X-E4M-With,access-token,version,Token,token,refresh-token,from'
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
