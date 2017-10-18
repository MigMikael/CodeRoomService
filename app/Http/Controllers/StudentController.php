<?php

namespace App\Http\Controllers;

use App\File;
use App\Helper\TokenGenerate;
use App\Student;
use App\Course;
use App\StudentCourse;
use App\StudentLesson;
use App\Submission;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use App\Traits\FileTrait;
use Excel;
use Log;
use Storage;
use Zipper;

class StudentController extends Controller
{
    use ImageTrait, FileTrait;

    public function dashboard()
    {
        if (!isset($_SESSION['userID'])){
            return response()->json(['msg' => 'please login']);
        }
        $userID = $_SESSION['userID'];

        $student = Student::findOrFail($userID);

        $student['courses'] = $student->courses()->withCount([
            'students', 'teachers', 'lessons',
        ])->get();

        foreach ($student['courses'] as $course){
            $c = Course::findOrFail($course->id);
            $prob_count = 0;
            foreach ($c->lessons as $lesson){
                $prob_count += $lesson->problems->count();
            }

            $course['problems_count'] = $prob_count;
        }

        $data = [];
        $data['student'] = $student;

        $courses = Course::withCount([
            'students', 'teachers', 'lessons'
        ])->get();

        foreach ($courses as $course){
            $problems_count = 0;
            foreach ($course->lessons as $lesson){
                $problems_count += $lesson->problems->count();
            }
            $course['problems_count'] = $problems_count;

            $student_course = StudentCourse::where([
                ['student_id', $student->id],
                ['course_id', $course->id]
            ])->first();

            if(sizeof($student_course) > 0){
                $course['progress'] = $student_course->progress;
            }else{
                $course['progress'] = 0;
            }
        }
        $data['courses'] = $courses;

        return $data;
    }

    public function profile($student_id)
    {
        $student = Student::where('student_id', $student_id)->firstOrFail();
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

        if ($request->hasFile('image')){
            $image = File::findOrFail($student->image);
            self::deleteFile($image);
            $image->delete();

            $image = self::storeImage($request->file('image'));
            $student->image = $image->id;
        }

        $student->name = $name;
        $student->email = $email;
        $student->username = $username;
        $student->save();

        $student->makeVisible('token');
        $student->makeVisible('username');
        return $student;
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
                $id = '0'.$id;
                $students[] = ['student_id' => $id, 'name' => $value->name];
                //Log::info('###### '. $id.' '.$value->name);
            }
        }else{
            return response()->json(['msg' => 'error in data File']);
        }

        $token = new TokenGenerate();
        foreach ($students as $student){
            $id = Student::count() + 1;
            $image = self::genImage($id);

            // check if student already exist in DB
            $curr_student = Student::where('student_id', $student['student_id'])->first();
            if(sizeof($curr_student) < 1){
                $student['email'] = '';
                $student['image'] = $image->id;
                $student['token'] = $token->generate(32);
                $student['ip'] = '';
                $student['status'] = 'enable';
                $student['username'] = $student['student_id'];
                $student['password'] = password_hash($student['student_id'], PASSWORD_DEFAULT);
                $student = Student::firstOrCreate($student);
            }else{
                $student = $curr_student;
            }

            $course = Course::findOrFail($course_id);
            $studentCourse = [
                'student_id' => $student->id,
                'course_id' => $course->id,
                'status' => 'enable',
                'progress' => 0,
            ];
            StudentCourse::firstOrCreate($studentCourse);
            foreach ($course->lessons as $lesson){
                $studentLesson = [
                    'student_id' => $student->id,
                    'lesson_id' => $lesson->id,
                    'progress' => 0
                ];
                StudentLesson::firstOrCreate($studentLesson);
            }
        }

        return response()->json(['msg' => 'add students success']);
    }

    public function disable($id, $course_id)
    {
        $student_course = StudentCourse::where([
            ['student_id', '=', $id],
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

    public function removeAllIP($course_id)
    {
        $course = Course::findOrFail($course_id);
        $students = $course->students;
        foreach ($students as $student){
            $student->ip = '';
            $student->save();
        }

        return response()->json(['msg' => 'remove ip complete']);
    }

    public function submissionCode($id)
    {
        $submission = Submission::find($id);
        if(sizeof($submission) < 1){
            return response()->json(['msg' => 'submission code not found']);
        }

        $student = $submission->student;
        $problem = $submission->problem;
        $now = str_replace(' ', '_', Carbon::now());
        $folderName = $problem->name.'_'.$student->student_id.'_'.$now.'/src/';

        foreach ($submission->submissionFiles as $submissionFile){
            $packageName = '';
            if ($submissionFile->package != 'default package') {
                $temps = explode('.', $submissionFile->package);
                foreach ($temps as $temp) {
                    $packageName .= $temp . '/';
                }
            }
            if($submissionFile->package != 'driver'){
                Storage::put($folderName.$packageName.$submissionFile->filename, $submissionFile->code);
            }
        }
        $folderName = str_replace('/src/', '', $folderName);
        $files = storage_path().'/app/'.$folderName;

        $theName = $problem->name.'_'.$student->student_id.'_'.$now.'.zip';
        $des_path = storage_path().'/app/'.$theName;

        $zipper = new Zipper;
        $zipper->make($des_path)->add($files)->close();

        Log::info($folderName);
        Storage::deleteDirectory($folderName);

        $view_able_name = $problem->name.'_'.$student->student_id.'.zip';
        return response()->download(storage_path().'/app/'.$theName, $view_able_name)
            ->deleteFileAfterSend(true);
    }
}
