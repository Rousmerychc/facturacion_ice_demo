<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;

class ActivoMiddleware
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
        if (Auth::check()&&auth()->user()->estado == 1){
             return $next($request);
         }else{
            
            Auth::logout();
            return redirect('login');
         }
         
    }
}
