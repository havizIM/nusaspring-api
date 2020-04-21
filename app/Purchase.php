<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $table = 'purchases';

    protected $appends = ['grand_total', 'total_discount', 'total_qty', 'total_payment', 'total_return', 'total_qty_return', 'total_ppn_return', 'total_return_discount'];

    protected $fillable = [
        'contact_id', 'email', 'address', 'purchase_number', 'reference_number', 'message', 'memo', 'attachment', 'discount_percent', 'discount_amount', 'date', 'due_date'
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
        return $this->belongsTo('App\Contact')->where('type', '=', 'Supplier');
    }

    public function products()
    {
        return $this->hasMany('App\PurchaseProduct');
    }

    public function payments()
    {
        return $this->hasMany('App\PurchasePayment');
    }

    public function returns()
    {
        return $this->hasMany('App\PurchaseReturn');
    }

    public function return_products()
    {
        return $this->hasManyThrough('App\PurchaseReturnProduct', 'App\PurchaseReturn');
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

    public function getTotalPaymentAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getTotalReturnDiscountAttribute()
    {
        return $this->return_products()->sum('discount_amount');
    }

    public function getTotalPpnReturnAttribute()
    {
        return $this->returns()->sum('total_ppn');
    }

    public function getTotalReturnAttribute()
    {
        return $this->return_products()->sum('total');
    }

    public function getTotalQtyReturnAttribute()
    {
        return $this->return_products()->sum('qty');
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
