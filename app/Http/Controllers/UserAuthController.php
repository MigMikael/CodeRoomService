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
                $_SESSION['userID'] = $student->id;
                $_SESSION['userRole'] = 'student';
                $_SESSION['time'] = Carbon::now();

                /*Log::info('userID : '.$_SESSION['userID']);
                Log::info('userRole : '.$_SESSION['userRole']);*/

                $current_ip = $request->getClientIp();
                $student->ip = $current_ip;
                $student->save();

                $student['role'] = 'student';
                $student->makeVisible('token');
                return $student;

            }else{
                return response()->json(['msg' => 'password is incorrect']);
            }

        }elseif($teacher != null){
            if(password_verify($password, $teacher->password)){
                $_SESSION['userID'] = $teacher->id;
                $_SESSION['userRole'] = 'teacher';
                $_SESSION['time'] = Carbon::now();

                $teacher['role'] = 'teacher';
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
        $userRole = $_SESSION['userRole'];
        if ($userRole == 'student'){
            $userID = $_SESSION['userID'];
            $student = Student::findOrFail($userID);
            $student->ip = '';
            $student->save();
        }

        session_unset();
        session_destroy();
        return response()->json(['msg' => 'logout complete']);
    }
}
