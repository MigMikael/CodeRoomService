<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 6/19/2017
 * Time: 1:02
 */
namespace App\Traits;
use Illuminate\Support\Facades\File;
use Log;
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
     * @param $problem
     * @return string
     * @internal param $prob_id
     */
    public function problem_path($problem)
    {
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);
        if (App::environment('local')) {
            $path = storage_path() . '\\app\\'.$course_name.'\\' . $problem->id . '\\';
        }else{
            $path = storage_path() . '/app/'.$course_name.'/'. $problem->id . '/';
        }

        return $path;
    }

    public function question_path($problem)
    {
        $course = $problem->lesson->course;
        $course_name = $course->id.'_'.$course->name;
        $course_name = str_replace(' ', '_', $course_name);
        if (App::environment('local')) {
            $path = storage_path() . '\\app\\'.$course_name.'\\' . $problem->id . '\\' . $problem->name . '\\' . $problem->name . '.pdf';
        }else{
            $path = storage_path() . '/app/'.$course_name.'/'. $problem->id . '/' . $problem->name . '/' . $problem->name . '.pdf';
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
     * @param $problem
     * @internal param $prob_id
     */
    public function unzipProblem($file, $problem)
    {
        $des_path = self::problem_path($problem);
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

    public function checkFileStructure($problem)
    {
        $is_correct = true;
        $wrong_msg = [];

        $prob_path = self::problem_path($problem);
        $prob_path = $prob_path . $problem->name;
        if(!file_exists($prob_path)){
            $is_correct = false;
            array_push($wrong_msg, ['problem_path' => 'folder in zip and problem name not match']);
            Log::info('wrong problem path');
        }

        $question_path = self::question_path($problem);
        if(!file_exists($question_path)){
            $is_correct = false;
            array_push($wrong_msg, ['question_path' => 'question file name and problem name not match']);
            Log::info('wrong question path');
        }

        if ($is_correct){
            return $is_correct;
        }else{
            return $wrong_msg;
        }
    }

    public function checkTestCase($problem)
    {
        $hasTestCase = false;
        $prob_path = self::problem_path($problem);
        $testCase_path = $prob_path . $problem->name . '/testCase';
        if(file_exists($testCase_path)){
            $hasTestCase = true;
        }
        return $hasTestCase;
    }

    /**
     * @param $file
     */
    public function deleteFile($file)
    {
        Storage::delete($file->name);
    }

    public function getMime($file)
    {
        $mime = Storage::mimeType($file);
        return $mime;
    }
}
