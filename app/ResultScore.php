<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultScore extends Model
{
    protected $table = 'result_score';
    public $timestamps = false;

    protected $fillable = [
        'result_id',
        'class',
        'package',
        'enclose',
        'extends',
        'implements',
    ];

    public function result()
    {
        return $this->belongsTo('App\Result', 'result_id');
    }
}
