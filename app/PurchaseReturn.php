<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseReturn extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_returns';

    protected $appends = ['grand_total', 'total_discount', 'total_qty'];

    protected $fillable = [
        'purchase_id', 'return_number', 'reference_number', 'message', 'memo', 'attachment', 'discount_percent', 'discount_amount', 'date'
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

    public function contact()
    {
        return $this->belongsTo('App\Contact');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Purchase');
    }

    public function products()
    {
        return $this->hasMany('App\PurchaseReturnProduct');
    }

    public function getTotalQtyAttribute()
    {
        return $this->products()->sum('qty');
    }

    public function getTotalDiscountAttribute()
    {
        return $this->products()->sum('discount_amount');
    }

    public function getGrandTotalAttribute()
    {
        return $this->products()->sum('total');
    }

    public function created_by_user()
    {
        return $this->belongsTo('App\User', 'created_by', 'id')->select(['id', 'name']);
    }

    public function updated_by_user()
    {
        return $this->belongsTo('App\User', 'updated_by', 'id')->select(['id', 'name']);
    }

    public function deleted_by_user()
    {
        return $this->belongsTo('App\User', 'deleted_by', 'id')->select(['id', 'name']);
    }
}
