<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'course';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'image',
        'token',
        'color',
        'status',
        'mode'
    ];

    protected $hidden = [
        'token',
        'created_at',
        'updated_at'
    ];

    public function badges()
    {
        return $this->hasMany('App\Badge');
    }

    public function announcements()
    {
        return $this->hasMany('App\Announcement');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function teachers()
    {
        return $this->belongsToMany('App\Teacher', 'teacher_course', 'course_id', 'teacher_id')
            ->withPivot('status');
    }

    public function students()
    {
        return $this->belongsToMany('App\Student', 'student_course', 'course_id', 'student_id')
            ->withPivot('status', 'progress');
    }

    public function scopeEnable($query)
    {
        return $query->where('status', '=', 'enable');
    }

    public function scopeDisable($query)
    {
        return $query->where('status', '=', 'disable');
    }
}
