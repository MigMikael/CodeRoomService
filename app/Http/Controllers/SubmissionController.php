<?php

namespace App\Http\Controllers;

use App\ProblemAnalysis;
use App\ProblemAttribute;
use App\ProblemConstructor;
use App\ProblemMethod;
use App\ProblemScore;
use App\Result;
use App\ResultAttribute;
use App\ResultConstructor;
use App\ResultMethod;
use App\ResultScore;
use App\Student;
use App\StudentLesson;
use App\Submission;
use App\SubmissionFile;
use App\SubmissionOutput;
use App\ProblemFile;
use App\Problem;
use App\ProblemInput;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;
use App\Traits\EvaluatorTrait;
use App\Traits\DatabaseTrait;
use Log;

class SubmissionController extends Controller
{
    use FileTrait, EvaluatorTrait, DatabaseTrait;

    public $wrong = [];

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
            'is_accept' => 'true'
        ];
        $submission = Submission::create($submission);
        $code = $request->get('code');
        $submit_success = self::storeSubmissionFile2($submission, $code);

        if(!$submit_success){
            return response()->json(['msg' => 'submit file not found']);
        }

        $problem = $submission->problem;

        if ($problem->is_parse == 'true'){
            //Log::info('is_parse : true');
            $classes = self::analyzeSubmitFile2($submission);

            $res = self::saveResult($classes, $submission->submissionFiles);
            if($res == 'analysis error'){
                //Log::info($res);
                return response()->json(['msg' => $res]);
            }

            foreach ($submission->submissionFiles as $submissionFile){
                self::calStructureScore2($submissionFile);
            }

            $results_classname = [];
            foreach ($submission->submissionFiles as $submissionFile){
                foreach ($submissionFile->results as $result) {
                    array_push($results_classname, $result->class);
                    //Log::info('result : '. $result->class);
                }
            }

            $problemAnalysis_classname = [];
            foreach ($problem->problemFiles as $problemFile){
                foreach ($problemFile->problemAnalysis as $analysis) {
                    array_push($problemAnalysis_classname, $analysis->class);
                    //Log::info('analysis : '. $analysis->class);
                }
            }

            $class_diffs = array_diff($problemAnalysis_classname, $results_classname);
            //Log::info(print_r($class_diffs, true));
            foreach ($class_diffs as $diff){
                array_push($this->wrong, 'ไม่มีคลาส '.$diff);
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
                if(sizeof($scores) == 0){
                    array_push($this->wrong, 'ผลลัพธ์ผิดในทุกชุดข้อมูลทดสอบ');
                }
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
        if(sizeof($this->wrong) > 0){
            $submission->is_accept = 'false';
            $submission->save();
        }
        $student = Student::findOrFail($student_id);
        if($student->role == 'student'){
            self::updateStudentProgress($submission);
        }

        $submission['wrong'] = $this->wrong;
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
            //Log::info('#### '. $file[1]);

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

    public function storeSubmissionFile2($submission, $code)
    {
        for($i = 0; $i < sizeof($code); $i++){
            $files = $code[$i]['files'];
            for($j = 0; $j < sizeof($files); $j++){
                $file = $files[$j];
                $package = str_replace('src/', '', $file['package']);
                $package = str_replace('/', '.', $package);

                $f = [
                    'submission_id' => $submission->id,
                    'package' => $package,
                    'filename' => $file['filename'],
                    'mime' => $file['mime'],
                    'code' => $file['code'],
                ];
                SubmissionFile::create($f);
            }
        }

        return true;
    }

    public function saveScore($scores, $submission)
    {
        $submissionFiles = $submission->submissionFiles;
        foreach ($submissionFiles as $submissionFile){
            $problemFile = ProblemFile::where('filename', '=', $submissionFile->filename)->first();

            if(sizeof($problemFile) != 1){
                $problemOutputNum = 1;
            }else{
                $problemOutputNum = ProblemInput::where('problem_file_id', '=', $problemFile->id)->count();
            }

            if($problemOutputNum > 0){
                $count = 1;
                //Log::info(print_r($scores, true));
                if(isset($scores['score']) && $scores['score'] == 'Complie Error'){
                    $output = [
                        'submission_file_id' => $submissionFile->id,
                        'content' => '',
                        'score' => 0,
                        'error' => 'Compile Error',
                    ];
                    SubmissionOutput::create($output);
                    array_push($this->wrong, 'ไม่สามารถรันโค้ดได้');
                }else{
                    foreach ($scores as $score){
                        if($score['score'] == '100.000000'){
                            // this is correct
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => $score['score'],
                                'error' => '',
                            ];
                        }elseif ($score['score'] == 'Exited with error status 1'){
                            // this is time limit exceed
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => 0,
                                'error' => 'Memory Limit Exceed',
                            ];
                            array_push($this->wrong, 'ผลลัพธ์ผิดในชุดข้อมูลทดสอบที่ '.$count);
                        }else{
                            // this is wrong
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => 0,
                                'error' => '-',
                            ];
                            array_push($this->wrong, 'ผลลัพธ์ผิดในชุดข้อมูลทดสอบที่ '.$count);
                        }
                        $o = SubmissionOutput::create($output);
                        $submission->score += $o->score;
                        $count++;
                    }
                }
            }
        }
        $submission->save();
    }

    public function saveScore2($scores, $submission)
    {
        $problem = $submission->problem;
        $problemFiles = $problem->problemFiles;

        $count = 1;
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
                        if(isset($scores['score']) && $scores['score'] == 'Complie Error'){
                            $output = [
                                'submission_file_id' => $submissionFile->id,
                                'content' => '',
                                'score' => 0,
                                'error' => 'Compile Error',
                            ];
                            SubmissionOutput::create($output);
                            array_push($this->wrong, 'ไม่สามารถรันโค้ดได้');
                        }else{
                            if($score['score'] == '100.000000'){
                                // this is correct
                                $output = [
                                    'submission_file_id' => $submissionFile->id,
                                    'content' => '',
                                    'score' => $score['score'],
                                    'error' => '',
                                ];
                            }elseif ($score['score'] == 'Exited with error status 1'){
                                // this is time limit exi
                                $output = [
                                    'submission_file_id' => $submissionFile->id,
                                    'content' => '',
                                    'score' => 0,
                                    'error' => 'Memory Limit Exceed',
                                ];
                                array_push($this->wrong, 'ผลลัพธ์ผิดในชุดข้อมูลทดสอบที่ '.$count);
                            }elseif ($score['score'] == 'Compile Error'){
                                // this is time limit exceed
                                $output = [
                                    'submission_file_id' => $submissionFile->id,
                                    'content' => '',
                                    'score' => 0,
                                    'error' => 'Compile Error',
                                ];
                                array_push($this->wrong, 'ไม่สามารถรันโค้ดได้');
                            }else{
                                // this is wrong
                                $output = [
                                    'submission_file_id' => $submissionFile->id,
                                    'content' => '',
                                    'score' => 0,
                                    'error' => '-',
                                ];
                                array_push($this->wrong, 'ผลลัพธ์ผิดในชุดข้อมูลทดสอบที่ '.$count);
                            }
                            $o = SubmissionOutput::create($output);
                            $submission->score += $o->score;
                            $count++;
                        }
                    }
                }
            }
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
        foreach ($problem->problemFiles as $problemFile){
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

        $score = [
            'class' => 0,
            'package' => 0,
            'enclose' => 0,
            'extends' => 0,
            'implements' => 0,
            'attribute' => 0,
            'constructor' => 0,
            'method' => 0,
        ];

        $total_score = [
            'class' => 0,
            'package' => 0,
            'enclose' => 0,
            'extends' => 0,
            'implements' => 0,
            'attribute' => 0,
            'constructor' => 0,
            'method' => 0,
        ];

        if($submission != null){
            foreach ($submission->submissionFiles as $submissionFile){
                $submissionFile->outputs;

                foreach ($submissionFile->results as $result){
                    $result->score;
                    $result->attributes;
                    $result->constructors;
                    $result->methods;
                }

                foreach ($submissionFile->results as $result){
                    $result_score = ResultScore::where('result_id', $result->id)->first();
                    if(sizeof($result_score) == 1){
                        $score['class'] += $result_score->class;
                        $score['package'] += $result_score->package;
                        $score['enclose'] += $result_score->enclose;
                        $score['extends'] += $result_score->extends;
                        $score['implements'] += $result_score->implements;

                        foreach ($result->attributes as $attribute){
                            $score['attribute'] += $attribute->score;
                        }
                        foreach ($result->constructors as $constructor){
                            $score['constructor'] += $constructor->score;
                        }
                        foreach ($result->methods as $method){
                            $score['method'] += $method->score;
                        }
                    }
                }
            }

            $problem = $submission->problem;
            $problemFiles = ProblemFile::where([
                ['problem_id', '=', $problem->id],
                ['package', '!=', 'driver']
            ])->get();
            //Log::info("Problem File Size " . sizeof($problemFiles));
            foreach ($problemFiles as $problemFile){
                foreach ($problemFile->problemAnalysis as $analysis){
                    $problem_score = ProblemScore::where('analysis_id', $analysis->id)->first();
                    $total_score['class'] += $problem_score->class;
                    $total_score['package'] += $problem_score->package;
                    $total_score['enclose'] += $problem_score->enclose;
                    $total_score['extends'] += $problem_score->extends;
                    $total_score['implements'] += $problem_score->implements;

                    foreach ($analysis->attributes as $attribute){
                        $total_score['attribute'] += $attribute->score;
                    }
                    foreach ($analysis->constructors as $constructor){
                        $total_score['constructor'] += $constructor->score;
                    }
                    foreach ($analysis->methods as $method){
                        $total_score['method'] += $method->score;
                    }
                }
            }
        }else{
            $submission = [];
        }

        $submission['score'] = $score;
        $submission['total_score'] = $total_score;
        $data['submission'] = $submission;

        return $submission;
    }

    public function code($id)
    {
        $submissionFiles = SubmissionFile::where('submission_id', $id)->get();
        return $submissionFiles;
    }

    public function saveResult($classes, $submissionFiles)
    {
        if($classes['class'] == null){
            return 'analysis error';
        }

        foreach ($classes['class'] as $class){
            foreach ($submissionFiles as $submissionFile){
                $filename = explode('.', $submissionFile->filename);
                $filename = $filename[0];

                if($class['package'] == 'default'){
                    $class['package'] = 'default package';
                }

                $class_file = strpos($submissionFile->code, 'class ' . $class['name'] . ' ');

                /*Log::info('class_file '.$class_file);

                Log::info('class_package '.$class['package']);
                Log::info('submit_package '.$submissionFile->package);

                Log::info('strlen class name '.strlen($class['name']));
                Log::info('strlen file name '. strlen($filename));*/

                if($class_file == true && $class['package'] == $submissionFile->package){
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

                        if ($method['recursive'] == 0){
                            $method['recursive'] = 'false';
                        }else{
                            $method['recursive'] = 'true';
                        }


                        if ($method['loop_exist'] == 0){
                            $method['loop_exist'] = 'false';
                        }else{
                            $method['loop_exist'] = 'true';
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

        return 'analysis success';
    }

    public function calStructureScore($submissionFile)
    {
        $submission = $submissionFile->submission;
        $problem = $submission->problem;

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
                    array_push($this->wrong, $result->class.' มี package '.$result->package . ' ไม่ตรง');
                }

                if($result->enclose == $problemAnalysis->enclose){
                    $enclose_score = $problemAnalysis->score->enclose;
                }else{
                    $enclose_score = 0;
                    array_push($this->wrong, $result->class.' มี enclose '.$result->enclose . ' ไม่ตรง');
                }

                if($result->extends == $problemAnalysis->extends){
                    $extends_score = $problemAnalysis->score->extends;
                }else{
                    $extends_score = 0;
                    array_push($this->wrong, $result->class.' มี extends '.$result->extends . ' ไม่ตรง');
                }

                if($result->implements == $problemAnalysis->implements){
                    $implements_score = $problemAnalysis->score->extends;
                }else{
                    $implements_score = 0;
                    array_push($this->wrong, $result->class.' มี implements '.$result->implements . ' ไม่ตรง');
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
            $rs = ResultScore::create($result_score);
            $submission->score += $rs->class + $rs->package + $rs->enclose + $rs->extends + $rs->implements;

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
                    array_push($this->wrong, $result->class.' มี attribute '.$attribute->name . ' ไม่ตรง');
                }
                $attribute->save();
                $submission->score += $attribute->score;
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
                    array_push($this->wrong, $result->class.' มี constructor '.$constructor->name . ' ไม่ตรง');

                }
                $constructor->save();
                $submission->score += $constructor->score;
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
                    array_push($this->wrong, $result->class.' มี method '.$method->name . ' ไม่ตรง');
                }
                $method->save();
                $submission->score += $method->score;
                //Log::info('Method IS CORRECT '. $is_correct);
            }
        }
    }

    public function calStructureScore2($submissionFile)
    {
        $submission = $submissionFile->submission;
        $problem = $submission->problem;

        foreach ($problem->problemFiles as $problemFile){
            $problem_analysis = $problemFile->problemAnalysis;
            foreach ($problem_analysis as $analysis){
                $rs = Result::where('class', '=', $analysis->class)->get();

                $result = null;
                foreach ($rs as $r){
                    $this_problem = $r->submissionFile->submission->problem;
                    if ($problem->id == $this_problem->id){
                        $result = $r;
                    }
                }

                if($result != null){
                    $class_score = $analysis->score->class;

                    if($result->package == $analysis->package){
                        $package_score = $analysis->score->package;
                    }else{
                        $package_score = 0;
                        array_push($this->wrong, $result->class.' มี package '.$result->package . ' ไม่ตรง');
                    }

                    if($result->enclose == $analysis->enclose){
                        $enclose_score = $analysis->score->enclose;
                    }else{
                        $enclose_score = 0;
                        array_push($this->wrong, $result->class.' มี enclose '.$result->enclose . ' ไม่ตรง');
                    }

                    if($result->extends == $analysis->extends){
                        $extends_score = $analysis->score->extends;
                    }else{
                        $extends_score = 0;
                        array_push($this->wrong, $result->class.' มี extends '.$result->extends . ' ไม่ตรง');
                    }

                    if($result->implements == $analysis->implements){
                        $implements_score = $analysis->score->extends;
                    }else{
                        $implements_score = 0;
                        array_push($this->wrong, $result->class.' มี implements '.$result->implements . ' ไม่ตรง');
                    }
                }else{
                    $class_score = 0;
                    $package_score = 0;
                    $enclose_score = 0;
                    $extends_score = 0;
                    $implements_score = 0;

                    $result = Result::where('submission_file_id', $submissionFile->id)->first();
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

                $rs = ResultScore::create($result_score);
                $submission->score += $rs->class + $rs->package + $rs->enclose + $rs->extends + $rs->implements;

                foreach ($analysis->attributes as $attribute){
                    $result_attributes = ResultAttribute::where('name', '=', $attribute->name)
                        ->orderBy('id', 'desc')
                        ->get();
                    $result_attr = null;
                    foreach($result_attributes as $result_attribute){
                        $this_problem = $result_attribute->result->submissionFile->submission->problem;
                        if($problem->id == $this_problem->id){
                            $result_attr = $result_attribute;
                            break;
                        }else{
                            $result_attr = null;
                        }
                    }

                    $correct = true;
                    if($result_attr != null){
                        if($attribute->access_modifier != $result_attr->access_modifier){
                            $correct = false;
                        } elseif ($attribute->non_access_modifier != $result_attr->non_access_modifier){
                            $correct = false;
                        } elseif ($attribute->data_type != $result_attr->data_type){
                            $correct = false;
                        }
                    }else{
                        $correct = false;
                        $result_attr = new ResultAttribute;
                        $result_attr->result_id = $result->id;
                        $result_attr->access_modifier = ' ';
                        $result_attr->non_access_modifier = ' ';
                        $result_attr->data_type = ' ';
                        $result_attr->name = ' ';
                    }

                    if($correct){
                        $result_attr->score = $attribute->score;
                    }else{
                        $result_attr->score = 0;
                        array_push($this->wrong, $result->class.' มี attribute '.$result_attr->name . ' ไม่ตรง');
                    }
                    $result_attr->save();
                    $submission->score += $result_attr->score;
                    /*Log::info('Attribute IS CORRECT '. $correct);

                    Log::info('-------------------------------------------------------------');
                    Log::info('##### ATTRIBUTE NAME'. $attribute->name);
                    Log::info('-------------------------------------------------------------');
                    Log::info('##### ATTRIBUTE Access Modifier :'. $attribute->access_modifier.'5555');
                    Log::info('##### ATTRIBUTE Non Access Modifier '. $attribute->non_access_modifier);
                    Log::info('##### ATTRIBUTE Data Type '. $attribute->data_type);


                    Log::info('##### P ATTRIBUTE NAME'. $result_attr->name);
                    Log::info('-------------------------------------------------------------');
                    Log::info('##### P ATTRIBUTE Access Modifier :'. $result_attr->access_modifier.'5555');
                    Log::info('##### P ATTRIBUTE Non Access Modifier '. $result_attr->non_access_modifier);
                    Log::info('##### P ATTRIBUTE Data Type '. $result_attr->data_type);
                    Log::info('-------------------------------------------------------------');*/
                }

                foreach ($analysis->constructors as $constructor){
                    $result_constructors = ResultConstructor::where('name', '=', $constructor->name)
                        ->orderBy('id', 'desc')
                        ->get();
                    $result_con = null;
                    foreach ($result_constructors as $result_constructor){
                        $this_problem = $result_constructor->result->submissionFile->submission->problem;
                        if($problem->id == $this_problem->id){
                            $result_con = $result_constructor;
                            break;
                        }else{
                            $result_con = null;
                        }
                    }

                    $is_correct = true;
                    if($result_con != null){
                        if($constructor->access_modifiler != $result_con->access_modifiler){
                            $is_correct = false;
                        } elseif ($constructor->parameter != $result_con->parameter){
                            $is_correct = false;
                        }
                    }else{
                        $is_correct = false;
                        $result_con = new ResultConstructor;
                        $result_con->result_id = $result->id;
                        $result_con->access_modifier = ' ';
                        $result_con->name = ' ';
                        $result_con->parameter = ' ';
                    }

                    if($is_correct){
                        $result_con->score = $constructor->score;
                    }else{
                        $result_con->score = 0;
                        array_push($this->wrong, $result->class.' มี constructor '.$result_con->name . ' ไม่ตรง');
                    }
                    $result_con->save();
                    $submission->score += $result_con->score;
                    //Log::info('Constructor IS CORRECT '. $is_correct);
                }

                foreach ($analysis->methods as $method){
                    $result_methods = ResultMethod::where('name', '=', $method->name)
                        ->orderBy('id', 'desc')
                        ->get();
                    $result_me = null;
                    foreach ($result_methods as $result_method){
                        $this_problem = $result_method->result->submissionFile->submission->problem;
                        if($problem->id == $this_problem->id){
                            $result_me = $result_method;
                            break;
                        }else{
                            $result_me = null;
                        }
                    }

                    $is_correct = true;
                    if($result_me != null){
                        if($method->access_modifiler != $result_me->access_modifiler){
                            $is_correct = false;
                        } elseif ($method->non_access_modifiler != $result_me->non_access_modifiler){
                            $is_correct = false;
                        } elseif ($method->return_type != $result_me->return_type){
                            $is_correct = false;
                        } elseif ($method->recursive != $result_me->recursive){
                            $is_correct = false;
                        } elseif ($method->loop != $result_me->loop){
                            $is_correct = false;
                        } elseif ($method->parameter != $result_me->parameter){
                            $is_correct = false;
                        }
                    } else {
                        $is_correct = false;
                        $result_me = new ResultMethod;
                        $result_me->result_id = $result->id;
                        $result_me->access_modifier = ' ';
                        $result_me->non_access_modifier = ' ';
                        $result_me->return_type = ' ';
                        $result_me->name = ' ';
                        $result_me->parameter = ' ';
                        $result_me->recursive = ' ';
                        $result_me->loop = ' ';
                    }

                    if ($is_correct){
                        $result_me->score = $method->score;
                    }else{
                        $result_me->score = 0;
                        array_push($this->wrong, $result->class.' มี method '.$method->name . ' ไม่ตรง');
                    }
                    $result_me->save();
                    $submission->score += $result_me->score;
                    //Log::info('Method IS CORRECT '. $is_correct);
                }
            }
        }
    }

    public function resubmit(Request $request)
    {
        $problem_id = $request->get('problem_id');
        $problem = Problem::findOrFail($problem_id);
        $course = $problem->lesson->course;
        $students = $course->students;
        foreach ($students as $student){
            $submission = Submission::where([
                ['problem_id', $problem_id],
                ['student_id', $student->id]
            ])->orderBy('id', 'desc')->first();
            if(sizeof($submission) == 1){
                foreach ($submission->submissionFiles as $submissionFile){
                    foreach ($submissionFile->results as $result){
                        $result->delete();
                    }
                }

                if ($problem->is_parse == 'true'){
                    $classes = self::analyzeSubmitFile2($submission);

                    self::saveResult($classes, $submission->submissionFiles);

                    foreach ($submission->submissionFiles as $submissionFile){
                        self::calStructureScore2($submissionFile);
                    }

                    $results_classname = [];
                    foreach ($submission->submissionFiles as $submissionFile){
                        foreach ($submissionFile->results as $result) {
                            array_push($results_classname, $result->class);
                            //Log::info('result : '. $result->class);
                        }
                    }

                    $problemAnalysis_classname = [];
                    foreach ($problem->problemFiles as $problemFile){
                        foreach ($problemFile->problemAnalysis as $analysis) {
                            array_push($problemAnalysis_classname, $analysis->class);
                            //Log::info('analysis : '. $analysis->class);
                        }
                    }

                    $class_diffs = array_diff($problemAnalysis_classname, $results_classname);
                    //Log::info(print_r($class_diffs, true));
                    foreach ($class_diffs as $diff){
                        array_push($this->wrong, 'ไม่มีคลาส '.$diff);
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
                        if(sizeof($scores) == 0){
                            array_push($this->wrong, 'ผลลัพธ์ผิดในทุกชุดข้อมูลทดสอบ');
                        }
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
                if(sizeof($this->wrong) > 0){
                    $submission->is_accept = 'false';
                    $submission->save();
                }
                self::updateStudentProgress($submission);
            }

        }
        return response()->json(['msg' => 'inspection problem success']);
    }
}
