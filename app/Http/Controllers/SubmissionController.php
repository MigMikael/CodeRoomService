<?php

namespace App\Http\Controllers;

use App\Submission;
use App\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;
use Log;

class SubmissionController extends Controller
{
    use FileTrait;
    public function store(Request $request)
    {
        $student_id = $request->get('student_id');
        $problem_id = $request->get('problem_id');

        $sub_num = self::getSubNum($student_id, $problem_id);
        $submission = [
            'student_id' => $student_id,
            'problem_id' => $problem_id,
            'sub_num' => $sub_num,
            'is_accept' => 'false'
        ];
        $submission = Submission::create($submission);

        if($request->hasFile('file')){
            $file = $request->file('file');
        }
        else{
            return response()->json(['msg' => 'file not found']);
        }

        $next_id = Submission::count();
        $file = self::storeFile($file);
        self::unzipSubmission($file, $next_id + 1);

        self::storeSubmissionFile($submission);
        
        /*$hasDriver = false;
        $problem = $submission->problem;
        $problemFiles = $problem->problemFiles;
        foreach ($problemFiles as $problemFile){
            if($problemFile->package == 'driver'){
                $hasDriver = true;
            }
        }*/

        return response()->json(['msg' => 'submit success']);
    }

    public function storeSubmissionFile($submission)
    {
        $src_path = 'problem/'.$submission->id.'/'. $submission->name. '/src';
        $files = self::getFiles($src_path);
        foreach ($files as $file){
            $code = self::getFile($file);

            $file = explode('/src/', $file);
            Log::info('#### '. $file[1]);

            if(strrpos($file[1], '/')) {
                $package = substr($file[1], 0, strrpos($file[1], '/'));

                $file_name = str_replace($package, '', $file[1]);

                $package = str_replace('/','.', $package);
            }
            else{
                $package = 'default package';
                $file_name = $file[1];
            }

            $submission_file = [
                'submission_id' => $submission->id,
                'package' => $package,
                'filename' => $file_name,
                'mime' => 'java',
                'code' => $code
            ];
            $submission_file = SubmissionFile::create($submission_file);


        }
    }

    public function getSubNum($student_id, $problem_id)
    {
        $sub_num = DB::table('submission')->where([
            ['student_id', '=', $student_id],
            ['problem_id', '=', $problem_id],
        ])->count();
        $sub_num++;

        return $sub_num;
    }
    
    public function result($problem_id, $student_id)
    {
        $data = [];
        $submission = Submission::where([
            ['student_id', '=', $student_id],
            ['problem_id', '=', $problem_id]
        ])->orderBy('id', 'desc')->first();

        if($submission != null){
            foreach ($submission->submissionFiles as $submissionFile){
                $submissionFile->outputs;

                foreach ($submissionFile->results as $result){
                    $result->score;
                    $result->attributes;
                    $result->constructors;
                    $result->methods;
                }
            }
        }else{
            $submission = [];
        }
        $data['submission'] = $submission;

        $problem = $submission->problem;
        foreach ($problem->problemFiles as $problemFile){
            $problemFile->code = '';
            foreach ($problemFile->problemAnalysis as $probAnalysis){
                $probAnalysis->score;
                $probAnalysis->attributes;
                $probAnalysis->constructors;
                $probAnalysis->methods;
            }
        }
        $data['problem'] = $problem;

        return $submission;
    }

    public function code($id)
    {
        $submissionFiles = SubmissionFile::where('submission_id', $id)->get();
        return $submissionFiles;
    }
}
