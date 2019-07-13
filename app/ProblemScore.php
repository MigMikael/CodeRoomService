<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemScore extends Model
{
    protected $table = 'problem_score';
    public $timestamps = false;

    protected $fillable = [
        'analysis_id',
        'class',
        'package',
        'enclose',
        'extends',
        'implements',
    ];

    public function problemAnalysis()
    {
        return $this->belongsTo('App\ProblemAnalysis', 'analysis_id');
    }
}
