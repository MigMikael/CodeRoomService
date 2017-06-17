<?php

namespace App\Http\Controllers;

use App\Problem;
use App\ProblemScore;
use App\ProblemAttribute;
use App\ProblemConstructor;
use App\ProblemMethod;
use App\Student;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
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
        }

        return response()->json(['msg' => 'success']);*/
    }

    public function store(Request $request)
    {
        $lesson_id = $request->get('lesson_id');
        $order = Problem::where('lesson_id', $lesson_id)->max('order') + 1;

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

        if($request->hasFile('file')){
            $problem = Problem::create($problem);
            $file = $request->file('file');
            $msg = self::sendToProblemFile($problem, $file, 'create');

            if(strpos($msg, 'finish') !== false){
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

            }else{
                return response()->json(['msg' => 'error while parse code']);
            }

        } else {
            return response()->json(['msg' => 'file not found']);
        }
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
