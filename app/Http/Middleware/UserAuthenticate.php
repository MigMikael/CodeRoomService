<?php

namespace App\Http\Middleware;
use App\Student;
use App\Teacher;
use Carbon\Carbon;
use Closure;
use Log;

class UserAuthenticate
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
        session_start();

        //handle session expire
        $time_now = Carbon::now();
        $diff = $time_now->diffInMinutes($_SESSION['time']);
        //Log::info('diff = '.$diff);
        if ($diff > 60){
            session_unset();
            session_destroy();
            return response()->json(['status' => 'session expired']);
        }else{
            $_SESSION['time'] = Carbon::now();
        }

        if($request->hasHeader('Authorization_Token')){
            $student = Student::where('token', '=', $request->header('Authorization_Token'))->first();

            if($student == null){
                $teacher = Teacher::where('token', '=', $request->header('Authorization_Token'))->first();

                if($teacher == null){
                    return response()->json(['status' => 'request unauthorized']);
                }
            }

        }else{
            return response()->json(['status' => 'request unauthorized']);
        }
        return $next($request);
    }
}
