<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemFile extends Model
{
    public $timestamps = false;
    protected $table = 'problem_file';

    protected $fillable = [
        'problem_id',
        'package',
        'filename',
        'mime',
        'code'
    ];

    public function problem()
    {
        return $this->belongsTo('App\Problem');
    }

    public function problemAnalysis()
    {
        return $this->hasMany('App\ProblemAnalysis', 'problem_file_id');
    }

    public function inputs()
    {
        return $this->hasMany('App\ProblemInput', 'problem_file_id');
    }

    public function outputs()
    {
        return $this->hasMany('App\ProblemOutput', 'problem_file_id');
    }
}
