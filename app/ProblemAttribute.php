<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemAttribute extends Model
{
    public $timestamps = false;
    protected $table = 'problem_attribute';

    protected $fillable = [
        'analysis_id',
        'access_modifier',
        'non_access_modifier',
        'data_type',
        'name',
        'score',
    ];

    public function problemAnalysis()
    {
        return $this->belongsTo('App\ProblemAnalysis', 'analysis_id');
    }
}
