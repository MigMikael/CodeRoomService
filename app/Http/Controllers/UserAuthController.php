<?php

namespace App\Http\Controllers;
use App\Mail\ResetPassword;
use App\Student;
use App\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use App\Helper\TokenGenerate;
use Log;
use Mail;

class UserAuthController extends Controller
{
    use ImageTrait;
    public function __construct()
    {
        $this->middleware('web');
    }
    
    public function login(Request $request)
    {
        session_start();
        $username = $request->input('username');
        $password = $request->input('password');

        $student = Student::where('username', '=', $username)->first();
        $teacher = Teacher::where('username', '=', $username)->first();

        if($teacher != null){
            if(password_verify($password, $teacher->password)){
                $_SESSION['userID'] = $teacher->id;
                $_SESSION['userRole'] = $teacher->role;
                $_SESSION['time'] = Carbon::now();

                $teacher['role'] = $teacher->role;
                $teacher->makeVisible('token');
                $teacher->makeVisible('username');
                return $teacher;

            }else{
                return response()->json(['msg' => 'password is incorrect']);
            }

        }elseif($student != null){
            if(password_verify($password, $student->password)){

                /*if($student->ip != '' && $current_ip != $student->ip){
                    return response()->json(['msg' => 'you already login from another machine']);
                }*/

                $_SESSION['userID'] = $student->id;
                $_SESSION['userRole'] = 'student';
                $_SESSION['time'] = Carbon::now();

                //Log::info('userID : '.$_SESSION['userID']);
                //Log::info('userRole : '.$_SESSION['userRole']);

                $student['role'] = 'student';
                $student->makeVisible('token');
                $student->makeVisible('username');
                return $student;

            }else{
                return response()->json(['msg' => 'password is incorrect']);
            }

        }else{
            return response()->json(['msg' => 'username is incorrect']);
        }
    }

    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return response()->json(['msg' => 'logout complete']);
    }

    public function register()
    {
        return response()->json(['msg' => 'not available right now']);
    }

    public function registerUser(Request $request)
    {
        $email = $request->get('email');
        $student_id = $request->get('student_id');
        $name = $request->get('name');
        $username = $request->get('username');
        $password = $request->get('password');

        $p = explode('@', $email);
        if($p[1] != 'silpakorn.edu'){
            return response()->json(['msg' => 'please use university email']);
        }
        $student = Student::where('student_id', $student_id)->first();
        if(sizeof($student) == 1){
            return response()->json(['msg' => 'student code already exist']);
        }

        $student = Student::where('username', $username)->first();
        if(sizeof($student) == 1){
            return response()->json(['msg' => 'username already used']);
        }

        $image = self::genImage($student_id);

        $student = [
            'student_id' => $student_id,
            'name' => $name,
            'email' => $email,
            'image' => $image->id,
            'token' => (new TokenGenerate())->generate(32),
            'status' => 'enable',
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $student = Student::firstOrCreate($student);
        $student->makeVisible('token');
        $student->makeVisible('username');

        session_start();
        $_SESSION['userID'] = $student->id;
        $_SESSION['userRole'] = 'student';
        $_SESSION['time'] = Carbon::now();

        return $student;
    }

    public function resetPassword(Request $request)
    {
        $username = $request->get('username');
        $teacher = Teacher::where('username', $username)->first();
        if(sizeof($teacher) == 1){
            $tempPass = (new TokenGenerate())->generate(5);
            $teacher->password = password_hash($tempPass, PASSWORD_DEFAULT);
            $teacher->save();

            Mail::to($teacher->email)
                ->send(new ResetPassword($teacher->username, $tempPass));

            return response()->json(['msg' => 'reset password success']);

        }else{
            $student = Student::where('username', $username)->first();
            if(sizeof($student) == 1){
                $tempPass = (new TokenGenerate())->generate(5);
                $student->password = password_hash($tempPass, PASSWORD_DEFAULT);
                $student->save();

                if($student->email == ''){
                    return response()->json(['msg' => 'email not found']);
                }else{
                    Mail::to($student->email)
                        ->send(new ResetPassword($student->username, $tempPass));
                }

                return response()->json(['msg' => 'reset password success']);

            }else{
                return response()->json(['msg' => 'username not found']);
            }
        }
    }
}
