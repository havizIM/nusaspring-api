<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellingProduct extends Model
{

    public $timestamps = false;
    
    protected $table = 'selling_products';

    protected $fillable = [
        'product_id', 'description', 'qty', 'unit', 'unit_price', 'ppn', 'ppn_amount', 'total'
    ];

    protected $hidden = [
        'selling_id'
    ];

    public function selling()
    {
        return $this->belongsTo('App\Selling');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
