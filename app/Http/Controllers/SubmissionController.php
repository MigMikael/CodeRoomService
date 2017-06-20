<?php

namespace App\Http\Controllers;

use App\Submission;
use App\SubmissionFile;
use App\SubmissionOutput;
use App\ProblemFile;
use App\ProblemInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;
use App\Traits\EvaluatorTrait;
use Log;

class SubmissionController extends Controller
{
    use FileTrait, EvaluatorTrait;
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
        self::unzipSubmission($file, $next_id);

        $submit_success = self::storeSubmissionFile($submission);
        if(!$submit_success){
            return response()->json(['msg' => 'submit file not found']);
        }

        $problem = $submission->problem;
        $hasDriver = self::checkDriver($problem);
        $currentVer = self::getCurrentVersion($problem);

        if(!$hasDriver){
            // this submit in problem that not have driver
            $data = self::checkInputVersion($problem);
            if($data['in'] == null || $data['in'][0]['version'] != $currentVer){
                self::sendNewInput($problem);
            }

            $data = self::checkOutputVersion($problem);
            if($data['sol'] == null || $data['sol'][0]['version'] != $currentVer){
                self::sendNewOutput($problem);
            }

            // send Student Code to Evaluator
            $score = self::evaluateFile($submission);
            self::saveScore($score, $submission);
        }
        return response()->json(['msg' => 'submit success']);
    }

    public function storeSubmissionFile($submission)
    {
        $src_path = 'submission/'.$submission->id.'/'. $submission->problem->name. '/src';
        $files = self::getFiles($src_path);
        if(sizeof($files) == 0){
            // file not found
            return false;
        }

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
            SubmissionFile::create($submission_file);
        }
        return true;
    }

    public function saveScore($scores, $submission)
    {
        $submissionFiles = $submission->submissionFiles;
        $isAccept = true;
        foreach ($submissionFiles as $submissionFile){
            $problemFile = ProblemFile::where('filename', '=', $submissionFile->filename)->first();
            //Log::info('##### '. $problemFile->filename);
            $problemOutputNum = ProblemInput::where('problem_file_id', '=', $problemFile->id)->count();

            if($problemOutputNum > 0){
                foreach ($scores as $score){
                    if($score['score'] != 100){
                        $isAccept = false;
                    }
                    $submissionOutput = [
                        'submission_file_id' => $submissionFile->id,
                        'content' => '',
                        'score' => $score['score'],
                        'error' => '',
                    ];
                    SubmissionOutput::create($submissionOutput);
                }
            }
        }
        if ($isAccept == true){
            $submission->is_accept = 'true';
        }else{
            $submission->is_accept = 'false';
        }
        $submission->save();
    }

    public function checkDriver($problem)
    {
        $hasDriver = false;
        $problemFiles = $problem->problemFiles;
        foreach ($problemFiles as $problemFile){
            if($problemFile->package == 'driver'){
                $hasDriver = true;
            }
        }

        return $hasDriver;
    }

    public function getCurrentVersion($problem)
    {
        $currentVersion = 0;
        $problemFiles = $problem->problemFiles;
        foreach ($problemFiles as $problemFile){
            if(sizeof($problemFile->inputs) > 0){
                $input = $problemFile->inputs()->first();
                $currentVersion = $input->version;
            }
        }

        return $currentVersion;
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
