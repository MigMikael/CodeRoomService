<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemOutput extends Model
{
    public $timestamps = false;
    protected $table = 'problem_output';

    protected $fillable = [
        'problem_file_id',
        'version',
        'filename',
        'content',
        'score',
    ];

    public function problemFile()
    {
        return $this->belongsTo('App\ProblemFile', 'problem_file_id');
    }
}
