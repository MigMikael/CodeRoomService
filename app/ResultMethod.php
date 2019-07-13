<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultMethod extends Model
{
    public $timestamps = false;
    protected $table = 'result_method';

    protected $fillable = [
        'result_id',
        'access_modifier',
        'non_access_modifier',
        'return_type',
        'name',
        'parameter',
        'recursive',
        'loop',
        'score',
    ];

    public function result()
    {
        return $this->belongsTo('App\Result', 'result_id');
    }
}
