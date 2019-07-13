<?php

namespace App\Http\Middleware;

use Closure;
use App\Teacher;

class AdminAuthenticate
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
        $token = $request->header('AuthorizationToken');
        $teacher = Teacher::where('token', '=', $token)->first();

        if($teacher == null || $teacher->role != 'admin'){
            return response()->json(['status' => 'user unauthorized']);
        }
        return $next($request);
    }
}
