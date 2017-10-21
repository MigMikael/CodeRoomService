<?php

namespace App\Http\Controllers;

use App\File;
use App\Helper\TokenGenerate;
use App\Teacher;
use App\Traits\FileTrait;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use App\Student;
use App\Course;

class TeacherController extends Controller
{
    use ImageTrait, FileTrait;
    public function dashboard()
    {
        $userID = $_SESSION['userID'];

        $teacher = Teacher::findOrFail($userID);
        $teacher['courses'] = $teacher->courses()->withCount([
            'students', 'teachers', 'lessons',
        ])->get();

        foreach ($teacher['courses'] as $course){
            $c = Course::findOrFail($course->id);
            $prob_count = 0;
            foreach ($c->lessons as $lesson){
                $prob_count += $lesson->problems->count();
            }

            $course['problems_count'] = $prob_count;
        }

        $data = [];
        $data['teacher'] = $teacher;

        return $data;
    }

    public function showAdmin($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->makeVisible('token');
        $teacher->makeVisible('username');
        $teacher->makeVisible('password');

        return $teacher;
    }

    public function profile($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->courses;
        return $teacher;
    }

    public function store(Request $request)
    {
        $name = $request->get('name');
        $email = $request->get('email');

        $id = Teacher::count() + 1;
        $image = self::genImage($id);
        $teacher = [
            'name' => $name,
            'email' => $email,
            'image' => $image->id,
            'token' => (new TokenGenerate())->generate(32),
            'role' => 'teacher',
            'status' => 'enable',
            'username' => $email,
            'password' => password_hash($email, PASSWORD_DEFAULT),
        ];
        $teacher = Teacher::create($teacher);

        $student = [
            'student_id' => $teacher->id,
            'name' => $name,
            'email' => $email,
            'image' => $image->id,
            'token' => $teacher->token,
            'ip' => '',
            'status' => 'enable',
            'username' => $email,
            'password' => password_hash($email, PASSWORD_DEFAULT),
            'role' => 'hidden'
        ];
        Student::firstOrCreate($student);

        return response()->json(['msg' => 'create teacher success']);
    }

    public function update(Request $request)
    {
        $id = $request->get('id');
        $teacher = Teacher::findOrFail($id);

        $teacher->name = $request->get('name');
        $teacher->email = $request->get('email');
        $teacher->username = $request->get('username');
        $teacher->password = $request->get('password');

        if ($request->hasFile('image')){
            $image = File::findOrFail($teacher->image);
            self::deleteFile($image);
            $image->delete();

            $image = $request->file('image');
            $image = self::storeImage($image);
            $teacher->image = $image->id;
        }
        $teacher->save();

        return $teacher;
    }

    public function updateProfile(Request $request)
    {
        $id = $request->get('id');
        $teacher = Teacher::findOrFail($id);

        $name = $request->get('name');
        $email = $request->get('email');
        $username = $request->get('username');

        if ($request->hasFile('image')){
            $image = File::findOrFail($teacher->image);
            self::deleteFile($image);
            $image->delete();

            $image = $request->file('image');
            $image = self::storeImage($image);
            $teacher->image = $image->id;
        }

        $teacher->name = $name;
        $teacher->email = $email;
        $teacher->username = $username;
        $teacher->save();

        $teacher->makeVisible('token');
        $teacher->makeVisible('username');
        return $teacher;
    }

    public function changePassword(Request $request)
    {
        $teacher_id = $request->get('teacher_id');
        $old_pass = $request->get('old_password');
        $new_pass = $request->get('new_password');

        $teacher = Teacher::findOrFail($teacher_id);
        $current_pass = $teacher->password;

        if(password_verify($old_pass, $current_pass)){
            $teacher->password = password_hash($new_pass, PASSWORD_DEFAULT);
            $teacher->save();

            return response()->json(['msg' => 'change password complete']);

        }else{
            return response()->json(['msg' => 'password is incorrect']);
        }
    }

    public function changeStatus($teacher_id)
    {
        $teacher = Teacher::findOrFail($teacher_id);
        if($teacher->status == 'enable'){
            $teacher->status = 'disable';
        }else{
            $teacher->status = 'enable';
        }
        $teacher->save();

        return response()->json(['msg' => 'change teacher status success']);
    }

    public function getAll()
    {
        $teachers = Teacher::all();
        return $teachers;
    }
}
