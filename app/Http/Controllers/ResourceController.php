<?php

namespace App\Http\Controllers;

use App\File;
use App\Lesson;
use App\Resource;
use Illuminate\Http\Request;
use App\Traits\FileTrait;

class ResourceController extends Controller
{
    use FileTrait;

    public function store(Request $request)
    {
        $problem_id = $request->get('problem_id');

        if($request->hasFile('file')){
            $file = $request->file('file');
            $file = self::storeFile($file);
        }else{
            return response()->json(['msg' => 'resource file not found']);
        }

        $visible = $request->get('visible');

        $resource = [
            'problem_id' => $problem_id,
            'file_id' => $file->id,
            'visible' => $visible
        ];
        Resource::create($resource);

        return response()->json(['msg' => 'add resource file success']);
    }

    public function update(Request $request)
    {
        $id = $request->get('id');
        $file = File::findOrFail($id);
        $resource = Resource::findOrFail($file->id);

        if($request->hasFile('file')){
            $newFile = $request->file('file');
            $newFile = self::storeFile($newFile);
            $resource->file_id = $newFile->id;
            $resource->save();

            self::deleteFile($file);
            $file->delete();
        }else{
            return response()->json(['msg' => 'resource file not found']);
        }

        return response()->json(['msg' => 'edit resource file success']);
    }

    public function changeStatus($id)
    {
        $resource = Resource::where('file_id', $id)->first();
        if($resource->visible == 'true'){
            $resource->visible = 'false';
            $msg = 'change resource visible to false';
        }else{
            $resource->visible = 'true';
            $msg = 'change resource visible to true';
        }
        $resource->save();

        return response()->json(['msg' => $msg]);
    }

    public function changeVisible($id, $status)
    {
        if($status == 'on' || $status == 'off'){
            $lesson = Lesson::findOrFail($id);
            foreach ($lesson->problems as $problem){
                foreach ($problem->resources as $resource){
                    if($status == 'on'){
                        $resource->visible = 'true';
                    }else{
                        $resource->visible = 'false';
                    }
                    $resource->save();
                }
            }
            return response()->json(['msg' => 'change all resource visible to ' . $status]);

        }else{
            return response()->json(['msg' => 'wrong status on & off only']);
        }
    }

    public function destroy($id)
    {
        $resource = Resource::findOrFail($id);
        $file = File::findOrFail($resource->file_id);

        self::deleteFile($file);
        $file->delete();
        $resource->delete();

        return response()->json(['msg' => 'delete resource success']);
    }
}
