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
        if ($request->hasHeader('Authorization_Token')){
            $token = $request->header('Authorization_Token');
            $student = Student::where('token', '=', $token)->first();

            $student->ip = $request->getClientIp();
            $student->save();

            if($student == null){
                return response()->json(['status' => 'user unauthorized']);
            }
        }
        return $next($request);
    }
}
