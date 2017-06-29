<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 6/18/2017
 * Time: 22:03
 */

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

trait ImageTrait
{
    public function genImage($id)
    {
        $identicon = new \Identicon\Identicon();
        $img = $identicon->getImageDataUri($id, 100);

        $des_path = storage_path() . '/app/';

        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $file = $des_path . $id . '.png';
        file_put_contents($file, $data);

        $fileRecord = [
            'name' => $id . '.png',
            'mime' => 'image/png',
            'original_name' => $id . '.png',
        ];
        $file = \App\File::create($fileRecord);

        return $file;
    }

    public function storeImage($file)
    {
        $ex = $file->getClientOriginalExtension();
        Storage::disk('local')->put($file->getFilename(). '.' . $ex, File::get($file));
        $fileRecord = [
            'name' => $file->getFilename(). '.' . $ex,
            'mime' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ];
        $file = \App\File::create($fileRecord);
        //self::resizeImage($file, $type);

        $size = Storage::disk('local')->size($file->name);
        if ($size > 400000) {
            self::compress($file);
        }
        return $file;
    }

    public function compress($file)
    {
        if (App::environment('local')) {
            //windows path
            $img_path = storage_path() . '\\app\\' . $file->name;
            $des_path = storage_path() . '\\app\\compress_' . $file->name;
        } else {
            //linux path
            $img_path = storage_path() . '/app/' . $file->name;
            $des_path = storage_path() . '/app/compress_' . $file->name;
        }

        if ($file->mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($img_path);

        } elseif ($file->mime == 'image/gif') {
            $image = imagecreatefromgif($img_path);

        } elseif ($file->mime == 'image/png') {
            $image = imagecreatefrompng($img_path);

        } else {
            return abort(500);
        }

        imagejpeg($image, $des_path, 25);

        $file->name = 'compress_' . $file->name;
        $file->save();

        return 'success';
    }

    public function deleteImage($file)
    {
        Storage::delete($file->name);
    }
}