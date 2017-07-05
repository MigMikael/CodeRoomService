<?php

namespace App\Http\Controllers;

use App\Course;
use App\Helper\TokenGenerate;
use App\Lesson;
use App\Student;
use App\StudentCourse;
use App\StudentLesson;
use App\TeacherCourse;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;

class CourseController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $courses = Course::enable()->get();
        return $courses;
    }

    public function store(Request $request)
    {
        $image = self::storeImage($request->file('image'));
        $course = [
            'name' => $request->get('name'),
            'color' => $request->get('color'),
            'token' => (new TokenGenerate())->generate(6),
            'image' => $image->id,
            'status' => 'enable',
            'mode' => 'normal',
        ];
        $course = Course::create($course);

        return $course;
    }

    public function member($id)
    {
        $course = Course::findOrFail($id);
        foreach ($course->students as $student){
            $student->pivot;
            $student->lessons;
        }
        foreach ($course->teachers as $teacher){
            $teacher->pivot;
        }

        return $course;
    }

    public function join(Request $request)
    {
        $course_id = $request->get('course_id');
        $student_id = $request->get('student_id');
        $token = $request->get('token');

        $course = Course::findOrFail($course_id);

        $student_course = StudentCourse::where([
            ['student_id', '=', $student_id],
            ['course_id', '=', $course_id]
        ])->first();

        if(sizeof($student_course) > 0){
            return response()->json(['msg' => 'already join this course']);
        }

        if($course->token == $token){
            $student_course = [
                'student_id' => $student_id,
                'course_id' => $course_id,
                'status' => 'enable',
                'progress' => 0
            ];

            StudentCourse::create($student_course);
            return response()->json(['msg' => 'join course success']);
        }else{
            return response()->json(['msg' => 'token mismatch']);
        }
    }

    public function showStudent(Request $request, $student_id, $course_id)
    {
        $course = Course::withCount([
            'students', 'teachers', 'lessons', 'badges', 'announcements'
        ])->findOrFail($course_id);


        if($course->mode == 'normal'){                  // normal mode
            $course['lessons'] = Lesson::where('course_id', $course_id)
                ->normal()
                ->ordered()
                ->get();
        }else{                                          // test mode
            $student = Student::findOrFail($student_id);
            $current_ip = $request->getClientIp();

            if($current_ip != $student->ip){
                return response()->json(['msg' => 'you already login from another machine']);
            }
            $course['lessons'] = Lesson::where('course_id', $course_id)
                ->test()
                ->orderBy('order', 'desc')
                ->first();
        }

        foreach ($course->lessons as $lesson){
            $student_lesson = StudentLesson::where([
                ['student_id', '=', $student_id],
                ['lesson_id', '=', $lesson->id]
            ])->first();

            if($student_lesson == null){
                $lesson['progress'] = 0;
            }else{
                $lesson['progress'] = $student_lesson->progress;
            }
            $lesson['problems_count'] = $lesson->problems()->count();
        }
        $course->badges;
        $course->announcements;

        return $course;
    }

    public function showTeacher($course_id)
    {
        $course = Course::withCount([
            'students', 'teachers', 'lessons', 'badges', 'announcements'
        ])->findOrFail($course_id);

        $course['lessons'] = Lesson::where('course_id', '=', $course_id)->ordered()->get();
        foreach ($course['lessons'] as $lesson){
            $lesson['problems_count'] = $lesson->problems()->count();
        }
        $course->badges;
        $course->announcements;
        $course->students;
        $course->makeVisible('token');

        return $course;
    }

    public function changeStatus($course_id)
    {
        $course = Course::findOrFail($course_id);
        if($course->status == 'enable'){
            $course->status = 'disable';
        }else{
            $course->status = 'enable';
        }
        $course->save();

        return response()->json(['msg' => 'change course status success']);
    }

    public function changeMode($course_id)
    {
        $course = Course::findOrFail($course_id);
        if($course->mode == 'normal'){
            $course->mode = 'test';
        }else{
            $course->mode = 'normal';
        }
        $course->save();

        return response()->json(['msg' => 'change course mode success']);
    }

    public function addTeacher(Request $request)
    {
        $course_id = $request->get('course_id');
        $course = Course::findOrFail($course_id);

        $teachers = $request->get('teachers');
        foreach ($teachers as $teacher){
            $teacher_course = [
                'teacher_id' => $teacher['id'],
                'course_id' => $course->id,
                'status' => 'enable'
            ];
            TeacherCourse::firstOrCreate($teacher_course);
        }

        return response()->json(['msg' => 'add Teacher success']);
    }

    public function teacherMember($course_id)
    {
        $course = Course::findOrFail($course_id);
        $teachers = $course->teachers;
        foreach ($teachers as $teacher){
            $teacher->makeHidden('role');
        }
        return $teachers;
    }
}
