<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

/**
 * Trait ImageTrait.
 *
 * provide image manipulation to reuse code and reduce complexity of controller.
 *
 * @package App\Traits
 */
trait ImageTrait
{
    /**
     * genImage.
     *
     * Generate user Avatar image from user _id_
     * this will guarantee unique fo an image since
     * user id is auto incremental.
     *
     * * user _Identicon_ package
     * * size of Avatar Image is 100
     *
     * @see https://packagist.org/packages/yzalis/identicon Documentation of Identicon
     *
     * @author  MigMikale <chanachai_mig@hotmail.com>
     *
     * @param int $id This is an id of user
     *
     * @return \App\File
     */
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

    /**
     * storeImage.
     *
     * store image file using Laravel _filesystem_
     * and compress image if size is larger than 400 kb
     *
     * @see https://laravel.com/docs/5.4/filesystem Documentation of Laravel Filesystem
     * @author  MigMikale <chanachai_mig@hotmail.com>
     * @param $file
     * @return \App\File
     */
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

        $size = Storage::disk('local')->size($file->name);
        if ($size > 400000) {
            self::compress($file);
        }
        return $file;
    }

    /**
     * compress.
     *
     * compress image file using _imagejpeg_
     *
     * * current quality is 25
     *
     * @see https://www.apptha.com/blog/how-to-reduce-image-file-size-while-uploading-using-php-code/ for more info
     * @author  MigMikale <chanachai_mig@hotmail.com>
     * @param $file
     * @return string
     */
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
            $image = imagecreatefromjpeg($img_path);
        }

        imagejpeg($image, $des_path, 25);

        $file->name = 'compress_' . $file->name;
        $file->save();

        return 'success';
    }

    /**
     * deleteImage.
     *
     * ลบไฟล์ที่จัดเก็บในระบบโดยใช้ _Laravel Filesystem_
     *
     * @author  MigMikale <chanachai_mig@hotmail.com>
     * @param $file
     * @return void
     */
    public function deleteImage($file)
    {
        Storage::delete($file->name);
    }
}