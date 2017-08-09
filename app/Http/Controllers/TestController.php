<?php

namespace App\Http\Controllers;

use App\TeacherCourse;
use Illuminate\Support\Facades\App;
use App\Course;
use App\Resource;
use App\File;
use App\Problem;
use App\Student;
use App\Lesson;
use App\Submission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Chumper\Zipper\Zipper;
use App\Traits\FileTrait;
use Storage;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Helper\TokenGenerate;

class TestController extends Controller
{
    use FileTrait;

    public function start(Request $request, $value)
    {
        $request->session()->put('number', $value);

        $value = $request->session()->all();

        return response()->json(['msg' => $value]);
    }

    public function end(Request $request)
    {

        $value = $request->session()->all();

        return response()->json(['msg' => $value]);
    }

    public function testStudent()
    {
        $token = 'eDAs36X1d3TDH8tZVdchphucYusqZq9S';
        $student = Student::where('token', '=', $token)->first();
        return $student;
    }

    public function test(Request $request)
    {
        /*session_start();
        if (isset($_SESSION['userID'])){
            $userID = $_SESSION['userID'];
        }else{
            $userID = 'null';
        }
        return 'store session complete : '.$userID;
        return Lesson::where('course_id', 1)->max('order') + 1;*/


        /*$identicon = new \Identicon\Identicon();
        $img = $identicon->getImageDataUri(45, 200);
        $des_path = storage_path() . '/app/';
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = $des_path . uniqid() . '.png';
        $success = file_put_contents($file, $data);
        return $success;*/

        $file = $request->file('file');
        $file = self::storeFile($file);

        $des_path = storage_path() . '\\app\\';
        $filePath = storage_path() . '\\app\\' . $file->name;

        $zipper = new Zipper();
        $zipper->make($filePath)->extractTo($des_path);

        return response()->json(['msg' => 'extract complete']);
    }

    public function test2()
    {
        /*$prob_name = 'LastDigit';
        $path = $prob_name . '/src' ;
        Log::info($path);
        $files = Storage::disk('local')->allFiles($path);
        return $files;*/

        /*$file = 'LastDigit/src/com/example/driver/LastDigit.java';
        $file = explode('/src/', $file);

        if(strrpos($file[1], '/')) {
            Log::info(strrpos($file[1], '/'));
            $package = substr($file[1], 0, strrpos($file[1], '/'));
            $package = str_replace('/','.', $package);
            return $package;
        }*/

        /*$problem = Problem::find(1);
        $problemFile = $problem->problemFiles->first();

        return $problemFile->inputs->count();*/

        /*$file = File::find(48);
        Storage::delete($file->name);
        return 'delete success';*/

        /*$problemAnalysis = Student::find([1, 2, 3, 8, 16]);
        $results = Student::find([1, 2, 4]);

        $diff = $problemAnalysis->diff($results);
        foreach ($diff as $d){
            echo $d->id." ";
        }*/

        /*$submission = Submission::orderBy('id', 'desc')->first();
        $problem = $submission->problem;

        $wrong = [];
        $results = [];
        foreach ($submission->submissionFiles as $submissionFile){
            $results = $submissionFile->results;
        }

        $problemAnalysis = [];
        foreach ($problem->problemFiles as $problemFile){
            $problemAnalysis = $problemFile->problemAnalysis;
        }

        $class_diffs = $problemAnalysis->diff($results);
        foreach ($class_diffs as $diff){
            array_push($wrong, 'ไม่มีคลาส '.$diff->class);
        }*/

        /*$lesson = Lesson::findOrFail(1);
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
            array_push($data_score, [
                'id' => $student->student_id,
                'name' => $student->name,
                'score' => $student->score
            ]);
        }

        $filename = 'score-'.Carbon::now();
        $filename = str_replace(' ', '-', $filename);
        Excel::create($filename, function($excel) use ($data_score) {
            $excel->sheet('sheet1', function($sheet) use ($data_score) {
                $sheet->fromArray($data_score);
            });
        })->download('xlsx');*/
        //return 'create excel complete';

        /*$problem = new Problem();
        return $problem->id;*/
    }

    public function test3()
    {
        /*$lesson = Lesson::withCount(['problems'])->findOrFail(1);

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

        return $lesson;*/

        /*$course = Course::find(1);
        $dt = Carbon::parse($course->created_at);
        return $dt->timestamp;*/

        $course = Course::findOrFail(2);
        $course_path = $course->id . '_' . $course->name;
        $course_path = str_replace(' ', '_', $course_path);
        return $course_path;
    }

    public function test4()
    {
        $base_path = '2_Computer_Programming_II';
        $des_path = '3_Computer_Programming_II';
        $directories = Storage::directories($base_path);
        foreach ($directories as $directory) {
            $files = Storage::allFiles($directory);
            foreach ($files as $file){
                $new_file = str_replace($base_path, $des_path, $file);
                Storage::copy($file, $new_file);
            }
        }

        /*$problem_id_3 = "3";
        Storage::makeDirectory($des_path . '/' . $problem_id_3);
        $problem_id_4 = "4";
        Storage::makeDirectory($des_path . '/' . $problem_id_4);*/

        $directories = Storage::directories($des_path);
        foreach ($directories as $directory){
            $files = Storage::allFiles($directory);
            foreach ($files as $file){
                Log::info($file);
                $temp = explode('/', $file);
                $temp[1] = (int)($temp[1]);
                $temp[1] = $temp[1] + 2;
                $temp[1] = (string)$temp[1];

                $new_file = '';
                foreach ($temp as $t){
                    $new_file .= $t.'/';
                }
                Log::info($new_file);
                // 3_Computer_Programming_II/1/PrimeNumber/.classpath
                Storage::copy($file, $new_file);
            }
            Storage::deleteDirectory($directory);
        }

        return $directories;
    }

    public function test5()
    {
        /*$id = $request->get('course_id');
        $new_name = $request->get('name');*/

        $id = 2;
        $new_name = "Computer Programming II";

        $course = Course::findOrFail($id);

        $new_token = (new TokenGenerate())->generate(6);
        $new_course = [
            'name' => $new_name,
            'image' => $course->image,
            'color' => $course->color,
            'status' => $course->status,
            'token' => $new_token,
            'mode' => 'normal'
        ];
        $new_course = Course::create($new_course);
        $teachers_course = TeacherCourse::where('course_id', $course->id)->get();
        foreach ($teachers_course as $teacher_course){
            $new_teacher_course = [
                'teacher_id' => $teacher_course->teacher_id,
                'course_id' => $new_course->id,
                'status' => $teacher_course->status
            ];
            TeacherCourse::create($new_teacher_course);
        }

        $course_path = $course->id . '_' . $course->name;
        $course_path = str_replace(' ', '_', $course_path);
        $new_course_path = $new_course->id . '_' . $new_course->name;
        $new_course_path = str_replace(' ', '_', $new_course_path);

        $directories = Storage::directories($course_path);
        foreach ($directories as $directory) {
            $files = Storage::allFiles($directory);
            foreach ($files as $file){
                $new_file = str_replace($course_path, $new_course_path, $file);
                Storage::copy($file, $new_file);
            }
        }

        foreach ($course->lessons as $lesson){
            $new_lesson = [
                'name' => $lesson->name,
                'course_id' => $new_course->id,
                'status' => $lesson->status,
                'order' => $lesson->order,
                'open_submit' => $lesson->open_submit,
            ];
            $new_lesson = Lesson::create($new_lesson);
            foreach ($lesson->problems as $problem){
                $new_problem = [
                    'lesson_id' => $new_lesson->id,
                    'name' => $problem->name,
                    'description' => $problem->description,
                    'evaluator' => $problem->evaluator,
                    'order' => $problem->order,
                    'question' => $problem->question,
                    'timelimit' => $problem->tiemlimit,
                    'memorylimit' => $problem->memorylimit,
                    'is_parse' => $problem->is_parse,
                    'score' => $problem->score,
                ];
                $new_problem = Problem::create($new_problem);

                $files = Storage::allFiles($new_course_path . '/' . $problem->id);
                foreach ($files as $file){
                    $temp = explode('/', $file);
                    $temp[1] = (int)($temp[1]);
                    $temp[1] = $new_problem->id;
                    $temp[1] = (string)$temp[1];

                    $new_file = '';
                    foreach ($temp as $t){
                        $new_file .= $t.'/';
                    }
                    Storage::copy($file, $new_file);
                }
                Storage::deleteDirectory($new_course_path . '/' . $problem->id);
                foreach ($problem->problemFiles as $problemFile){
                    $new_prob_file = [
                        'problem_id' => $new_problem->id,
                        'package' => $problemFile->package,
                        'filename' => $problemFile->filename,
                        'mime' => $problemFile->mime,
                        'code' => $problemFile->code
                    ];
                    $new_prob_file = ProblemFile::create($new_prob_file);

                    foreach ($problemFile->inputs as $input){
                        $new_input = [
                            'problem_file_id' => $new_prob_file->id,
                            'version' => $input->version,
                            'filename' => $input->filename,
                            'content' => $input->content
                        ];
                        ProblemInput::create($new_input);
                    }

                    foreach ($problemFile->inputs as $output){
                        $new_output = [
                            'problem_file_id' => $new_prob_file->id,
                            'version' => $output->version,
                            'filename' => $output->filename,
                            'content' => $output->content,
                            'score' => $output->score
                        ];
                        ProblemOutput::create($new_output);
                    }

                    foreach ($problemFile->problemAnalysis as $analysis){
                        $new_analysis = [
                            'problem_file_id' => $new_prob_file->id,
                            'class' => $analysis->class,
                            'package' => $analysis->package,
                            'enclose' => $analysis->enclose,
                            'extends' => $analysis->extends,
                            'implements' => $analysis->implements
                        ];
                        $new_analysis = ProblemAnalysis::create($new_analysis);
                        $problem_score = $analysis->score;

                        $new_score = [
                            'analysis_id' => $new_analysis->id,
                            'class' => $problem_score->class,
                            'package' => $problem_score->package,
                            'enclose' => $problem_score->enclose,
                            'extends' => $problem_score->extends,
                            'implements' => $problem_score->implements
                        ];
                        ProblemScore::create($new_score);

                        foreach ($analysis->attributes as $attribute){
                            $new_attribute = [
                                'analysis_id' => $new_analysis->id,
                                'access_modifier' => $attribute->access_modifier,
                                'non_access_modifier' => $attribute->non_access_modifier,
                                'data_type' => $attribute->data_type,
                                'name' => $attribute->name,
                                'score' => $attribute->score
                            ];
                            ProblemAttribute::create($new_attribute);
                        }

                        foreach ($analysis->constructors as $constructor){
                            $new_constructor = [
                                'analysis_id' => $new_analysis->id,
                                'access_modifier' => $constructor->access_modifier,
                                'name' => $constructor->name,
                                'parameter' => $constructor->parameter,
                                'score' => $constructor->score
                            ];
                            ProblemConstructor::create($new_constructor);
                        }

                        foreach ($analysis->methods as $method){
                            $new_method = [
                                'analysis_id' => $new_analysis->id,
                                'access_modifier' => $method->access_modifier,
                                'non_access_modifier' => $method->non_access_midifier,
                                'return_type' => $method->return_type,
                                'name' => $method->name,
                                'parameter' => $method->parameter,
                                'recursive' => $method->recursive,
                                'loop' => $method->loop,
                                'score' => $method->score,
                            ];
                            ProblemMethod::create($new_method);
                        }
                    }
                }
            }
        }

        return response()->json(['msg' => 'clone course complete']);
    }

    public function test6()
    {
        /*$submission = Submission::findOrFail(1);
        $student = $submission->student;
        foreach ($submission->submissionFiles as $submissionFile){
            Storage::put($submissionFile->filename, $submissionFile->code);
        }*/
        $course = Course::find(1);
        return $course->lessons->count();
    }

}
