<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function show($id)
    {
        $image = File::findOrFail($id);
        $file = Storage::disk('local')->get($image->name);
        return response($file, 200)->header('Content-Type', $image->mime);
    }
}
