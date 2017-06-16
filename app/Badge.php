<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    protected $table = 'badge';
    public $timestamps = true;
    protected $fillable = [
        'name',
        'description',
        'image',
        'course_id',
        'type',
        'criteria',
    ];

    public function students()
    {
        return $this->belongsToMany('App\Student', 'student_badge', 'badge_id', 'student_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Course');
    }
}
