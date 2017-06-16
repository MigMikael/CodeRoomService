<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'announcement';
    public $timestamps = true;

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'priority'
    ];

    public function course(){
        return $this->belongsTo('App\Course');
    }
}
