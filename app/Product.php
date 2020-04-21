<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $appends = [
        'sum_purchase', 'sum_selling', 'sum_purchase_return', 'sum_selling_return', 'sum_adjustment',
        'sum_qty_awal', 'sum_transfer_in', 'sum_transfer_out', 'sum_other'
    ];

    protected $fillable = [
        'product_name', 'sku', 'purchase_price', 'selling_price', 'picture'
    ];

    protected $hidden = [
        'created_by', 'updated_by', 'deleted_by', 'category_id', 'unit_id'
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

    

    public function unit()
    {
        return $this->belongsTo('App\Unit')->select(['id', 'unit_name']);
    }

    public function category()
    {
        return $this->belongsTo('App\Category')->select(['id', 'category_name']);;
    }


    public function purchases()
    {
        return $this->belongsToMany('App\Purchase', 'purchase_products')->select(['id', 'purchase_number', 'reference_number', 'date'])->withPivot('unit_price','qty', 'total');
    }

    public function getSumPurchaseAttribute()
    {
        return $this->purchases()->sum('purchase_products.qty');
    }


    public function sellings()
    {
        return $this->belongsToMany('App\Selling', 'selling_products')->select(['id', 'selling_number', 'reference_number', 'date'])->withPivot('unit_price','qty', 'total');
    }

    public function getSumSellingAttribute()
    {
        return $this->sellings()->sum('selling_products.qty');
    }


    public function purchase_returns()
    {
        return $this->belongsToMany('App\PurchaseReturn', 'purchase_return_products')->select(['id', 'return_number', 'reference_number', 'date'])->withPivot('unit_price','qty', 'total');
    }

    public function getSumPurchaseReturnAttribute()
    {
        return $this->purchase_returns()->sum('purchase_return_products.qty');
    }


    public function selling_returns()
    {
        return $this->belongsToMany('App\SellingReturn', 'selling_return_products')->select(['id', 'return_number', 'reference_number', 'date'])->withPivot('unit_price', 'qty', 'total');
    }

    public function getSumSellingReturnAttribute()
    {
        return $this->selling_returns()->sum('selling_return_products.qty');
    }


    public function adjustments()
    {
        return $this->belongsToMany('App\Adjustment', 'adjustment_products')->select(['id', 'category', 'reference_number', 'date'])->withPivot('unit_price', 'qty', 'total');
    }

    public function getSumAdjustmentAttribute()
    {
        return $this->adjustments()->sum('adjustment_products.qty');
    }

    public function getSumQtyAwalAttribute()
    {
        return $this->adjustments()->where('category', 'Qty Awal')->sum('adjustment_products.qty');
    }

    public function getSumTransferInAttribute()
    {
        return $this->adjustments()->where('category', 'Transfer In')->sum('adjustment_products.qty');
    }

    public function getSumTransferOutAttribute()
    {
        return $this->adjustments()->where('category', 'Transfer Out')->sum('adjustment_products.qty');
    }

    public function getSumOtherAttribute()
    {
        return $this->adjustments()->where('category', 'Other')->sum('adjustment_products.qty');
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
