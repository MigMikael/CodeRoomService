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
        $problem->resoruces;
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

        self::storeProblemFile($problem);
        self::storeResource($problem);

        if($problem->is_parse == 'true'){
            foreach ($problem->problemFiles as $problemFile){
                $classes = self::analyzeProblemFile($problemFile);
                self::saveResult($classes, $problemFile);
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
        self::updateLessonProgress($problem->lesson);
        
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
            Log::info('#### '. $file[1]);

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

            $problem_file = ProblemFile::create($problem_file);

            $inputPath = $course_name.'/'.$problem->id.'/'. $problem->name. '/testCase/';
            $inputFiles = self::getFiles($inputPath);
            foreach ($inputFiles as $inputFile){
                $temps = explode('/', $inputFile);
                $fileName = $temps[sizeof($temps) - 1];
                $version = 1;

                if(strpos($fileName, 'in') != false) {          // This is input file
                    $problemInput = [
                        'problem_file_id' => $problem_file->id,
                        'version' => $version,
                        'filename' => $fileName,
                        'content' => self::getFile($inputFile)
                    ];
                    ProblemInput::create($problemInput);
                }
                else if(strpos($fileName, 'sol') != false) {    //This is output file
                    $problemOutput = [
                        'problem_file_id' => $problem_file->id,
                        'version' => $version,
                        'filename' => $fileName,
                        'content' => self::getFile($inputFile),
                        'score' => 100
                    ];
                    $output = ProblemOutput::create($problemOutput);
                    $problem->score += $output->score;
                }
            }
        }
        $problem->save();
    }

    public function storeResource($problem)
    {
        $course = $problem->lesson->course;
        $course_name = str_replace(' ', '_', $course->name);
        $resource_path = $course->id.'_'.$course_name.'/'.$problem->id.'/'. $problem->name. '/resource';
        $files = self::getFiles($resource_path);
        if(sizeof($files) > 0){
            foreach ($files as $file){
                $name = explode('/', $file);
                $name = $name[sizeof($name) - 1];
                Log::info($name);
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
            Log::info('Store Resource Success');
        }
    }

    public function update(Request $request)
    {
        $problem = Problem::findOrFail($request->get('id'));

        if($request->hasFile('file')){
            $new_file = $request->file('file');
        }
        else{
            return response()->json(['msg' => 'file not found']);
        }

        $new_problem = [
            'lesson_id' => $problem->lesson_id,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'evaluator' => $request->get('evaluator'),
            'order' => $problem->id,
            'timelimit' => $request->get('timelimit'),
            'memorylimit' => $request->get('memorylimit'),
            'is_parse' => $request->get('is_parse'),
        ];
        $new_problem = Problem::create($new_problem);

        // delete old file
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);
        $prob_path = $course_name.'/'.$problem->id.'/'. $problem->name. '/';
        $files = self::getFiles($prob_path);
        foreach ($files as $file){
            self::deleteFile($file);
        }
        $question_file = File::findOrFail($problem->question);
        $question_file->delete();
        self::delete($problem->id);


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
        self::storeResource($new_problem);

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
                    if ($met['recursive'] == 'null'){
                        $met['recursive'] = 'false';
                    }elseif ($met['recursive'] == 1){
                        $met['recursive'] = 'true';
                    }else{
                        $met['recursive'] = 'false';
                    }
                    $method->recursive = $met['recursive'];

                    if ($met['loop'] == 'null'){
                        $met['loop'] = 'false';
                    }elseif ($met['loop'] == 1){
                        $met['loop'] = 'true';
                    }else{
                        $met['loop'] = 'false';
                    }
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

    public function saveResult($classes, $problemFile)
    {
        foreach ($classes['class'] as $class){
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

    public function delete($id)
    {
        $problem = Problem::findOrFail($id);
        foreach ($problem->resources as $resource){
            $file = File::find($resource->file_id);
            $file->delete();
        }
        $problem->delete();

        return response()->json(['msg' => 'delete problem success']);
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

    public function updateInput(Request $request)
    {
        $id = $request->get('id');
        $problem_input = ProblemInput::findOrFail($id);

        if($request->has('in')){
            $inFile = $request->file('in');
            $name = self::storeFile($inFile);

            $content = self::getFile($name);
            $problem_input->content = $content;
            $problem_input->version += 1;
            $problem_input->save();

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

        if($request->has('sol')){
            $solFile = $request->file('sol');
            $name = self::storeFile($solFile);

            $content = self::getFile($name);
            $problem_output->content = $content;
            $problem_output->version += 1;
            $problem_output->save();

            return response()->json(['msg' => 'edit output success']);
        }
        else{
            return response()->json(['msg' => 'file output not found']);
        }
    }

    public function destroyInput($id)
    {
        $problem_input = ProblemInput::findOrFail($id);
        $problem_input->delete();

        return response()->json(['msg' => 'delete input complete']);
    }

    public function destroyOutput($id)
    {
        $problem_output = ProblemOutput::findOrFail($id);
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
            $problem->status = 'show';
        }
        $problem->save();

        return response()->json(['msg' => 'change status complete']);
    }
}
