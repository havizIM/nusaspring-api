<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $table = 'contacts';

    protected $appends = [
        'sum_selling', 'sum_selling_ppn', 'sum_selling_payment', 'sum_selling_return', 'sum_selling_return_ppn', 'sum_selling_discount', 'sum_selling_return_discount',
        'sum_purchase', 'sum_purchase_ppn', 'sum_purchase_payment', 'sum_purchase_return', 'sum_purchase_return_ppn', 'sum_purchase_discount', 'sum_purchase_return_discount',
    ];

    protected $fillable = [
        'contact_name', 'type', 'pic', 'phone', 'fax', 'handphone', 'email', 'address', 'npwp', 'memo'
    ];

    protected $hidden = [
        'created_by', 'updated_by', 'deleted_by'
    ];

    public function sellings()
    {
        return $this->hasMany('App\Selling');
    }

    public function selling_products()
    {
        return $this->hasManyThrough('App\SellingProduct', 'App\Selling');
    }

    public function getSumSellingDiscountAttribute()
    {
        return $this->selling_products()->sum('selling_products.discount_amount');
    }

    public function getSumSellingAttribute()
    {
        return $this->selling_products()->sum('selling_products.total');
    }

    public function getSumSellingPpnAttribute()
    {
        return $this->sellings()->sum('total_ppn');
    }


    public function selling_payments()
    {
        return $this->hasMany('App\SellingPayment');
    }

    public function getSumSellingPaymentAttribute()
    {
        return $this->selling_payments()->sum('amount');
    }



    public function selling_returns()
    {
        return $this->hasMany('App\SellingReturn');
    }

    public function selling_return_products()
    {
        return $this->hasManyThrough('App\SellingReturnProduct', 'App\SellingReturn');
    }

    public function getSumSellingReturnDiscountAttribute()
    {
        return $this->selling_return_products()->sum('selling_return_products.discount_amount');
    }

    public function getSumSellingReturnAttribute()
    {
        return $this->selling_return_products()->sum('selling_return_products.total');
    }

    public function getSumSellingReturnPpnAttribute()
    {
        return $this->selling_returns()->sum('total_ppn');
    }



    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }

    public function purchase_products()
    {
        return $this->hasManyThrough('App\PurchaseProduct', 'App\Purchase');
    }

    public function getSumPurchaseDiscountAttribute()
    {
        return $this->purchase_products()->sum('purchase_products.discount_amount');
    }

    public function getSumPurchaseAttribute()
    {
        return $this->purchase_products()->sum('purchase_products.total');
    }

    public function getSumPurchasePpnAttribute()
    {
        return $this->purchases()->sum('total_ppn');
    }



    public function purchase_payments()
    {
        return $this->hasMany('App\PurchasePayment');
    }

    public function getSumPurchasePaymentAttribute()
    {
        return $this->purchase_payments()->sum('amount');
    }



    public function purchase_returns()
    {
        return $this->hasMany('App\PurchaseReturn');
    }

    public function purchase_return_products()
    {
        return $this->hasManyThrough('App\PurchaseReturnProduct', 'App\PurchaseReturn');
    }

    public function getSumPurchaseReturnDiscountAttribute()
    {
        return $this->purchase_return_products()->sum('purchase_return_products.discount_amount');
    }

    public function getSumPurchaseReturnAttribute()
    {
        return $this->purchase_return_products()->sum('purchase_return_products.total');
    }

    public function getSumPurchaseReturnPpnAttribute()
    {
        return $this->purchase_returns()->sum('total_ppn');
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
