<?php

namespace App\Http\Middleware;

use Closure;
use App\Teacher;

class TeacherAuthenticate
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
        $token = $request->header('Authorization_Token');
        $teacher = Teacher::where('token', '=', $token)->first();

        if($teacher == null){
            return response()->json(['status' => 'user unauthorized']);
        }

        return $next($request);
    }
}
