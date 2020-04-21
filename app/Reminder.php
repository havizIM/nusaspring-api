<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use SoftDeletes;

    protected $table = 'reminders';

    protected $fillable = [
        'description', 'datetime', 'color'
    ];

    protected $hidden = [
        'user_id'
    ];
}
