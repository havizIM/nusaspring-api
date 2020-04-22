<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostDetail extends Model
{
    public $timestamps = false;
    
    protected $table = 'cost_details';

    protected $fillable = [
        'description', 'amount', 'attachment'
    ];

    protected $hidden = [
        'cost_id'
    ];

    public function cost()
    {
        return $this->belongsTo('App\Cost');
    }
}
