<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockOpname extends Model
{
    use SoftDeletes;

    protected $table = 'stock_opnames';

    protected $appends = ['total_system_amount', 'total_actual_amount', 'total_system_qty', 'total_actual_qty'];

    protected $fillable = [
        'so_number', 'status', 'date', 'memo', 'message', 'attachment'
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

    public function products()
    {
        return $this->hasMany('App\StockOpnameProduct');
    }

    public function getTotalSystemAmountAttribute()
    {
        return $this->products()->sum('system_total');
    }

    public function getTotalActualAmountAttribute()
    {
        return $this->products()->sum('actual_total');
    }

    public function getTotalSystemQtyAttribute()
    {
        return $this->products()->sum('system_qty');
    }

    public function getTotalActualQtyAttribute()
    {
        return $this->products()->sum('actual_qty');
    }
}
