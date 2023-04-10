<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        $this->redirectToDashBoard($request,$response);
        return $response;
    }

    //跳转到dashboard页面，如果是dashboard角色
    private function redirectToDashBoard($request,$response){
        $user = Auth::user();
        if($user){
            $dashboard_role = $user->hasRole(['dashboard',]);
            $dashboard_route = $request->route()->named('backend.dashboard.index');
            $dashboard_alldata_route = $request->route()->named('backend.dashboard.alldata');
            $dashboard_orderdata_route = $request->route()->named('backend.dashboard.analysis.orderReport');
            $allow_route = false;
            if($dashboard_route or $dashboard_alldata_route or $dashboard_orderdata_route){
                $allow_route = true;
            }
            if($dashboard_role and !$allow_route){
                $url = route('backend.dashboard.index');
                $response->headers->set('Location', $url);
            }
        }

    }
}
