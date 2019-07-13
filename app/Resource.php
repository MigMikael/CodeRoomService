<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resource';
    public $timestamps = false;

    protected $fillable = [
        'problem_id',
        'file_id',
        'visible'
    ];

    public function scopeShow($query)
    {
        return $query->where('visible', '=', 'true');
    }

    public function scopeHide($query)
    {
        return $query->where('visible', '=', 'false');
    }
}
