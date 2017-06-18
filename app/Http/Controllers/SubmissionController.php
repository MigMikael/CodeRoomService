<?php

namespace App\Http\Controllers;

use App\Submission;
use App\SubmissionFile;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function store(Request $request)
    {
        
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
