<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultConstructor extends Model
{
    public $timestamps = false;
    protected $table = 'result_constructor';

    protected $fillable = [
        'result_id',
        'access_modifier',
        'name',
        'parameter',
        'score',
    ];

    public function result()
    {
        return $this->belongsTo('App\Result', 'result_id');
    }
}
