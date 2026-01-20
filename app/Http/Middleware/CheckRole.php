<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $roles = $this->getRequiredRoleForRoute($request->route());

        if($request->user()->hasRole($roles) || !$roles){
            return $next($request);
        }
        Auth::logout();
        return redirect()->route('login');
        //return redirect('/admin');
    }

    private function getRequiredRoleForRoute($route){
        $action = $route->getAction();
        return isset($action['roles']) ? $action['roles'] : null;
    }
}
