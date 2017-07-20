<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Lesson;
use App\Problem;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LessonController extends Controller
{
    public function show($id)
    {
        $lesson = Lesson::withCount(['problems'])->findOrFail($id);

        $problems = Problem::where('lesson_id', '=', $lesson->id)
            ->ordered()
            ->get();
        $lesson['problems'] = $problems;

        foreach ($lesson['problems'] as $problem){
            $problem['question'] = url('problem/'.$problem->id.'/question');
        }

        return $lesson;
    }

    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $input = [
            'name' => $request->get('name'),
            'course_id' => $course_id,
            'status' => 'normal',// lesson status 'normal' or 'test'
            'order' => Lesson::where('course_id', $course_id)->max('order') + 1
        ];
        Lesson::create($input);

        // Todo fix it
        //$request = $request->create('api/gen_lesson_badge', 'POST', $lesson);
        //$res = app()->handle($request);

        return response()->json(['msg' => 'create lesson success']);
    }

    public function update(Request $request)
    {
        $lesson_id = $request->get('id');

        $lesson = Lesson::findOrFail($lesson_id);
        $lesson->name = $request->get('name');;
        $lesson->status = $request->get('status');
        $lesson->save();

        return response()->json(['msg' => 'update lesson success']);
    }

    public function delete($id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return response()->json(['msg' => 'delete lesson success']);
    }

    public function changeOrder(Request $request)
    {
        $newLessons = $request->all();
        $count = 1;
        foreach ($newLessons as $newLesson){
            $lesson = Lesson::findOrFail($newLesson['id']);
            $lesson->order = $count;
            $lesson->save();
            $count++;
        }

        return response()->json(['msg' => 'change order success']);
    }

    public function exportScore($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course = $lesson->course;
        $students = $course->students;

        foreach ($lesson->problems as $problem){
            foreach ($problem->submissions as $submission){
                if($submission->is_accept == 'true'){
                    $score = 0;
                    foreach ($submission->submissionFiles as $submissionFile){
                        foreach ($submissionFile->outputs as $output){
                            $score += $output->score;
                        }
                        $curr_std = $submission->student;
                        $student = $students->where('id', $curr_std->id)->first();
                        $student['score'] = $score;
                    }
                    $student['sub_num'] = $submission->sub_num;
                }
            }
        }
        $data_score = [];
        foreach ($students as $student){
            if(!isset($student->score)){
                $student->score = '0';
            }

            if(!isset($student->sub_num)){
                $student->sub_num = '-';
            }
            array_push($data_score, [
                'id' => $student->student_id,
                'name' => $student->name,
                'score' => $student->score,
                'submit count' => $student->sub_num
            ]);
        }

        $filename = 'score-'.Carbon::now();
        $filename = str_replace(' ', '-', $filename);
        Excel::create($filename, function($excel) use ($data_score) {
            $excel->sheet('sheet1', function($sheet) use ($data_score) {
                $sheet->fromArray($data_score);
            });
        })->download('xlsx');
    }
}
