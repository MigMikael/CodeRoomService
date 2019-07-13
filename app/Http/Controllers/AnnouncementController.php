<?php

namespace App\Http\Controllers;

use App\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function show($announce_id)
    {
        $announcement = Announcement::findOrFail($announce_id);
        return $announcement;
    }

    public function store(Request $request)
    {
        $course_id = $request->get('course_id');
        $title = $request->get('title');
        $content = $request->get('content');

        // priority has two value 1 = normal : 2 = express
        if ($request->has('priority')){
            $priority = $request->get('priority');
        }else{
            $priority = 1;
        }

        if ($request->has('show')){
            $show = $request->get('show');
        }else{
            $show = 'false';
        }

        $announce = [
            'course_id' => $course_id,
            'title' => $title,
            'content' => $content,
            'priority' => $priority,
            'show' => $show
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
        if ($request->has('show')){
            $announce->show = $request->get('show');
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

    public function changeStatus($id)
    {
        $announce = Announcement::findOrFail($id);
        if($announce->show == 'true'){
            $announce->show = 'false';
        }else{
            $announce->show = 'true';
        }
        $announce->save();

        return response()->json(['msg' => 'change status complete']);
    }
}
