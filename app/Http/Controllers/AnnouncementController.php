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

        $announce = [
            'course_id' => $course_id,
            'title' => $title,
            'content' => $content,
            'priority' => 2
        ];

        Announcement::create($announce);

        return response()->json(['msg' => 'create announcement success']);
    }

    public function update(Request $request)
    {
        $id = $request->get('id');
        $title = $request->get('title');
        $content = $request->get('content');

        $announce = Announcement::findOrFail($id);
        $announce->title = $title;
        $announce->content = $content;
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
