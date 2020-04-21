<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdjustmentProduct extends Model
{

    public $timestamps = false;
    
    protected $table = 'adjustment_products';

    protected $fillable = [
        'product_id', 'description', 'qty', 'unit', 'unit_price', 'total'
    ];

    protected $hidden = [
        'adjustment_id'
    ];

    public function adjustment()
    {
        return $this->belongsTo('App\Adjustment');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

}
