<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 8/1/2017
 * Time: 21:29
 */

namespace App\Traits;
use App\Lesson;
use App\StudentCourse;
use App\StudentLesson;
use App\Submission;
use Log;

trait DatabaseTrait
{
    public function updateLessonProgress($lesson)
    {
        foreach ($lesson->problems as $problem){
            $course = $lesson->course;
            foreach ($problem->submissions as $submission){
                $student = $submission->student;
                $student_lesson = StudentLesson::where([
                    ['student_id', $student->id],
                    ['lesson_id', $lesson->id]
                ])->first();

                if(sizeof($student_lesson) == 0){
                    $student_lesson = [
                        'student_id' => $student->id,
                        'lesson_id' => $lesson->id,
                        'progress' => 0
                    ];
                    $student_lesson = StudentLesson::create($student_lesson);
                }
                $prob_count = $lesson->problems->count();

                $accept_count = 0;
                foreach ($lesson->problems as $problem){
                    $accept_submission = Submission::where([
                        ['student_id', $student->id],
                        ['problem_id', $problem->id],
                        ['is_accept', 'true']
                    ])->first();

                    if(sizeof($accept_submission) > 0){
                        $accept_count++;
                    }
                }

                /*Log::info('Accept Count : '.$accept_count);
                Log::info('Prob Count : '.$prob_count);*/
                $progress = ($accept_count/$prob_count)*100;

                if($progress < 10){
                    $progress = round($progress, 1);
                }else{
                    $progress = round($progress);
                }

                $student_lesson->progress = $progress;
                $student_lesson->save();

                $this->updateCourseProgress($course, $student);
            }
        }
    }

    public function updateStudentProgress($submission)
    {
        $problem = $submission->problem;
        $student = $submission->student;

        $lesson = $problem->lesson;
        $student_lesson = StudentLesson::where([
            ['student_id', $student->id],
            ['lesson_id', $lesson->id]
        ])->first();

        if(sizeof($student_lesson) == 0){
            $student_lesson = [
                'student_id' => $student->id,
                'lesson_id' => $lesson->id,
                'progress' => 0
            ];
            $student_lesson = StudentLesson::create($student_lesson);
        }
        $prob_count = $lesson->problems->count();

        $accept_count = 0;
        foreach ($lesson->problems as $problem){
            $accept_submission = Submission::where([
                ['student_id', $student->id],
                ['problem_id', $problem->id],
                ['is_accept', 'true']
            ])->first();

            if(sizeof($accept_submission) > 0){
                $accept_count++;
            }
        }

        //Log::info('Accept Count : '.$accept_count);
        //Log::info('Prob Count : '.$prob_count);
        $progress = ($accept_count/$prob_count)*100;

        if($progress < 10){
            $progress = round($progress, 1);
        }else{
            $progress = round($progress);
        }

        $student_lesson->progress = $progress;
        $student_lesson->save();

        $course = $lesson->course;
        $this->updateCourseProgress($course, $student);
    }

    public function updateCourseProgress($course, $student)
    {
        $studentCourse = StudentCourse::where([
            ['course_id', $course->id],
            ['student_id', $student->id]
        ])->first();

        if(sizeof($studentCourse) < 1){
            $studentCourse = [
                'course_id' => $course->id,
                'student_id' => $student->id,
                'status' => 'enable',
                'progress' => 0
            ];
            $studentCourse = StudentCourse::create($studentCourse);
        }

        $studentLessons = StudentLesson::where('student_id', $student->id)->get();
        $sum_progress = 0;

        foreach ($studentLessons as $studentLesson){
            $lesson = Lesson::find($studentLesson->lesson_id);
            $this_course = $lesson->course;
            if($this_course->id == $course->id){
                $sum_progress += $studentLesson->progress;
            }
        }

        $amount_progress = $course->lessons->count();
        if($amount_progress != 0){
            $courseProgress = $sum_progress / $amount_progress;
        }else {
            $courseProgress = 0;
        }
        $studentCourse->progress = $courseProgress;
        $studentCourse->save();
    }
}