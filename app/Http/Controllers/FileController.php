<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;
use App\Problem;
use App\Resource;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function showResource($id)
    {
        $resource = Resource::where('file_id', $id)->first();
        $problem = Problem::where('id', $resource->problem_id)->first();
        $f = File::findOrFail($id);

        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);

        $pathToFile = storage_path('/app/'.$course_name.'/'.$problem->id.'/'.$problem->name.'/resource/'.$f->original_name);
        return response()->download($pathToFile, $f->original_name, ['Content-Type' => $f->mime]);
        //return response($file, 200)->header('Content-Type', $f->mime);
    }

    public function question($id)
    {
        $problem = Problem::findOrFail($id);
        $question = File::findOrFail($problem->question);
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);

        /*if (App::environment('local')) {
            $file = Storage::get($course_name.'\\'.$problem->id.'\\'.$problem->name.'\\'.$question->name);

        }else{
            $file = Storage::get($course_name.'/'.$problem->id.'/'.$problem->name.'/'.$question->name);
        }*/

        $pathToFile = storage_path('/app/'.$course_name.'/'.$problem->id.'/'.$problem->name.'/'.$question->name);
        return response()->download($pathToFile, $question->original_name, ['Content-Type' => $question->mime]);
        /*return response($file, 200)
            ->header('Content-Type', $question->mime);*/
    }
}
