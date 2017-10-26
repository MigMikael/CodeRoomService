<?php

namespace App\Http\Controllers;

use App\Course;
use App\File;
use App\Resource;
use App\Student;
use App\StudentLesson;
use App\Submission;
use Carbon\Carbon;
use App\Lesson;
use App\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\FileTrait;
use Log;

class LessonController extends Controller
{
    use FileTrait;

    public function showStudent($lesson_id, $student_id)
    {
        $lesson = Lesson::withCount(['problems'])->findOrFail($lesson_id);

        $problems = Problem::where('lesson_id', '=', $lesson->id)
            ->ordered()
            ->get();

        foreach ($problems as $problem){
            $resources = Resource::where('problem_id', $problem->id)->get();

            $resources_file = [];
            foreach ($resources as $resource){
                $file = File::find($resource->file_id);
                $file['status'] = $resource->visible;
                array_push($resources_file, $file);
            }
            $problem['resources_file'] = $resources_file;

            $submission = Submission::where([
                ['problem_id', $problem->id],
                ['student_id', $student_id],
            ])->orderBy('id', 'desc')->first();

            if(sizeof($submission) == 1){
                foreach ($submission->submissionFiles as $submissionFile){
                    $submissionFile->outputs;

                    foreach ($submissionFile->results as $result){
                        $result->score;
                        $result->attributes;
                        $result->constructors;
                        $result->methods;
                    }
                }
            }

            if(sizeof($submission) == 1){
                if($submission->is_accept == 'true'){
                    $problem['is_accept'] = 'true';
                }else{
                    $problem['is_accept'] = 'false';
                }
            }else{
                $problem['is_accept'] = 'not_submit';
            }
        }
        $lesson['problems'] = $problems;

        foreach ($lesson['problems'] as $problem){
            $problem['question'] = url('problem/'.$problem->id.'/question');
        }

        return $lesson;
    }

    public function showTeacher($id)
    {
        $lesson = Lesson::withCount(['problems'])->findOrFail($id);

        $problems = Problem::where('lesson_id', '=', $lesson->id)
            ->ordered()
            ->get();

        foreach ($problems as $problem){
            $resources = Resource::where([
                ['problem_id', $problem->id],
                ['visible', 'true']
            ])->get();

            $resources_file = [];
            foreach ($resources as $resource){
                $file = File::find($resource->file_id);
                array_push($resources_file, $file);
            }
            $problem['resources_file'] = $resources_file;
        }
        $lesson['problems'] = $problems;

        foreach ($lesson['problems'] as $problem){
            $problem['question'] = url('problem/'.$problem->id.'/question');
        }

        return $lesson;
    }

    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $course = Course::findOrFail($course_id);
        $input = [
            'name' => $request->get('name'),
            'course_id' => $course_id,
            'order' => Lesson::where('course_id', $course_id)->max('order') + 1
        ];

        if($request->has('mode')){ // lesson status 'normal' or 'test'
            $input['mode'] = $request->get('mode');
        }else{
            $input['mode'] = 'normal';
        }

        if($request->has('status')){ // lesson status 'show' or 'hide'
            $input['status'] = $request->get('status');
        }else{
            $input['status'] = 'show';
        }

        if($request->has('open_submit')){ // open_submit 'true' or 'false'
            $input['open_submit'] = $request->get('open_submit');
        }else{
            $input['open_submit'] = 'true';
        }

        if($request->has('guide')){ // guide 'true' or 'false'
            $input['guide'] = $request->get('guide');
        }else{
            $input['guide'] = 'true';
        }
        $lesson = Lesson::create($input);
        foreach ($course->students as $student){
            $studentLesson = [
                'student_id' => $student->id,
                'lesson_id' => $lesson->id,
                'progress' => 0
            ];
            StudentLesson::firstOrCreate($studentLesson);
        }

        // Todo fix it
        //$request = $request->create('api/gen_lesson_badge', 'POST', $lesson);
        //$res = app()->handle($request);

        return response()->json(['msg' => 'create lesson success']);
    }

    public function update(Request $request)
    {
        $lesson_id = $request->get('id');

        $lesson = Lesson::findOrFail($lesson_id);
        $lesson->name = $request->get('name');

        if($request->has('mode')){
            $lesson->mode = $request->get('mode');
        }
        if($request->has('status')){
            $lesson->status = $request->get('status');
        }
        if($request->has('open_submit')){
            $lesson->open_submit = $request->get('open_submit');
        }
        if($request->has('guide')){
            $lesson->guide = $request->get('guide');
        }
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

    public function changeStatus($id)
    {
        $lesson = Lesson::findOrFail($id);
        if($lesson->status == 'show'){
            $lesson->status = 'hide';
            $msg = 'change status to hide success';
        }else{
            $lesson->status = 'show';
            $msg = 'change status to show success';
        }
        $lesson->save();

        return response()->json(['msg' => $msg]);
    }

    public function changeSubmit($id)
    {
        $lesson = Lesson::findOrFail($id);
        if($lesson->open_submit == 'true'){
            $lesson->open_submit = 'false';
            $msg = 'close submit success';
        }else{
            $lesson->open_submit = 'true';
            $msg = 'open submit success';
        }
        $lesson->save();

        return response()->json(['msg' => $msg]);
    }

    public function exportScore($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course = $lesson->course;
        $students = $course->students;
        $score = [];
        foreach ($students as $student){
            $score[$student->id] = [];
            foreach ($lesson->problems as $problem){
                $score[$student->id][$problem->name] = '0';
            }
            $score[$student->id]['total'] = '0';
        }
        foreach ($lesson->problems as $problem){
            foreach ($problem->submissions as $submission){
                $curr_std = $submission->student;
                if($submission->score > 0 && $curr_std->role != 'hidden'){
                    $student = $students->where('id', $curr_std->id)->first();
                    $score[$student->id][$problem->name] = $submission->score;
                    $score[$student->id]['total'] += $submission->score;
                }
            }
        }

        foreach ($students as $student){
            $student['score'] = $score[$student->id];
        }

        $data_score = [];
        foreach ($students as $student){
            $row = [];
            $row['id'] = $student->student_id;
            $row['name'] = $student->name;
            foreach ($lesson->problems as $problem){
                $row[$problem->name] = $student['score'][$problem->name];
            }
            $row['total'] = $student['score']['total'];
            array_push($data_score, $row);
        }

        $path = storage_path() . '/app/';

        $filename = 'score-'.Carbon::now();
        $filename = str_replace(' ', '-', $filename);
        Excel::create($filename, function($excel) use ($data_score) {
            $excel->sheet('sheet1', function($sheet) use ($data_score) {
                $sheet->fromArray($data_score);
            });
        })->store('xlsx', $path);

        $exportPath = storage_path() . '/app/' . $filename . '.xlsx';
        $ex = base64_encode(file_get_contents($exportPath));

        return response()->json([
            'excel' => $ex,
        ]);
    }

    public function exportByProblem($id, $problem_id)
    {
        $lesson = Lesson::findOrFail($id);

        $problem = Problem::findOrFail($problem_id);
        $exportFilename = $problem->name.'.zip';
        $path = $problem->name . '/';
        foreach ($problem->submissions as $submission){
            $student_lesson = Student::where('id', $submission->student->id)->first();
            if(sizeof($student_lesson) == 1){
                $student = $submission->student;
                $eachFilePath = $path . $student->student_id . '/';
                foreach ($submission->submissionFiles as $submissionFile){
                    if($submissionFile->package != 'driver'){
                        Storage::put($eachFilePath . $submissionFile->package .'/'. $submissionFile->filename,
                            $submissionFile->code
                        );
                    }
                }
            }
        }
        $rootPath = storage_path() . '/app/' . $problem->name;
        $exportPath = storage_path() . '/app/' . $exportFilename;
        self::zipFile($exportPath, $rootPath);

        $zz = base64_encode(file_get_contents($exportPath));

        Storage::deleteDirectory($rootPath);

        return response()->json([
            'zip' => $zz,
        ]);

        /*ob_end_clean();
        return response()->download($exportPath, $exportFilename,
            ['Content-Type' => 'application/zip']
        );*/
    }

    public function exportByStudent($id, $student_id)
    {
        $lesson = Lesson::findOrFail($id);

        $student = Student::findOrFail($student_id);
        $exportFilename = $student->student_id.'.zip';
        $path = $student->student_id . '/';
        foreach ($student->submissions as $submission){
            $problem = $submission->problem;
            $prob_lesson = $problem->lesson;
            if($lesson->id == $prob_lesson->id){
                $eachFilePath = $path . $problem->name . '/';
                foreach ($submission->submissionFiles as $submissionFile){
                    Storage::put($eachFilePath . $submissionFile->package . '/' . $submissionFile->filename, $submissionFile->code);
                }
            }
        }
        $rootPath = storage_path() . '/app/' . $student->student_id;
        $exportPath = storage_path() . '/app/' . $exportFilename;
        self::zipFile($exportPath, $rootPath);

        $zz = base64_encode(file_get_contents($exportPath));

        Storage::deleteDirectory($rootPath);

        return response()->json([
            'zip' => $zz,
        ]);

        /*ob_end_clean();
        return response()->download($exportPath, $exportFilename,
            ['Content-Type' => 'application/zip']
        );*/
    }

    public function scoreboard($id)
    {
        $lesson = Lesson::findOrFail($id);
        $course = $lesson->course;
        $students = $course->students;
        $score = [];
        foreach ($students as $student){
            $score[$student->id] = [];
            foreach ($lesson->problems as $problem){
                $score[$student->id][$problem->name] = 0;
            }
            $score[$student->id]['total'] = 0;
            $score[$student->id]['time'] = 0;
            $score[$student->id]['complete'] = 'false';
        }
        $total_prob_score = 0;
        foreach ($lesson->problems as $problem){
            $total_prob_score += $problem->score;
            foreach ($problem->submissions as $submission){
                if($submission->score > 0){
                    $curr_std = $submission->student;
                    $student = $students->where('id', $curr_std->id)->first();

                    $score[$student->id][$problem->name] = $submission->score;
                    $score[$student->id]['total'] += $submission->score;
                    if ($submission->is_accept == 'true'){
                        $t = Carbon::parse($submission->created_at);
                        $score[$student->id]['time'] += $t->timestamp;
                    }
                }
            }
        }

        foreach ($students as $student){
            if ($total_prob_score == $score[$student->id]['total']) {
                $score[$student->id]['complete'] = 'true';
            }
            $student['score'] = $score[$student->id];
        }

        //return $students;
        return view('scoreboard', ['lesson' => $lesson, 'students' => $students]);
    }

    public function changeGuide($id)
    {
        $lesson = Lesson::findOrFail($id);
        if($lesson->guide == 'true'){
            $lesson->guide = 'false';
            $msg = 'change guide to false success';
        }else{
            $lesson->guide = 'true';
            $msg = 'change guide to true success';
        }
        $lesson->save();

        return response()->json(['msg' => $msg]);
    }
}
