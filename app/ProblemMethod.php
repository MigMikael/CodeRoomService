<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemMethod extends Model
{
    public $timestamps = false;
    protected $table = 'problem_method';

    protected $fillable = [
        'analysis_id',
        'access_modifier',
        'non_access_modifier',
        'return_type',
        'name',
        'parameter',
        'recursive',
        'loop',
        'score',
    ];

    public function problemAnalysis()
    {
        return $this->belongsTo('App\ProblemAnalysis', 'analysis_id');
    }
}
