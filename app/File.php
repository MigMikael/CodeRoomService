<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    public $timestamps = true;
    protected $table = 'file';
    protected $fillable = [
        'name', 'mime', 'original_name'
    ];
}
