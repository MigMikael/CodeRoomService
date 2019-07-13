<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemAnalysis extends Model
{
    protected $table = 'problem_analysis';
    public $timestamps = true;

    protected $fillable = [
        'problem_file_id',
        'class',
        'package',
        'enclose',
        'extends',
        'implements'
    ];

    public function problemFile()
    {
        return $this->belongsTo('App\ProblemFile', 'problem_file_id');
    }

    public function attributes()
    {
        return $this->hasMany('App\ProblemAttribute', 'analysis_id');
    }

    public function constructors()
    {
        return $this->hasMany('App\ProblemConstructor', 'analysis_id');
    }

    public function methods()
    {
        return $this->hasMany('App\ProblemMethod', 'analysis_id');
    }

    public function score()
    {
        return $this->hasOne('App\ProblemScore', 'analysis_id');
    }
}
