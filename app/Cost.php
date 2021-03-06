<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cost extends Model
{
    use SoftDeletes;

    protected $table = 'costs';
    
    protected $appends = ['grand_total', 'total_discount'];

    protected $fillable = [
        'cost_number', 'date', 'message', 'memo', 'attachment'
    ];

    protected $hidden = [
        'created_by', 'updated_by', 'deleted_by'
    ];

    public static function boot() {
        parent::boot();

        // create a event to happen on updating
        static::updating(function($table)  {
            $table->updated_by = auth()->user()->id;
        });

        // create a event to happen on deleting
        static::deleting(function($table) {
            $table->deleted_by = auth()->user()->id;
            $table->save();
        });

        // create a event to happen on saving
        static::saving(function($table)  {
            $table->created_by = auth()->user()->id;
            $table->updated_by = auth()->user()->id;
        });

        static::restored(function($table)
    	{
    		$table->deleted_by = NULL;
            $table->save();
    	});
    }

    public function details()
    {
        return $this->hasMany('App\CostDetail');
    }

    public function getGrandTotalAttribute()
    {
        return $this->details()->sum('amount');
    }

    public function getTotalDiscountAttribute()
    {
        return $this->details()->sum('discount_amount');
    }
}
