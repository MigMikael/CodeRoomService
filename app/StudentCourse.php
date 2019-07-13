<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentCourse extends Model
{
    protected $table = 'student_course';
    public $timestamps = true;

    protected $fillable = [
        'student_id',
        'course_id',
        'progress',
        'status',
    ];

    public function scopeEnable($query)
    {
        return $query->where('status', '=', 'enable');
    }

    public function scopeDisable($query)
    {
        return $query->where('status', '=', 'disable');
    }
}
