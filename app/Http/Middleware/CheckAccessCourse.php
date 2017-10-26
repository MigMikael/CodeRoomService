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
        if($request->is('api/student/course/*')){
            $course_id = $request->route('id');
            $course = Course::findOrFail($course_id);

        }elseif ($request->is('api/student/lesson/*')){
            $lesson_id = $request->route('lesson_id');
            $lesson = Lesson::findOrFail($lesson_id);
            $course = $lesson->course;

        }elseif ($request->is('api/student/announcement/*')){
            $announce_id = $request->route('announce_id');
            $announce = Announcement::findOrFail($announce_id);
            $course = $announce->course;

        }elseif ($request->is('api/student/submission/*')){
            $problem_id = $request->route('problem_id');
            $problem = Problem::findOrFail($problem_id);
            $course = $problem->lesson->course;

        }else{
            return response()->json(['msg' => 'url not match']);
        }

        if($course->status == 'disable'){
            return response()->json(['msg' => 'not allow to access course']);
        }
        return $next($request);
    }
}
