<?php

namespace App\Http\Middleware;

use Closure;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $roles)
    {
        $roles = explode('|', $roles);
        $passed = [];

        if ($request->user()->isSysAdmin()) {
            $passed[] = in_array('sys-admin', $roles);
        }

        if ($request->user()->isClientAdmin()) {
            $passed[] = in_array('company-admin', $roles);
        }

        foreach ($passed as $p) {
            if ($p) {
                return $next($request);
            }
        }

        foreach ($roles as $role) {
            if (validate_role($role)) {
                return $next($request);
            }
        }

        // other permissions
        
        $route_name = $request->route()->getName();
        $prefix = substr($route_name, 0, strpos($route_name, '.'));
        
        if (validate_role($prefix.'.index')) {
            return $next($request);
        }

        return abort(401);
    }
}
