<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lesson';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'course_id',
        'status',
        'order',
        'open_submit',
        'guide',
        'mode'
    ];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function students()
    {
        return $this->belongsToMany('App\Student', 'student_lesson', 'lesson_id', 'student_id')
            ->withPivot('progress');
    }

    public function problems()
    {
        return $this->hasMany('App\Problem');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeNormal($query)
    {
        return $query->where('status', '=', 'normal');
    }

    public function scopeTest($query)
    {
        return $query->where('status', '=', 'test');
    }
}
