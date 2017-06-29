<?php

namespace App\Http\Controllers;

use App\Result;
use App\ResultAttribute;
use App\ResultConstructor;
use App\ResultMethod;
use App\StudentLesson;
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
        elseif ($request->hasFile('files')){
            $file = $request->file('files');
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
        if ($problem->is_parse == 'true'){
            foreach ($submission->submissionFiles as $submissionFile){
                $classes = self::analyzeFile($submissionFile);
                self::saveResult($classes, $submissionFile);
                self::calStructureScore($submissionFile);
            }
        }

        $hasDriver = self::checkDriver($problem);
        $currentVer = self::getCurrentVersion($problem);

        if(!$hasDriver) {
            // this submit in problem that not have driver
            $data = self::checkInputVersion($problem, $hasDriver);
            if ($data['in'] == null || $data['in'][0]['version'] != $currentVer) {
                self::sendNewInput($problem);
            }

            $data = self::checkOutputVersion($problem, $hasDriver);
            if ($data['sol'] == null || $data['sol'][0]['version'] != $currentVer) {
                self::sendNewOutput($problem);
            }

            // send Student Code to Evaluator
            $scores = self::evaluateFile($submission);
            self::saveScore($scores, $submission);

        }else{
            $data = self::checkInputVersion($problem, $hasDriver);
            if ($data['in'] == null || $data['in'][0]['version'] != $currentVer) {
                self::sendNewInput2($problem);
            }

            $data = self::checkOutputVersion($problem, $hasDriver);
            if ($data['sol'] == null || $data['sol'][0]['version'] != $currentVer) {
                self::sendNewOutput2($problem);
            }

            self::sendDriver($problem);
            $scores = self::evaluateFile2($submission);
            self::saveScore2($scores, $submission);
        }

        return response()->json(['msg' => 'submit success']);
        //return $scores;
    }

    public function store2(Request $request)
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
        $files = $request->get('files');
        $submit_success = self::storeSubmissionFile2($submission, $files);

        if(!$submit_success){
            return response()->json(['msg' => 'submit file not found']);
        }

        $problem = $submission->problem;
        if ($problem->is_parse == 'true'){
            foreach ($submission->submissionFiles as $submissionFile){
                $classes = self::analyzeFile($submissionFile);
                self::saveResult($classes, $submissionFile);
                self::calStructureScore($submissionFile);
            }
        }

        $hasDriver = self::checkDriver($problem);
        $currentVer = self::getCurrentVersion($problem);

        if(!$hasDriver) {
            // this submit in problem that not have driver
            $data = self::checkInputVersion($problem, $hasDriver);
            if ($data['in'] == null || $data['in'][0]['version'] != $currentVer) {
                self::sendNewInput($problem);
            }

            $data = self::checkOutputVersion($problem, $hasDriver);
            if ($data['sol'] == null || $data['sol'][0]['version'] != $currentVer) {
                self::sendNewOutput($problem);
            }

            // send Student Code to Evaluator
            $scores = self::evaluateFile($submission);
            self::saveScore($scores, $submission);

        }else{
            $data = self::checkInputVersion($problem, $hasDriver);
            if ($data['in'] == null || $data['in'][0]['version'] != $currentVer) {
                self::sendNewInput2($problem);
            }

            $data = self::checkOutputVersion($problem, $hasDriver);
            if ($data['sol'] == null || $data['sol'][0]['version'] != $currentVer) {
                self::sendNewOutput2($problem);
            }

            self::sendDriver($problem);
            $scores = self::evaluateFile2($submission);
            self::saveScore2($scores, $submission);
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
                $package = str_replace('/','.', $package);

                $file_name = str_replace($package.'/', '', $file[1]);
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

    public function storeSubmissionFile2($submission, $files)
    {
        for($i = 0; $i < sizeof($files); $i++){
            $file = $files[$i];
            $f = [
                'submission_id' => $submission->id,
                'package' => $file['package'],
                'filename' => $file['filename'],
                'mime' => $file['mime'],
                'code' => $file['code'],
            ];
            SubmissionFile::create($f);
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
            $submission->save();
            self::updateProgress($submission);
        }else{
            $submission->is_accept = 'false';
            $submission->save();
        }

    }

    public function saveScore2($scores, $submission)
    {
        $problem = $submission->problem;
        $problemFiles = $problem->problemFiles;

        $isAccept = true;
        foreach ($problemFiles as $problemFile){
            if($problemFile->package == 'driver'){
                $submissionFile = [
                    'submission_id' => $submission->id,
                    'package' => $problemFile->package,
                    'filename' => $problemFile->filename,
                    'mime' => $problemFile->mime,
                    'code' => 'driver from teacher',
                ];
                $submissionFile = SubmissionFile::create($submissionFile);
                $temps = explode('.',$submissionFile->filename);
                $fileName = $temps[0];

                foreach ($scores as $score){
                    if($score['name'] == $fileName){
                        if($score != 100){
                            $isAccept = false;
                        }
                        $output = [
                            'submission_file_id' => $submissionFile->id,
                            'content' => '',
                            'score' => $score['score'],
                            'error' => '',
                        ];
                        SubmissionOutput::create($output);
                        //Log::info('#### '.$output->submissionfile_id);
                    }
                }
            }
        }
        if ($isAccept == true){
            $submission->is_accept = 'true';
            $submission->save();
        }else{
            $submission->is_accept = 'false';
            $submission->save();
        }
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

    public function saveResult($classes, $submissionFile)
    {
        foreach ($classes['class'] as $class){
            $im = '';
            foreach ($class['implements'] as $implement){
                $im .= $implement['name'];
            }

            $result = [
                'submission_file_id' => $submissionFile->id,
                'class' => $class['modifier'].';'.$class['static_required'].';'.$class['name'],
                'enclose' => $class['enclose'],
                'extends' => $class['extends'],
                'implements' => $im,
            ];
            $result = Result::create($result);

            foreach ($class['attribute'] as $attribute){
                $att = [
                    'result_id' => $result->id,
                    'access_modifier' => $attribute['modifier'],
                    'non_access_modifier' => $attribute['static_required'],
                    'data_type' => $attribute['datatype'],
                    'name' => $attribute['name']
                ];
                ResultAttribute::create($att);
            }

            foreach ($class['constructure'] as $constructor){
                $pa = '';
                foreach ($constructor['params'] as $param){
                    $pa .= $param['datatype'].';'.$param['name'].'|';
                }

                $con = [
                    'result_id' => $result->id,
                    'access_modifier' => $constructor['modifier'],
                    'name' => $constructor['name'],
                    'parameter' => $pa
                ];
                ResultConstructor::create($con);
            }

            foreach ($class['method'] as $method){
                $pa = '';
                foreach ($method['params'] as $param){
                    $pa .= $param['datatype'].';'.$param['name'].'|';
                }

                $me = [
                    'result_id' => $result->id,
                    'access_modifier' => $method['modifier'],
                    'non_access_modifier' => $method['static_required'],
                    'return_type' => $method['return_type'],
                    'name' => $method['name'],
                    'parameter' => $pa,
                    'recursive' => $method['recursive'],
                    'loop' => $method['loop_exist']
                ];
                ResultMethod::create($me);
            }
        }
    }

    public function calStructureScore($submissionFile)
    {
        $submission = $submissionFile->submission;
        $problem = $submission->problem;


    }

    public function updateProgress($submission)
    {
        $problem = $submission->problem;
        $student = $submission->student;

        $lesson = $problem->lesson;
        $student_lesson = StudentLesson::where([
            ['student_id', $student->id],
            ['lesson_id', $lesson->id]
        ])->first();

        $prob_count = $lesson->problems->count();

        $accept_count = 0;
        foreach ($lesson->problems as $problem){
            $accept_submissions = Submission::where([
                ['student_id', $student->id],
                ['problem_id', $problem->id]
            ])->get();

            foreach ($accept_submissions as $accept_submission){
                if($accept_submission->is_accept = 'true'){
                    $accept_count++;
                }
            }
        }

        $progress = ($accept_count/$prob_count)*100;

        $student_lesson->progress = $progress;
        $student_lesson->save();
    }
}
