<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherCourse extends Model
{
    protected $table = 'teacher_course';
    public $timestamps = true;

    protected $fillable = [
        'teacher_id',
        'course_id',
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
