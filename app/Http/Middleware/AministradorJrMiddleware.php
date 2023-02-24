<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class AministradorJrMiddleware
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
        if (Auth::check() && auth()->user()->rol == 1 || Auth::check() && auth()->user()->rol == 2){
            
            return $next($request);
        }else{
           
           return redirect('index');
        }
    }
}
