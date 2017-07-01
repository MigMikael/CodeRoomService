<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 6/19/2017
 * Time: 1:02
 */
namespace App\Traits;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;
use Chumper\Zipper\Zipper;

/**
 * Trait FileTrait
 * @package App\Traits
 */
trait FileTrait
{
    // Todo fix bug file name cannot in thai
    /**
     * storeFile.
     *
     * store file using Laravel _filesystem_
     * and store file record in file table.
     *
     * @param $file
     * @return \App\File
     */
    public function storeFile($file)
    {
        $ex = $file->getClientOriginalExtension();
        Storage::put($file->getFilename(). '.' . $ex, File::get($file));

        $fileRecord = [
            'name' => $file->getFilename(). '.' . $ex,
            'mime' => $file->getClientMimeType(),
            'original_name' => $file->getClientOriginalName(),
        ];
        $file = \App\File::create($fileRecord);
        return $file;
    }

    /**
     * path
     *
     * get path to specific file in project Storage
     * determine by environment that currently operate.
     *
     * @param $file
     * @return string
     */
    public function path($file)
    {
        if (App::environment('local')) {
            $path = storage_path() . '\\app\\' . $file->name;
        }else{
            $path = storage_path() . '/app/' . $file->name;
        }

        return $path;
    }

    /**
     * problem_path.
     *
     * get path to specific problem file
     * determine by environment thai currently operate.
     *
     * @param $prob_id
     * @return string
     */
    public function problem_path($prob_id)
    {
        if (App::environment('local')) {
            $path = storage_path() . '\\app\\problem\\' . $prob_id . '\\';
        }else{
            $path = storage_path() . '/app/problem/'. $prob_id . '/';
        }

        return $path;
    }

    /**
     * @param $submit_id
     * @return string
     */
    public function submission_path($submit_id)
    {
        if (App::environment('local')) {
            $path = storage_path() . '\\app\\submission\\'. $submit_id . '\\';
        }else{
            $path = storage_path() . '/app/submission/'. $submit_id . '/';
        }

        return $path;
    }

    /**
     * @param $file
     * @return mixed
     */
    public function getFile($file)
    {
        $file = Storage::get($file);
        return $file;
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getFiles($path)
    {
        $files = Storage::allFiles($path);
        return $files;
    }

    /**
     * @param $name
     * @return array
     */
    public function storeQuestion($name)
    {
        $question_file = [
            'name' => $name.'.pdf',
            'mime' => 'application/pdf',
            'original_name' => $name. '.pdf'
        ];
        $question_file = \App\File::create($question_file);
        return $question_file;
    }

    /**
     * @param $file
     * @param $prob_id
     */
    public function unzipProblem($file, $prob_id)
    {
        $des_path = self::problem_path($prob_id);
        $filePath = self::path($file);
        $zipper = new Zipper();
        $zipper->make($filePath)->extractTo($des_path);
    }

    /**
     * @param $file
     * @param $submit_id
     */
    public function unzipSubmission($file, $submit_id)
    {
        $des_path = self::submission_path($submit_id);
        $filePath = self::path($file);
        $zipper = new Zipper();
        $zipper->make($filePath)->extractTo($des_path);
        //Storage::delete($file->name);
    }

    /**
     * @param $file
     */
    public function deleteFile($file)
    {
        Storage::delete($file->name);
    }
}
