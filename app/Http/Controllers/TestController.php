<?php

namespace App\Http\Controllers;

use App\Student;
use App\Lesson;
use Illuminate\Http\Request;
use Chumper\Zipper\Zipper;
use App\Traits\FileTrait;
use Illuminate\Support\Facades\Storage;
use Log;

class TestController extends Controller
{
    use FileTrait;

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
        /*session_start();
        if (isset($_SESSION['userID'])){
            $userID = $_SESSION['userID'];
        }else{
            $userID = 'null';
        }
        return 'store session complete : '.$userID;
        return Lesson::where('course_id', 1)->max('order') + 1;*/


        /*$identicon = new \Identicon\Identicon();
        $img = $identicon->getImageDataUri(45, 200);
        $des_path = storage_path() . '/app/';
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = $des_path . uniqid() . '.png';
        $success = file_put_contents($file, $data);
        return $success;*/

        $file = $request->file('file');
        $file = self::storeFile($file);

        $des_path = storage_path() . '\\app\\';
        $filePath = storage_path() . '\\app\\' . $file->name;

        $zipper = new Zipper();
        $zipper->make($filePath)->extractTo($des_path);

        return response()->json(['msg' => 'extract complete']);
    }

    public function test2()
    {
        /*$prob_name = 'LastDigit';
        $path = $prob_name . '/src' ;
        Log::info($path);
        $files = Storage::disk('local')->allFiles($path);
        return $files;*/

        $file = 'LastDigit/src/com/example/driver/LastDigit.java';
        $file = explode('/src/', $file);

        if(strrpos($file[1], '/')) {
            Log::info(strrpos($file[1], '/'));
            $package = substr($file[1], 0, strrpos($file[1], '/'));
            $package = str_replace('/','.', $package);
            return $package;
        }
        return 'default package';
    }
}
