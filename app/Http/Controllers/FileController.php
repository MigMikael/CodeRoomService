<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;
use App\Problem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function show($id)
    {
        $f = File::findOrFail($id);
        $file = Storage::disk('local')->get($f->name);
        return response($file, 200)->header('Content-Type', $f->mime);
    }

    public function question($id)
    {
        $problem = Problem::findOrFail($id);
        $question = File::findOrFail($problem->question);
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);

        if (App::environment('local')) {
            $file = Storage::get($course_name.'\\'.$problem->id.'\\'.$problem->name.'\\'.$question->name);

        }else{
            $file = Storage::get($course_name.'/'.$problem->id.'/'.$problem->name.'/'.$question->name);
        }

        return response($file, 200)
            ->header('Content-Type', $question->mime);
    }
}
