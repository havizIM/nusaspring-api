<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{

    protected $table = 'logs';

    protected $fillable = [
        'description', 'referece_id', 'url'
    ];

    protected $hidden = [
        'user_id'
    ];
}
