<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submission';
    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'problem_id',
        'sub_num',
        'is_accept',
        'score'
    ];

    public function student()
    {
        return $this->belongsTo('App\Student');
    }

    public function problem()
    {
        return $this->belongsTo('App\Problem');
    }

    public function submissionFiles()
    {
        return $this->hasMany('App\SubmissionFile');
    }
}
