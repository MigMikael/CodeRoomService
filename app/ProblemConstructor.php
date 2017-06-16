<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemConstructor extends Model
{
    public $timestamps = false;
    protected $table = 'problem_constructor';

    protected $fillable = [
        'analysis_id',
        'access_modifier',
        'name',
        'parameter',
        'score',
    ];

    public function problemAnalysis()
    {
        return $this->belongsTo('App\ProblemAnalysis', 'analysis_id');
    }
}
