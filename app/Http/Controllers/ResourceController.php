<?php

namespace App\Http\Controllers;

use App\File;
use App\Resource;
use Illuminate\Http\Request;
use App\Traits\FileTrait;

class ResourceController extends Controller
{
    use FileTrait;

    public function store(Request $request)
    {
        $problem_id = $request->get('problem_id');

        if($request->has('file')){
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

    public function changeStatus($id)
    {
        $resource = Resource::findOrFail($id);
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
