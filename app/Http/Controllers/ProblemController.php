<?php

namespace App\Http\Controllers;

use App\File;
use App\Problem;
use App\ProblemAnalysis;
use App\ProblemFile;
use App\ProblemScore;
use App\ProblemAttribute;
use App\ProblemConstructor;
use App\ProblemMethod;
use App\Resource;
use App\Student;
use App\Traits\EvaluatorTrait;
use Illuminate\Http\Request;
use App\Traits\FileTrait;
use App\Traits\DatabaseTrait;
use App\ProblemInput;
use App\ProblemOutput;
use Log;
use DB;

class ProblemController extends Controller
{
    use FileTrait, EvaluatorTrait, DatabaseTrait;

    public function show($id)
    {
        $problem = Problem::findOrFail($id);
        $problemFiles = $problem->problemFiles;
        foreach ($problemFiles as $problemFile){
            $problemAnalysis = $problemFile->problemAnalysis;
            foreach ($problemAnalysis as $analysis){
                $analysis->score;
                $analysis->attributes;
                $analysis->constructors;
                $analysis->methods;
            }
            $problemFile->inputs;
            $problemFile->outputs;
        }

        $resources_file = [];
        foreach ($problem->resources as $resource){
            $res = File::find($resource->file_id);
            array_push($resources_file, $res);
        }
        $problem['resources'] = $resources_file;
        $problem['question'] = url('problem/'.$problem->id.'/question');
        /*$submission = Submission::where('problem_id', '=', $problem->id)->get()->last();
        $problem['lastSubmission'] = $submission;*/

        return $problem;
    }

    public function store(Request $request)
    {
        $lesson_id = $request->get('lesson_id');
        $order = Problem::where('lesson_id', $lesson_id)->max('order') + 1;

        if($request->hasFile('file')){
            $file = $request->file('file');
        }
        else{
            return response()->json(['msg' => 'file not found']);
        }

        $problem = [
            'lesson_id' => $lesson_id,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'evaluator' => $request->get('evaluator'),
            'order' => $order,
            'timelimit' => $request->get('timelimit'),
            'memorylimit' => $request->get('memorylimit'),
            'is_parse' => $request->get('is_parse'),
            'status' => 'hide'
        ];
        $problem = Problem::create($problem);

        $file = self::storeFile($file);
        self::unzipProblem($file, $problem);
        self::deleteFile($file);

        $response = self::checkFileStructure($problem);
        if($response != true){
            $problem->delete();
            return response()->json($response);
        }

        $question_file = self::storeQuestion($problem->name);
        $problem->question = $question_file->id;
        $problem->save();

        $res = self::storeProblemFile($problem);
        if($res != 'success'){
            return response()->json(['msg' => $res]);
        }

        self::storeResources($problem);

        if($problem->is_parse == 'true'){
            $classes = self::analyzeProblemFile($problem);

            $res = self::saveResult($classes, $problem->problemFiles);
            if($res == 'analysis error'){
                //Log::info($res);
                return response()->json(['msg' => $res]);
            }

            foreach ($problem->problemFiles as $problemFile){
                $problemFile['code'] = '';
                foreach ($problemFile->problemAnalysis as $analysis){
                    $analysis->score;
                    $analysis->attributes;
                    $analysis->constructors;
                    $analysis->methods;
                }
            }
            return $problem;
        }
        
        return response()->json(['msg' => 'create problem success']);
    }

    public function storeProblemFile($problem)
    {
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);
        $src_path = $course_name.'/'.$problem->id.'/'. $problem->name. '/src';
        $files = self::getFiles($src_path);
        foreach ($files as $file){
            //Log::info('#### '. $file); # LastDigit/src/LastDigit.java
            $code = self::getFile($file);

            $file = explode('/src/', $file);
            //Log::info('#### '. $file[1]);

            if(strrpos($file[1], '/')) {
                $package = substr($file[1], 0, strrpos($file[1], '/'));

                $file_name = str_replace($package.'/', '', $file[1]);

                $package = str_replace('/','.', $package);
            }
            else{
                $package = 'default package';
                $file_name = $file[1];
            }

            $problem_file = [
                'problem_id' => $problem->id,
                'package' => $package,
                'filename' => $file_name,
                'mime' => 'java',
                'code' => $code,
            ];

            ProblemFile::create($problem_file);
        }

        $inputPath = $course_name.'/'.$problem->id.'/'. $problem->name. '/testCase/';
        $inputFiles = self::getFiles($inputPath);
        foreach ($inputFiles as $inputFile){
            $temps = explode('/', $inputFile);
            $fileName = $temps[sizeof($temps) - 1];
            $folderName = $temps[sizeof($temps) - 2];
            $folderName = $folderName.'.java';
            $version = 1;

            $pro_file = ProblemFile::where('filename', $folderName)->first();

            if(sizeof($pro_file) != 1){
                //Log::info('in sol wrong folder');
                return response()->json(['msg' => 'in sol wrong folder']);
            }

            if(strpos($fileName, 'in') != false) {          // This is input file
                $problemInput = [
                    'problem_file_id' => $pro_file->id,
                    'version' => $version,
                    'filename' => $fileName,
                    'content' => self::getFile($inputFile)
                ];
                ProblemInput::create($problemInput);
            }
            else if(strpos($fileName, 'sol') != false) {    //This is output file
                $problemOutput = [
                    'problem_file_id' => $pro_file->id,
                    'version' => $version,
                    'filename' => $fileName,
                    'content' => self::getFile($inputFile),
                    'score' => 100
                ];
                $output = ProblemOutput::create($problemOutput);
                $problem->score += $output->score;
            }
        }
        $problem->save();
        return 'success';
    }

    public function storeResources($problem)
    {
        $course = $problem->lesson->course;
        $course_name = str_replace(' ', '_', $course->name);
        $resource_path = $course->id.'_'.$course_name.'/'.$problem->id.'/'. $problem->name. '/resource';
        $files = self::getFiles($resource_path);
        if(sizeof($files) > 0){
            foreach ($files as $file){
                $name = explode('/', $file);
                $name = $name[sizeof($name) - 1];
                //Log::info($name);
                $mime = self::getMime($file);
                $fileRecord = [
                    'name' => $name,
                    'mime' => $mime,
                    'original_name' => $name,
                ];
                $file = File::create($fileRecord);
                $resource = [
                    'problem_id' => $problem->id,
                    'file_id' => $file->id,
                    'visible' => 'false',
                ];
                Resource::create($resource);
            }
            //Log::info('Store Resource Success');
        }
    }

    public function update(Request $request)
    {
        $problem = Problem::findOrFail($request->get('id'));
        $old_name = $problem->name;

        $new_problem = [
            'lesson_id' => $problem->lesson_id,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'evaluator' => $request->get('evaluator'),
            'order' => $problem->order,
            'timelimit' => $request->get('timelimit'),
            'memorylimit' => $request->get('memorylimit'),
            'is_parse' => $request->get('is_parse'),
            'status' => $problem->status
        ];
        $problem->update($new_problem);

        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);

        $new_problem = Problem::find($problem->id);

        // delete old file
        if($request->hasFile('file')){
            foreach ($problem->problemFiles as $problemFile){
                $problemFile->delete();
            }

            $new_file = $request->file('file');
            $prob_path = $course_name.'/'.$problem->id.'/'. $problem->name. '/';
            $files = self::getFiles($prob_path);
            foreach ($files as $file){
                self::deleteByPath($file);
            }
            $question_file = File::findOrFail($problem->question);
            $question_file->delete();
            self::deleteResource($problem->id);

            $new_file = self::storeFile($new_file);
            self::unzipProblem($new_file, $new_problem);
            self::deleteFile($new_file);

            $response = self::checkFileStructure($new_problem);
            if($response != true){
                $new_problem->delete();
                return response()->json($response);
            }

            $question_file = self::storeQuestion($new_problem->name);
            $new_problem->question = $question_file->id;
            $new_problem->save();

            self::storeProblemFile($new_problem);
            self::storeResources($new_problem);

            if($new_problem->is_parse == 'true'){
                foreach ($new_problem->problemFiles as $problemFile){
                    $classes = self::analyzeProblemFile($problemFile);
                    self::saveResult($classes, $problemFile);
                }

                foreach ($new_problem->problemFiles as $problemFile){
                    $problemFile['code'] = '';
                    foreach ($problemFile->problemAnalysis as $analysis){
                        $analysis->score;
                        $analysis->attributes;
                        $analysis->constructors;
                        $analysis->methods;
                    }
                }
                return $new_problem;
            }
        }else{
            if($old_name != $problem->name){
                $old_path =  $course_name.'/'.$problem->id.'/'. $old_name. '/';
                $prob_path = $course_name.'/'.$problem->id.'/'. $problem->name. '/';
                rename($old_path, $prob_path);
            }
        }

        return response()->json(['msg' => 'edit problem success']);
    }

    public function storeScore(Request $request)
    {
        $pFiles = $request->get('problem_files');
        foreach ($pFiles as $pFile){
            $prob_file = ProblemFile::findOrFail($pFile['id']);
            $problem = $prob_file->problem;

            $pAs = $pFile['problem_analysis'];
            foreach ($pAs as $pA){
                $score = $pA['score'];
                $problem_score = ProblemScore::findOrFail($score['id']);
                $problem_score->class = $score['class'];
                $problem_score->package = $score['package'];
                $problem_score->enclose = $score['enclose'];
                $problem_score->extends = $score['extends'];
                $problem_score->implements = $score['implements'];
                $problem_score->save();
                $problem->score += $problem_score->class + $problem_score->package +
                    $problem_score->enclose + $problem_score->extends +
                    $problem_score->implements;

                $atts = $pA['attributes'];
                foreach ($atts as $att){
                    $attribute = ProblemAttribute::findOrFail($att['id']);
                    $attribute->score = $att['score'];
                    $attribute->save();
                    $problem->score += $attribute->score;
                }

                $cons = $pA['constructors'];
                foreach ($cons as $con){
                    $constructor = ProblemConstructor::findOrFail($con['id']);
                    $constructor->score = $con['score'];
                    $constructor->save();
                    $problem->score += $constructor->score;
                }

                $mets = $pA['methods'];
                foreach ($mets as $met){
                    $method = ProblemMethod::findOrFail($met['id']);
                    $method->recursive = $met['recursive'];
                    $method->loop = $met['loop'];
                    $method->score = $met['score'];
                    $method->save();
                    $problem->score += $method->score;
                }
            }
            $problem->save();
        }

        return response()->json(['msg' => 'store score success']);
    }

    public function saveResult($classes, $problemFiles)
    {
        if($classes['class'] == null){
            return 'analysis error';
        }

        foreach ($classes['class'] as $class){
            foreach ($problemFiles as $problemFile){
                if($class['package'] == 'default'){
                    $class['package'] = 'default package';
                }

                $class_file = strpos($problemFile->code, 'class '.$class['name']. ' ');

                /*Log::info('strlen class name '.strlen($class['name']));
                Log::info('strlen file name '. strlen($filename));*/

                if($class_file != false && $class['package'] == $problemFile->package && $problemFile->package != 'driver'){
                    $im = '';
                    foreach ($class['implements'] as $implement){
                        $im .= $implement['name'];
                    }

                    $problemAnalysis = [
                        'problem_file_id' => $problemFile->id,
                        'class' => $class['modifier'].';'.$class['static_required'].';'.$class['name'],
                        'package' => $problemFile->package,
                        'enclose' => $class['enclose'],
                        'extends' => $class['extends'],
                        'implements' => $im,
                    ];
                    $problemAnalysis = ProblemAnalysis::create($problemAnalysis);
                    if($problemAnalysis->enclose == 'null'){
                        $problemAnalysis->enclose = '';
                        $problemAnalysis->save();
                    }
                    if($problemAnalysis->extends == 'null'){
                        $problemAnalysis->extends = '';
                        $problemAnalysis->save();
                    }

                    $problem_score = [
                        'analysis_id' => $problemAnalysis->id,
                        'class' => 0,
                        'package' => 0,
                        'enclose' => 0,
                        'extends' => 0,
                        'implements' => 0,
                    ];
                    ProblemScore::create($problem_score);

                    foreach ($class['constructure'] as $constructor){
                        $pa = '';
                        foreach ($constructor['params'] as $param){
                            $pa .= $param['datatype'].';'.$param['name'].'|';
                        }

                        $con = [
                            'analysis_id' => $problemAnalysis->id,
                            'access_modifier' => $constructor['modifier'],
                            'name' => $constructor['name'],
                            'parameter' => $pa
                        ];
                        ProblemConstructor::create($con);
                    }

                    foreach ($class['attribute'] as $attribute){
                        $att = [
                            'analysis_id' => $problemAnalysis->id,
                            'access_modifier' => $attribute['modifier'],
                            'non_access_modifier' => $attribute['static_required'],
                            'data_type' => $attribute['datatype'],
                            'name' => $attribute['name']
                        ];
                        ProblemAttribute::create($att);
                    }

                    foreach ($class['method'] as $method){
                        $pa = '';
                        foreach ($method['params'] as $param){
                            $pa .= $param['datatype'].';'.$param['name'].'|';
                        }

                        if($method['recursive'] == null){
                            $method['recursive'] = 'null';
                        }
                        if($method['loop_exist'] == null){
                            $method['loop_exist'] = 'null';
                        }

                        $me = [
                            'analysis_id' => $problemAnalysis->id,
                            'access_modifier' => $method['modifier'],
                            'non_access_modifier' => $method['static_required'],
                            'return_type' => $method['return_type'],
                            'name' => $method['name'],
                            'parameter' => $pa,
                            'recursive' => $method['recursive'],
                            'loop' => $method['loop_exist']
                        ];
                        ProblemMethod::create($me);
                    }
                }
            }
        }
        return 'analysis success';
    }

    public function deleteResource($id)
    {
        $problem = Problem::findOrFail($id);
        foreach ($problem->resources as $resource){
            $file = File::find($resource->file_id);
            $file->delete();
        }

        return response()->json(['msg' => 'delete problem resource success']);
    }

    public function changeOrder(Request $request)
    {
        $newProblems = $request->all();
        $count = 1;
        foreach ($newProblems as $newProblem){
            $problem = Problem::findOrFail($newProblem['id']);
            $problem->order = $count;
            $problem->save();
            $count++;
        }

        return response()->json(['msg' => 'change order success']);
    }

    public function submission($id)
    {
        $submissions = DB::select('select * from submission where id IN (select max(id) FROM submission WHERE problem_id = ? GROUP BY student_id)', [$id]);

        foreach ($submissions as $submission){
            $submission->student = Student::findOrFail($submission->student_id);
        }

        return $submissions;
    }

    public function storeInputAndOutput(Request $request)
    {
        $type = $request->get('type');
        if($request->hasFile('file')){
            $theFile = $request->file('file');
        }else{
            return response()->json(['msg' => 'file not found']);
        }

        if($type == 'input'){
            $f = self::storeFile($theFile);
            $problemFile_id = $request->get('problem_file_id');

            $problemFile = ProblemFile::findOrFail($problemFile_id);
            $input = [
                'problem_file_id' => $problemFile->id,
                'version' => self::getMaxInputVersion($problemFile) + 1,
                'filename' => $f->original_name,
                'content' => self::getFile($f->name)
            ];
            ProblemInput::create($input);
            self::updateInputVersion($problemFile);

            return response()->json(['msg' => 'create input success']);
        }else{
            $f = self::storeFile($theFile);
            $problemFile_id = $request->get('problem_file_id');

            $problemFile = ProblemFile::findOrFail($problemFile_id);
            $output = [
                'problem_file_id' => $problemFile->id,
                'version' => self::getMaxOutputVersion($problemFile) + 1,
                'filename' => $f->original_name,
                'content' => self::getFile($f->name),
                'score' => 100.00
            ];
            ProblemOutput::create($output);
            self::updateOutputVersion($problemFile);

            return response()->json(['msg' => 'create output success']);
        }
    }

    public function updateInput(Request $request)
    {
        $id = $request->get('id');
        $problem_input = ProblemInput::findOrFail($id);

        if($request->hasFile('in')){
            $inFile = $request->file('in');
            $f = self::storeFile($inFile);

            $content = self::getFile($f->name);
            $problem_input->filename = $f->original_name;
            $problem_input->content = $content;
            $problem_input->version = self::getMaxInputVersion($problem_input->problemFile) + 1;
            $problem_input->save();

            self::updateInputVersion($problem_input->problemFile);

            return response()->json(['msg' => 'edit input success']);
        }
        else{
            return response()->json(['msg' => 'file input not found']);
        }
    }

    public function updateOutput(Request $request)
    {
        $id = $request->get('id');
        $problem_output = ProblemOutput::findOrFail($id);

        if($request->hasFile('sol')){
            $solFile = $request->file('sol');
            $f = self::storeFile($solFile);

            $content = self::getFile($f->name);
            $problem_output->filename = $f->original_name;
            $problem_output->content = $content;
            $problem_output->version = self::getMaxOutputVersion($problem_output->problemFile) + 1;
            $problem_output->save();

            self::updateOutputVersion($problem_output->problemFile);

            return response()->json(['msg' => 'edit output success']);
        }
        else{
            return response()->json(['msg' => 'file output not found']);
        }
    }

    public function destroyInput($id)
    {
        $problem_input = ProblemInput::findOrFail($id);
        $problem_input->version = self::getMaxInputVersion($problem_input->problemFile) + 1;
        $problem_input->save();
        self::updateInputVersion($problem_input->problemFile);

        $problem_input->delete();

        return response()->json(['msg' => 'delete input complete']);
    }

    public function destroyOutput($id)
    {
        $problem_output = ProblemOutput::findOrFail($id);
        $problem_output->version = self::getMaxOutputVersion($problem_output->problemFile) + 1;
        $problem_output->save();
        self::updateOutputVersion($problem_output->problemFile);

        $problem_output->delete();

        return response()->json(['msg' => 'delete output complete']);
    }

    public function destroyAllInput($problem_id)
    {
        $problem = Problem::findOrFail($problem_id);
        foreach ($problem->problemFiles as $problemFile){
            foreach ($problemFile->inputs as $input){
                $input->delete();
            }
        }

        return response()->json(['msg' => 'delete input complete']);
    }

    public function destroyAllOutput($problem_id)
    {
        $problem = Problem::findOrFail($problem_id);
        foreach ($problem->problemFiles as $problemFile){
            foreach ($problemFile->outputs as $output){
                $output->delete();
            }
        }

        return response()->json(['msg' => 'delete output complete']);
    }

    public function changeStatus($id)
    {
        $problem = Problem::findOrFail($id);
        if($problem->status == 'show'){
            $problem->status = 'hide';
        }else{
            $is_equals = true;
            foreach ($problem->problemFiles as $problemFile){
                $input_count = $problemFile->inputs()->count();
                $output_count = $problemFile->outputs()->count();
                if($input_count != $output_count){
                    $is_equals = false;
                }
            }

            if($is_equals){
                $problem->status = 'show';
            }else{
                return response()->json(['msg' => 'in and sol do not match']);
            }
        }
        $problem->save();
        self::updateLessonProgress($problem->lesson);

        return response()->json(['msg' => 'change status complete']);
    }

    public function delete($id)
    {
        $problem = Problem::findOrFail($id);
        $lesson = $problem->lesson;
        $problem->delete();

        self::updateLessonProgress($lesson);

        return response()->json(['msg' => 'delete problem success']);
    }

    public function getMaxInputVersion($problemFile)
    {
        $input = ProblemInput::where('problem_file_id', $problemFile->id)
            ->first();

        if(sizeof($input) == 1){
            $max = ProblemInput::where('problem_file_id', $problemFile->id)
                ->max('version');
            return $max;
        }else{
            return 0;
        }
    }

    public function getMaxOutputVersion($problemFile)
    {
        $output = ProblemOutput::where('problem_file_id', $problemFile->id)->first();
        if(sizeof($output) == 1){
            $max = ProblemInput::where('problem_file_id', $problemFile->id)
                ->max('version');
            return $max;
        }else{
            return 0;
        }
    }

    public function updateInputVersion($problemFile)
    {
        $maxVer = self::getMaxInputVersion($problemFile);
        foreach ($problemFile->inputs as $input){
            $input->version = $maxVer;
            $input->save();
        }
    }

    public function updateOutputVersion($problemFile)
    {
        $maxVer = self::getMaxOutputVersion($problemFile);
        foreach ($problemFile->outputs as $output){
            $output->version = $maxVer;
            $output->save();
        }
    }
}
