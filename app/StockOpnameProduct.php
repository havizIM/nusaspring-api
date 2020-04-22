<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockOpnameProduct extends Model
{
    public $timestamps = false;
    
    protected $table = 'stock_opname_products';

    protected $fillable = [
        'product_id', 'description', 'system_qty', 'unit', 'unit_price', 'actual_qty', 'note'
    ];

    public function stock_opname()
    {
        return $this->belongsTo('App\StockOpname');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
