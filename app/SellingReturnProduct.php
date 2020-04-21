<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellingReturnProduct extends Model
{
    public $timestamps = false;

    protected $table = 'selling_return_products';

    protected $fillable = [
        'product_id', 'description', 'qty', 'unit', 'unit_price', 'ppn', 'ppn_amount', 'total'
    ];

    protected $hidden = [
        'selling_return_id'
    ];
    
    public function selling_return()
    {
        return $this->belongsTo('App\SellingReturn');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
