<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseProduct extends Model
{

    public $timestamps = false;

    protected $table = 'purchase_products';

    protected $fillable = [
        'product_id', 'description', 'qty', 'unit', 'unit_price', 'ppn', 'ppn_amount', 'total'
    ];

    protected $hidden = [
        'purchase_id'
    ];
    
    public function purchase()
    {
        return $this->belongsTo('App\Purchase');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
