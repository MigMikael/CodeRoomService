<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\File;
use App\Problem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function question($id)
    {
        $problem = Problem::findOrFail($id);
        $question = File::findOrFail($problem->question);

        if (App::environment('local')) {
            $file = Storage::disk('local')->get($problem->name.'\\'.$question->name);

        }else{
            $file = Storage::disk('local')->get($problem->name.'/'.$question->name);
        }

        return response($file, 200)
            ->header('Content-Type', $question->mime);
    }
}
