<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnProduct extends Model
{
    public $timestamps = false;

    protected $table = 'purchase_return_products';

    protected $fillable = [
        'product_id', 'description', 'qty', 'unit', 'unit_price', 'ppn', 'ppn_amount', 'total'
    ];

    protected $hidden = [
        'purchase_return_id'
    ];
    
    public function purchase_return()
    {
        return $this->belongsTo('App\PurchaseReturn');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
