<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function start(Request $request, $value)
    {
        $request->session()->put('number', $value);

        $value = $request->session()->all();

        return response()->json(['msg' => $value]);
    }

    public function end(Request $request)
    {

        $value = $request->session()->all();

        return response()->json(['msg' => $value]);
    }

    public function testStudent()
    {
        $token = 'eDAs36X1d3TDH8tZVdchphucYusqZq9S';
        $student = Student::where('token', '=', $token)->first();
        return $student;
    }

    public function test(Request $request)
    {
        session_start();
        //$request->session()->put('number', 123);

        if (isset($_SESSION['userID'])){
            $userID = $_SESSION['userID'];
        }else{
            $userID = 'null';
        }
        return 'store session complete : '.$userID;
    }
}
