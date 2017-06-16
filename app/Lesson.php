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
        'order'
    ];

    public function course()
    {
        return $this->belongsTo('App\Course');
    }

    public function lessons()
    {
        return $this->hasMany('App\Lesson');
    }

    public function problems()
    {
        return $this->hasMany('App\Problem');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
