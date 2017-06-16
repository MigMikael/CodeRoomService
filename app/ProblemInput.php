<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProblemInput extends Model
{
    public $timestamps = false;
    protected $table = 'problem_input';

    protected $fillable = [
        'problem_file_id',
        'version',
        'filename',
        'content',
    ];

    public function problemFile()
    {
        return $this->belongsTo('App\ProblemFile', 'problem_file_id');
    }
}
