<?php

namespace App\Http\Middleware;

use Closure;

class CheckServiceMode
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
        $appMode = env('APP_MODE', 'ON');
        if($appMode == 'OFF'){
            return response()->json(['status' => 'service is OFF']);
        }

        return $next($request);
    }
}
