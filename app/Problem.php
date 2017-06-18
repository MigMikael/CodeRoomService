<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    protected $table = 'problem';
    public $timestamps = true;

    protected $fillable = [
        'lesson_id',
        'name',
        'description',
        'evaluator',
        'order',
        'timelimit',
        'memorylimit',
        'is_parse',
        'question'
    ];

    public function lesson()
    {
        return $this->belongsTo('App\Lesson');
    }

    public function submissions()
    {
        return $this->hasMany('App\Submission');
    }

    public function problemFiles()
    {
        return $this->hasMany('App\ProblemFile');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
