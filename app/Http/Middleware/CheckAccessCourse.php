<?php

namespace App\Http\Middleware;

use App\Course;
use Closure;

class CheckAccessCourse
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
        $course = Course::findOrFail($course_id);

        if($course->status == 'disable'){
            return response()->json(['msg' => 'not allow to access course']);
        }
        return $next($request);
    }
}
