<?php

namespace App\Http\Controllers;

use App\Announcement;
use App\Course;
use App\Helper\TokenGenerate;
use App\Lesson;
use App\Problem;
use App\ProblemAnalysis;
use App\ProblemAttribute;
use App\ProblemConstructor;
use App\ProblemFile;
use App\ProblemInput;
use App\ProblemMethod;
use App\ProblemOutput;
use App\ProblemScore;
use App\Student;
use App\StudentCourse;
use App\StudentLesson;
use App\TeacherCourse;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use Storage;
use Log;

class CourseController extends Controller
{
    use ImageTrait;

    public function index()
    {
        $courses = Course::enable()->get();
        return $courses;
    }

    public function store(Request $request)
    {
        $image = self::storeImage($request->file('image'));
        $course = [
            'name' => $request->get('name'),
            'color' => $request->get('color'),
            'token' => (new TokenGenerate())->generate(6),
            'image' => $image->id,
            'status' => 'enable',
            'mode' => 'normal',
        ];
        $course = Course::create($course);

        return $course;
    }

    public function member($id)
    {
        $course = Course::findOrFail($id);
        foreach ($course->students as $student){
            $student->pivot;
            $student->lessons;
        }
        foreach ($course->teachers as $teacher){
            $teacher->courses;
            $teacher->pivot;
        }

        return $course;
    }

    public function join(Request $request)
    {
        $course_id = $request->get('course_id');
        $student_id = $request->get('student_id');
        $token = $request->get('token');

        $course = Course::findOrFail($course_id);

        $student_course = StudentCourse::where([
            ['student_id', '=', $student_id],
            ['course_id', '=', $course_id]
        ])->first();

        if(sizeof($student_course) > 0){
            return response()->json(['msg' => 'already join this course']);
        }

        if($course->token == $token){
            $student_course = [
                'student_id' => $student_id,
                'course_id' => $course_id,
                'status' => 'enable',
                'progress' => 0
            ];

            StudentCourse::create($student_course);
            return response()->json(['msg' => 'join course success']);

        }else{
            return response()->json(['msg' => 'token mismatch']);

        }
    }

    public function showStudent(Request $request, $student_id, $course_id)
    {
        $course = Course::withCount([
            'students', 'teachers', 'lessons', 'badges', 'announcements'
        ])->findOrFail($course_id);

        $student = Student::where('student_id', $student_id)->firstOrFail();

        if($course->mode == 'normal'){                  // normal mode
            $course['lessons'] = Lesson::where('course_id', $course_id)
                ->normal()
                ->ordered()
                ->get();
        }else{                                          // test mode
            if ($student->ip == ''){
                $current_ip = $request->getClientIp();
                $student->ip = $current_ip;
                $student->save();
            }

            $current_ip = $request->getClientIp();

            if($current_ip != $student->ip){
                return response()->json(['msg' => 'you already login from another machine']);
            }
            $course['lessons'] = Lesson::where('course_id', $course_id)
                ->test()
                ->orderBy('order', 'desc')
                ->take(1)
                ->get();
        }

        foreach ($course->lessons as $lesson){
            $student_lesson = StudentLesson::where([
                ['student_id', '=', $student->id],
                ['lesson_id', '=', $lesson->id]
            ])->first();
            if(sizeof($student_lesson) < 1){
                $lesson['progress'] = 0;
            }else{
                $lesson['progress'] = $student_lesson->progress;
            }
            $lesson['problems_count'] = $lesson->problems()->count();
        }
        $course->badges;
        $course->announcements;

        return $course;
    }

    public function showTeacher($course_id)
    {
        $course = Course::withCount([
            'students', 'teachers', 'lessons', 'badges', 'announcements'
        ])->findOrFail($course_id);

        $course['lessons'] = Lesson::where('course_id', '=', $course_id)
            ->ordered()
            ->get();
        foreach ($course['lessons'] as $lesson){
            $lesson['problems_count'] = $lesson->problems()->count();
        }
        $course->badges;
        $course->announcements;
        $course->students;
        $course->makeVisible('token');

        return $course;
    }

    public function changeStatus($course_id)
    {
        $course = Course::findOrFail($course_id);
        if($course->status == 'enable'){
            $course->status = 'disable';
        }else{
            $course->status = 'enable';
        }
        $course->save();

        return response()->json(['msg' => 'change course status success']);
    }

    public function changeMode($course_id)
    {
        $course = Course::findOrFail($course_id);
        if($course->mode == 'normal'){
            $course->mode = 'test';
            $students = $course->students;
            foreach ($students as $student){
                $student->ip = '';
                $student->save();
            }
        }else{
            $course->mode = 'normal';
        }
        $course->save();

        return response()->json(['msg' => 'change course mode success']);
    }

    public function addTeacher(Request $request)
    {
        $course_id = $request->get('course_id');
        $course = Course::findOrFail($course_id);

        $teachers = $request->get('teachers');
        foreach ($teachers as $teacher){
            $teacher_course = [
                'teacher_id' => $teacher['id'],
                'course_id' => $course->id,
                'status' => 'enable'
            ];
            TeacherCourse::firstOrCreate($teacher_course);
        }

        return response()->json(['msg' => 'add Teacher success']);
    }

    public function teacherMember($course_id)
    {
        $course = Course::findOrFail($course_id);
        $teachers = $course->teachers;
        foreach ($teachers as $teacher){
            $teacher->makeHidden('role');
        }
        return $teachers;
    }

    public function cloneCourse(Request $request)
    {
        $id = $request->get('course_id');
        $new_name = $request->get('name');

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
                    'timelimit' => $problem->timelimit,
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

                    foreach ($problemFile->outputs as $output){
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
                                'non_access_modifier' => $method->non_access_modifier,
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

        foreach ($course->announcements as $announcement){
            $new_announcement = [
                'course_id' => $new_course->id,
                'title' => $announcement->title,
                'content' => $announcement->content,
                'priority' => $announcement->priority,
                'show' => 'false'
            ];
            Announcement::create($new_announcement);
        }

        return response()->json(['msg' => 'clone course complete']);
    }
}
