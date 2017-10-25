<?php

namespace App\Http\Middleware;

use App\Student;
use App\StudentCourse;
use Closure;

class CheckBannedUser
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
        $course_id = $request->route('id');
        $token = $request->header('AuthorizationToken');
        $student = Student::where('token', $token)->first();

        $studentCourse = StudentCourse::where([
            ['course_id', $course_id],
            ['student_id', $student->id]
        ])->first();

        if ($studentCourse->status == 'disable'){
            return response()->json(['msg' => 'user banned form this course']);
        }

        return $next($request);
    }
}
