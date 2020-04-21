<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjustment extends Model
{
    use SoftDeletes;

    protected $table = 'adjustments';

    protected $appends = ['grand_total', 'total_qty'];

    protected $fillable = [
        'categories', 'reference_number', 'date', 'memo', 'attachment'
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
        return $this->hasMany('App\AdjustmentProduct');
    }

    public function getTotalQtyAttribute()
    {
        return $this->products()->sum('qty');
    }

    public function getGrandTotalAttribute()
    {
        return $this->products()->sum('total');
    }

    public function created_by()
    {
        return $this->hasOne('App\User', 'id', 'created_by')->withTrashed()->select(['id', 'name']);
    }

    public function updated_by()
    {
        return $this->hasOne('App\User', 'id', 'updated_by')->withTrashed()->select(['id', 'name']);
    }

    public function deleted_by()
    {
        return $this->hasOne('App\User', 'id', 'deleted_by')->withTrashed()->select(['id', 'name']);
    }
}
