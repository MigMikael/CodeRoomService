<?php

namespace App\Http\Controllers;
use App\Student;
use App\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Log;

class UserAuthController extends Controller
{
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

        if($student != null){
            if(password_verify($password, $student->password)){

                /*if($student->ip != '' && $current_ip != $student->ip){
                    return response()->json(['msg' => 'you already login from another machine']);
                }*/

                $_SESSION['userID'] = $student->id;
                $_SESSION['userRole'] = 'student';
                $_SESSION['time'] = Carbon::now();

                /*Log::info('userID : '.$_SESSION['userID']);
                Log::info('userRole : '.$_SESSION['userRole']);*/

                $student['role'] = 'student';
                $student->makeVisible('token');
                return $student;

            }else{
                return response()->json(['msg' => 'password is incorrect']);
            }

        }elseif($teacher != null){
            if(password_verify($password, $teacher->password)){
                $_SESSION['userID'] = $teacher->id;
                $_SESSION['userRole'] = $teacher->role;
                $_SESSION['time'] = Carbon::now();

                $teacher['role'] = $teacher->role;
                $teacher->makeVisible('token');
                return $teacher;

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

    public function registerUser()
    {
        return response()->json(['msg' => 'not available right now']);
    }
}
