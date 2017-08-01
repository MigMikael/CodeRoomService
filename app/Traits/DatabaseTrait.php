<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 8/1/2017
 * Time: 21:29
 */

namespace App\Traits;
use App\Lesson;
use App\StudentLesson;
use App\Submission;
use Log;

trait DatabaseTrait
{
    public function updateLessonProgress($lesson)
    {
        foreach ($lesson->problems as $problem){
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

                $student_lesson->progress = $progress;
                $student_lesson->save();
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

        Log::info('Accept Count : '.$accept_count);
        Log::info('Prob Count : '.$prob_count);
        $progress = ($accept_count/$prob_count)*100;

        $student_lesson->progress = $progress;
        $student_lesson->save();
    }
}