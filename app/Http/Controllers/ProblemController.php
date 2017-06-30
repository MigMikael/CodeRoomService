<?php

namespace App\Http\Controllers;

use App\File;
use App\Problem;
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
use Illuminate\Support\Facades\DB;

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
        $problem['question'] = url('problem/getQuestion/'.$problem->id);
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

        $next_id = Problem::count();
        $file = self::storeFile($file);
        self::unzipProblem($file, $next_id + 1);

        $name = $request->get('name');
        $question_file = self::storeQuestion($name);

        $problem = [
            'lesson_id' => $lesson_id,
            'name' => $name,
            'description' => $request->get('description'),
            'evaluator' => $request->get('evaluator'),
            'order' => $order,
            'question' => $question_file->id,
            'timelimit' => $request->get('timelimit'),
            'memorylimit' => $request->get('memorylimit'),
            'is_parse' => $request->get('is_parse'),
        ];
        $problem = Problem::create($problem);

        self::storeProblemFile($problem);

        if($problem->is_parse == 'true'){
            //self::analyzeFile();
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
                $package = str_replace('/','.', $package);

                $file_name = str_replace($package.'/', '', $file[1]);
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
        $id = $request->get('id');
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
        }

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
