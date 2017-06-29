<?php

namespace App\Http\Middleware;

use App\Student;
use Closure;
use Log;

class StudentAuthenticate
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
        $student = Student::where('token', '=', $token)->first();

        $current_ip = $request->getClientIp();

        if($student->ip != $current_ip){
            return response()->json(['status' => 'you already login from another machine']);
        }

        if($student == null){
            return response()->json(['status' => 'user unauthorized']);
        }

        return $next($request);
    }
}
