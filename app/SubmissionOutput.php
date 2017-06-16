<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionOutput extends Model
{
    public $timestamps = false;
    protected $table = 'submission_output';

    protected $fillable = [
        'submission_file_id',
        'content',
        'score',
        'error',
    ];

    public function submissionFile()
    {
        return $this->belongsTo('App\SubmissionFile', 'submission_file_id');
    }
}
