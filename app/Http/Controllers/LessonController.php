<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Problem;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function show($id)
    {
        $lesson = Lesson::withCount(['problems'])->findOrFail($id);

        $problems = Problem::where('lesson_id', '=', $lesson->id)->ordered()->get();
        $lesson['problems'] = $problems;

        foreach ($lesson['problems'] as $problem){
            $problem['question'] = url('problem/getQuestion/'.$problem->id);
        }

        return $lesson;
    }

    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $input = [
            'name' => $request->get('name'),
            'course_id' => $course_id,
            'status' => 'false',
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
        $new_name = $request->get('name');

        $lesson = Lesson::findOrFail($lesson_id);
        $lesson->name = $new_name;
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
}
