<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentBadge extends Model
{
    protected $table = 'student_badge';
    public $timestamps = true;
    protected $fillable = [
        'id',
        'student_id',
        'badge_id',
    ];
}
