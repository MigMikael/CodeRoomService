<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);
        return $announcement;
    }

    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $title = $request->get('title');
        $content = $request->get('content');

        if ($request->has('priority')){
            $priority = $request->get('priority');
        }else{
            $priority = 2;
        }

        $announce = [
            'course_id' => $course_id,
            'title' => $title,
            'content' => $content,
            'priority' => $priority
        ];

        Announcement::create($announce);

        return response()->json(['msg' => 'create announcement success']);
    }

    public function update(Request $request)
    {
        $id = $request->get('id');
        $announce = Announcement::findOrFail($id);

        $announce->title = $request->get('title');
        $announce->content = $request->get('content');

        if ($request->has('priority')){
            $announce->priority = $request->get('priority');
        }
        $announce->save();

        return response()->json(['msg' => 'update announcement success']);
    }

    public function delete($id)
    {
        $announce = Announcement::findOrFail($id);
        $announce->delete();

        return response()->json(['msg' => 'delete announcement success']);
    }
}
