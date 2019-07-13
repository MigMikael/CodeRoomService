<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    public $timestamps = false;
    protected $table = 'submission_file';

    protected $fillable = [
        'submission_id',
        'package',
        'filename',
        'mime',
        'code'
    ];

    public function submission()
    {
        return $this->belongsTo('App\Submission');
    }

    public function results()
    {
        return $this->hasMany('App\Result', 'submission_file_id');
    }

    public function outputs()
    {
        return $this->hasMany('App\SubmissionOutput', 'submission_file_id');
    }
}
