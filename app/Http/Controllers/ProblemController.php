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
use App\Student;
use App\Traits\EvaluatorTrait;
use Illuminate\Http\Request;
use App\Traits\FileTrait;
use App\ProblemInput;
use App\ProblemOutput;
use Log;
use DB;

class ProblemController extends Controller
{
    use FileTrait, EvaluatorTrait;

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
        ];
        $problem = Problem::create($problem);

        $file = self::storeFile($file);
        self::unzipProblem($file, $problem->id);

        $response = self::checkFileStructure($problem);
        if($response != true){
            $problem->delete();
            return response()->json($response);
        }

        $question_file = self::storeQuestion($problem->name);
        $problem->question = $question_file->id;
        $problem->save();

        self::storeProblemFile($problem);

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
        
        return response()->json(['msg' => 'create problem success']);
    }

    public function storeProblemFile($problem)
    {
        $src_path = 'problem/'.$problem->id.'/'. $problem->name. '/src';
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

            $inputPath = 'problem/'.$problem->id.'/'. $problem->name. '/testCase/';
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
                        'score' => 10
                    ];
                    ProblemOutput::create($problemOutput);
                }
            }
        }
    }

    public function update(Request $request)
    {
        /*$id = $request->get('id');
        $problem = Problem::findOfFail($id);

        $problem->name = $request->get('name');
        $problem->description = $request->get('description');
        $problem->evaluator = $request->get('evaluator');
        $problem->timelimit = $request->get('timelimit');
        $problem->memorylimit = $request->get('memorylimit');
        $problem->lesson_id = $request->get('lesson_id');
        $problem->is_parse = $request->get('is_parse');

        if($request->hasFile('file')){
            $file = $request->file('file');
            self::sendToProblemFile($problem, $file, 'edit');
        }*/

        return response()->json(['msg' => 'success']);
    }

    public function storeScore(Request $request)
    {
        $pFiles = $request->get('problem_files');
        foreach ($pFiles as $pFile){
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

                $atts = $pA['attributes'];
                foreach ($atts as $att){
                    $attribute = ProblemAttribute::findOrFail($att['id']);
                    $attribute->score = $att['score'];
                    $attribute->save();
                }

                $cons = $pA['constructors'];
                foreach ($cons as $con){
                    $constructor = ProblemConstructor::findOrFail($con['id']);
                    $constructor->score = $con['score'];
                    $constructor->save();
                }

                $mets = $pA['methods'];
                foreach ($mets as $met){
                    $method = ProblemMethod::findOrFail($met['id']);
                    $method->recursive = $met['recursive'];
                    $method->loop = $met['loop'];
                    $method->score = $met['score'];
                    $method->save();
                }
            }
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
}
