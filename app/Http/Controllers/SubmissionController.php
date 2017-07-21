<?php

namespace App\Http\Controllers;

use App\ProblemAnalysis;
use App\ProblemAttribute;
use App\ProblemConstructor;
use App\ProblemMethod;
use App\Result;
use App\ResultAttribute;
use App\ResultConstructor;
use App\ResultMethod;
use App\ResultScore;
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
                $classes = self::analyzeSubmitFile($submissionFile);
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

        $wrong = [];
        $problem = $submission->problem;
        if ($problem->is_parse == 'true'){
            Log::info('is_parse : true');
            $classes = self::analyzeSubmitFile2($submission);

            foreach ($submission->submissionFiles as $submissionFile){
                self::saveResult($classes, $submissionFile);
                $wrong = self::calStructureScore($submissionFile);
            }

            $results_classname = [];
            foreach ($submission->submissionFiles as $submissionFile){
                foreach ($submissionFile->results as $result) {
                    array_push($results_classname, $result->class);
                    Log::info('result : '. $result->class);
                }
            }

            $problemAnalysis_classname = [];
            foreach ($problem->problemFiles as $problemFile){
                foreach ($problemFile->problemAnalysis as $analysis) {
                    array_push($problemAnalysis_classname, $analysis->class);
                    Log::info('analysis : '. $analysis->class);
                }
            }

            $class_diffs = $problemAnalysis_classname->diff($results_classname);
            Log::info(print_r($class_diffs, true));
            foreach ($class_diffs as $diff){
                array_push($wrong, 'ไม่มีคลาส '.$diff);
            }
        }

        $hasTestCase = self::checkTestCase($problem);
        if ($hasTestCase){
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
        }

        foreach ($submission->submissionFiles as $submissionFile){
            $submissionFile->outputs;

            $problem = $submission->problem;
            foreach ($problem->problemFiles as $problemFile){
                foreach ($problemFile->problemAnalysis as $analysis){
                    $analysis->score;
                    $analysis->attributes;
                    $analysis->constructors;
                    $analysis->methods;
                }
            }

            foreach ($submissionFile->results as $result){
                $result->score;
                $result->attributes;
                $result->constructors;
                $result->methods;
            }
        }

        $submission['wrong'] = $wrong;
        return $submission;

        //return response()->json(['msg' => 'submit success']);
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
                'mime' => $file['mime'],
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
                    if($score['score'] != '100.000000'){
                        $isAccept = false;
                        $output = [
                            'submission_file_id' => $submissionFile->id,
                            'content' => '',
                            'score' => 0,
                            'error' => $score['score'],
                        ];
                    }else{
                        $output = [
                            'submission_file_id' => $submissionFile->id,
                            'content' => '',
                            'score' => $score['score'],
                            'error' => '',
                        ];
                    }
                    SubmissionOutput::create($output);
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
                        /*Log::info(gettype($score['score']));*/
                        if($score['score'] != '100.000000'){
                            // this is wrong
                            $isAccept = false;
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => 0,
                                'error' => $score['score'],
                            ];
                        }else{
                            // this is correct
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => $score['score'],
                                'error' => '',
                            ];
                        }
                        SubmissionOutput::create($output);
                    }
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

    /*public function checkTestCase($problem)
    {
        $hasTestCase = false;
        foreach ($problem->problemFiles as $problemFile){
            $size = $problemFile->inputs()->count();
            if($size > 0){
                $hasTestCase = true;
            }
        }

        return $hasTestCase;
    }*/

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

        /*$problem = [];
        if(sizeof($submission->problem) > 0){
            $problem = $submission->problem;
            $problemFiles = $problem->problemFiles;
            foreach ($problemFiles as $problemFile){
                $problemFile->code = '';
                foreach ($problemFile->problemAnalysis as $probAnalysis){
                    $probAnalysis->score;
                    $probAnalysis->attributes;
                    $probAnalysis->constructors;
                    $probAnalysis->methods;
                }
            }
        }

        $data['problem'] = $problem;*/

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
            $filename = explode('.', $submissionFile->filename);
            $filename = $filename[0];

            if($class['name'] == $filename && $class['package'] == $submissionFile->package){
                $im = '';
                foreach ($class['implements'] as $implement){
                    $im .= $implement['name'];
                }

                $result = [
                    'submission_file_id' => $submissionFile->id,
                    'class' => $class['modifier'].';'.$class['static_required'].';'.$class['name'],
                    'package' => $submissionFile->package,
                    'enclose' => $class['enclose'],
                    'extends' => $class['extends'],
                    'implements' => $im,
                ];
                $result = Result::create($result);
                if($result->enclose == 'null'){
                    $result->enclose = '';
                    $result->save();
                }
                if($result->extends == 'null'){
                    $result->extends = '';
                    $result->save();
                }

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
    }

    public function calStructureScore($submissionFile)
    {
        $submission = $submissionFile->submission;
        $problem = $submission->problem;
        $wrong = [];

        foreach ($submissionFile->results as $result){
            $ps = ProblemAnalysis::where('class', '=', $result->class)->get();

            $problemAnalysis = null;
            foreach ($ps as $p){
                $this_problem = $p->problemFile->problem;
                if($problem->id == $this_problem->id){
                    $problemAnalysis = $p;
                }
            }

            if($problemAnalysis != null){
                $class_score = $problemAnalysis->score->class;
                if($result->package == $problemAnalysis->package){
                    $package_score = $problemAnalysis->score->package;
                }else{
                    $package_score = 0;
                    array_push($wrong, $result->class.' มี package '.$result->package . ' ไม่ตรง');
                }

                if($result->enclose == $problemAnalysis->enclose){
                    $enclose_score = $problemAnalysis->score->enclose;
                }else{
                    $enclose_score = 0;
                    array_push($wrong, $result->class.' มี enclose '.$result->enclose . ' ไม่ตรง');
                }

                if($result->extends == $problemAnalysis->extends){
                    $extends_score = $problemAnalysis->score->extends;
                }else{
                    $extends_score = 0;
                    array_push($wrong, $result->class.' มี extends '.$result->extends . ' ไม่ตรง');
                }

                if($result->implements == $problemAnalysis->implements){
                    $implements_score = $problemAnalysis->score->extends;
                }else{
                    $implements_score = 0;
                    array_push($wrong, $result->class.' มี implements '.$result->implements . ' ไม่ตรง');

                }
            }else{
                $class_score = 0;
                $package_score = 0;
                $enclose_score = 0;
                $extends_score = 0;
                $implements_score = 0;
            }
            $result_score = [
                'result_id' => $result->id,
                'class' => $class_score,
                'package' => $package_score,
                'enclose' => $enclose_score,
                'extends' => $extends_score,
                'implements' => $implements_score
            ];
            /*Log::info('-------------------------------------------------------------');
            Log::info('##### CLASS NAME'. $result->class);
            Log::info('-------------------------------------------------------------');
            Log::info('##### CLASS SCORE '. $class_score);
            Log::info('##### ENCLOSE SCORE '. $enclose_score);
            Log::info('##### EXTENDS SCORE '. $extends_score);
            Log::info('##### IMPLEMENTS SCORE '. $implements_score);
            Log::info('-------------------------------------------------------------');*/
            ResultScore::create($result_score);

            foreach ($result->attributes as $attribute){
                $problem_attributes = ProblemAttribute::where('name', '=', $attribute->name)
                    ->orderBy('id', 'desc')
                    ->get();
                $prob_attr = null;
                foreach ($problem_attributes as $problem_attribute){
                    $this_problem = $problem_attribute->problemAnalysis->problemFile->problem;
                    if($problem->id == $this_problem->id){
                        $prob_attr = $problem_attribute;
                        break;
                    }else{
                        $prob_attr = null;
                    }
                }

                $correct = true;
                if($prob_attr != null){
                    if($attribute->access_modifier != $prob_attr->access_modifier){
                        $correct = false;
                    } elseif ($attribute->non_access_modifier != $prob_attr->non_access_modifier){
                        $correct = false;
                    } elseif ($attribute->data_type != $prob_attr->data_type){
                        $correct = false;
                    }
                }else{
                    $correct = false;
                }

                if($correct){
                    $attribute->score = $prob_attr->score;
                }else{
                    $attribute->score = 0;
                    array_push($wrong, $result->class.' มี attribute '.$attribute->name . ' ไม่ตรง');
                }
                $attribute->save();
                /*Log::info('Attribute IS CORRECT '. $correct);

                Log::info('-------------------------------------------------------------');
                Log::info('##### ATTRIBUTE NAME'. $attribute->name);
                Log::info('-------------------------------------------------------------');
                Log::info('##### ATTRIBUTE Access Modifier :'. $attribute->access_modifier.'5555');
                Log::info('##### ATTRIBUTE Non Access Modifier '. $attribute->non_access_modifier);
                Log::info('##### ATTRIBUTE Data Type '. $attribute->data_type);


                Log::info('##### P ATTRIBUTE NAME'. $prob_attr->name);
                Log::info('-------------------------------------------------------------');
                Log::info('##### P ATTRIBUTE Access Modifier :'. $prob_attr->access_modifier.'5555');
                Log::info('##### P ATTRIBUTE Non Access Modifier '. $prob_attr->non_access_modifier);
                Log::info('##### P ATTRIBUTE Data Type '. $prob_attr->data_type);
                Log::info('-------------------------------------------------------------');*/
            }

            foreach ($result->constructors as $constructor){
                $problem_constructors = ProblemConstructor::where('name', '=', $constructor->name)
                    ->orderBy('id', 'desc')
                    ->get();
                $prob_con = null;
                foreach ($problem_constructors as $problem_constructor){
                    $this_problem = $problem_constructor->problemAnalysis->problemFile->problem;
                    if($problem->id == $this_problem->id){
                        $prob_con = $problem_constructor;
                        break;
                    }else{
                        $prob_con = null;
                    }
                }

                $is_correct = true;
                if($prob_con != null){
                    if($constructor->access_modifiler != $prob_con->access_modifiler){
                        $is_correct = false;
                    } elseif ($constructor->parameter != $prob_con->parameter){
                        $is_correct = false;
                    }
                }else {
                    $is_correct = false;
                }

                if($is_correct){
                    $constructor->score = $prob_con->score;
                }else{
                    $constructor->score = 0;
                    array_push($wrong, $result->class.' มี constructor '.$constructor->name . ' ไม่ตรง');

                }
                $constructor->save();
                //Log::info('Constructor IS CORRECT '. $is_correct);
            }

            foreach ($result->methods as $method){
                $problem_methods = ProblemMethod::where('name', '=', $method->name)
                    ->orderBy('id', 'desc')
                    ->get();
                $prob_me = null;
                foreach ($problem_methods as $problem_method){
                    $this_problem = $problem_method->problemAnalysis->problemFile->problem;
                    if($problem->id == $this_problem->id){
                        $prob_me = $problem_method;
                        break;
                    }else{
                        $prob_me = null;
                    }
                }

                $is_correct = true;
                if($prob_me != null){
                    if($method->access_modifiler != $prob_me->access_modifiler){
                        $is_correct = false;
                    } elseif ($method->non_access_modifiler != $prob_me->non_access_modifiler){
                        $is_correct = false;
                    } elseif ($method->return_type != $prob_me->return_type){
                        $is_correct = false;
                    } elseif ($method->recursive != $prob_me->recursive){
                        $is_correct = false;
                    } elseif ($method->loop != $prob_me->loop){
                        $is_correct = false;
                    } elseif ($method->parameter != $prob_me->parameter){
                        $is_correct = false;
                    }
                } else {
                    $is_correct = false;
                }

                if($is_correct){
                    $method->score = $prob_me->score;
                }else{
                    $method->score = 0;
                    array_push($wrong, $result->class.' มี method '.$method->name . ' ไม่ตรง');
                }
                $method->save();
                //Log::info('Method IS CORRECT '. $is_correct);
            }
        }
        return $wrong;
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

        if(sizeof($student_lesson) == 0){
            $student_lesson = [
                'student_id' => $student->id,
                'lesson_id' => $lesson->id,
                'progress' => 0
            ];
            $student_lesson = StudentLesson::create($student_lesson);
        }
        $prob_count = $lesson->problems->count();

        $accept_count = 0;
        foreach ($lesson->problems as $problem){
            $accept_submission = Submission::where([
                ['student_id', $student->id],
                ['problem_id', $problem->id],
                ['is_accept', 'true']
            ])->first();

            if(sizeof($accept_submission) > 0){
                $accept_count++;
            }
        }

        Log::info('Accept Count : '.$accept_count);
        Log::info('Prob Count : '.$prob_count);
        $progress = ($accept_count/$prob_count)*100;

        $student_lesson->progress = $progress;
        $student_lesson->save();
    }
}
