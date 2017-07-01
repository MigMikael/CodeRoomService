<?php

namespace App\Http\Controllers;

use App\Helper\TokenGenerate;
use App\Student;
use App\Course;
use App\StudentCourse;
use App\StudentLesson;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use App\Traits\FileTrait;
use Excel;
use Log;

class StudentController extends Controller
{
    use ImageTrait, FileTrait;

    public function dashboard()
    {
        $userID = $_SESSION['userID'];

        $student = Student::findOrFail($userID);
        $student['courses'] = $student->courses()->withCount([
            'students', 'teachers', 'lessons',
        ])->get();

        $data = [];
        $data['student'] = $student;

        $courses = Course::withCount([
            'students', 'teachers', 'lessons'
        ])->get();
        $data['courses'] = $courses;

        return $data;
    }

    public function profile($id)
    {
        $student = Student::findOrFail($id);
        foreach ($student->courses as $course){
            foreach ($course->lessons as $lesson){
                $student_lesson = StudentLesson::where([
                    ['student_id', '=', $student->id],
                    ['lesson_id', '=', $lesson->id]
                ])->first();

                if($student_lesson == null){
                    $lesson['progress'] = 0;
                }else{
                    $lesson['progress'] = $student_lesson->progress;
                }
            }
        }
        return $student->makeVisible('username');
    }

    public function updateProfile(Request $request)
    {
        $id = $request->get('id');
        $student = Student::findOrFail($id);

        $name = $request->get('name');
        $email = $request->get('email');
        $username = $request->get('username');

        $student->name = $name;
        $student->email = $email;
        $student->username = $username;
        $student->save();

        return response()->json(['msg' => 'edit complete']);
    }

    public function changePassword(Request $request)
    {
        $student_id = $request->get('student_id');
        $old_pass = $request->get('old_password');
        $new_pass = $request->get('new_password');

        $student = Student::findOrFail($student_id);
        $current_pass = $student->password;

        if(password_verify($old_pass, $current_pass)){
            $student->password = password_hash($new_pass, PASSWORD_DEFAULT);
            $student->save();

            return response()->json(['msg' => 'change password complete']);

        }else{
            return response()->json(['msg' => 'password is incorrect']);
        }
    }

    public function addMember(Request $request)
    {
        $course_id = $request->get('course_id');
        $student_id = $request->get('student_id');
        $name = $request->get('name');

        $std_count = Student::where('student_id', $student_id)->count();
        if($std_count == 0){
            // student already in db
            $student = Student::where('student_id', $student_id)->first();
        }else{
            $id = Student::count() + 1;
            $image = self::genImage($id);

            $student = [
                'student_id' => $student_id,
                'name' => $name,
                'email' => '',
                'image' => $image->id,
                'token' => (new TokenGenerate())->generate(32),
                'status' => 'enable',
                'username' => $student_id,
                'password' => password_hash($student_id, PASSWORD_DEFAULT)
            ];
            $student = Student::firstOrCreate($student);
        }

        $studentCourse = [
            'student_id' => $student->id,
            'course_id' => $course_id,
            'status' => 'enable',
        ];
        StudentCourse::firstOrCreate($studentCourse);

        return response()->json(['msg' => 'add student success']);
    }

    public function addMembers(Request $request)
    {
        $course_id = $request->get('course_id');
        $studentListFile = $request->file('studentList');

        $file = self::storeFile($studentListFile);
        $path = self::path($file);

        $data = Excel::load($path, function ($reader){
        })->get();

        $students = [];
        if(!empty($data) && $data->count()){
            Log::info('###### '.sizeof($data));
            foreach ($data as $key => $value) {
                $id = $value->id;
                $id = str_replace(' ', '', $id);
                $students[] = ['student_id' => $id, 'name' => $value->name];
                Log::info('###### '. $id.' '.$value->name);
            }
        }else{
            return response()->json(['msg' => 'error in data File']);
        }

        $token = new TokenGenerate();
        foreach ($students as $student){
            $id = Student::count() + 1;
            $image = self::genImage($id);

            $student['email'] = '';
            $student['image'] = $image->id;
            $student['token'] = $token->generate(32);
            $student['ip'] = '';
            $student['status'] = 'enable';
            $student['username'] = $student['student_id'];
            $student['password'] = password_hash($student['student_id'], PASSWORD_DEFAULT);
            $student = Student::firstOrCreate($student);

            $studentCourse = [
                'student_id' => $student->id,
                'course_id' => $course_id,
                'status' => 'enable',
                'progress' => 0,
            ];
            StudentCourse::firstOrCreate($studentCourse);
        }

        return response()->json(['msg' => 'add students success']);
    }

    public function disable($student_id, $course_id)
    {
        $student_course = StudentCourse::where([
            ['student_id', '=', $student_id],
            ['course_id', '=', $course_id]
        ])->first();

        if($student_course->status == 'enable'){
            $student_course->status = 'disable';
        }else{
            $student_course->status = 'enable';
        }
        $student_course->save();

        return response()->json(['msg' => 'change status success']);
    }

    public function getAll($course_id)
    {
        $data = [];

        $students = Student::all();
        $data['students'] = $students;

        $course = Course::findOrFail($course_id);
        $data['students_course'] = $course->students;

        return $data;
    }

    public function removeIP($id)
    {
        $student = Student::findOrFail($id);
        $student->ip = '';
        $student->save();
        return response()->json(['msg' => 'remove ip complete']);
    }
}
