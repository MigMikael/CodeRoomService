<?php

namespace App\Http\Middleware;

use App\Announcement;
use App\Course;
use App\Lesson;
use App\Problem;
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

        $token = $request->header('AuthorizationToken');
        $student = Student::where('token', $token)->first();

        $studentCourse = StudentCourse::where([
            ['course_id', $course->id],
            ['student_id', $student->id]
        ])->first();

        if ($studentCourse->status == 'disable'){
            return response()->json(['msg' => 'user banned form this course']);
        }

        return $next($request);
    }
}
