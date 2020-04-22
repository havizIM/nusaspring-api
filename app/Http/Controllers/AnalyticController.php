<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Selling;
use App\Purchase;

use App\SellingProduct;
use App\PurchaseProduct;

use App\Log;
use App\Product;

class AnalyticController extends Controller
{
    
    public function bussiness($year)
    {
        if($year === ''){
            $year = date('Y');
        }

        $sellings = Selling::select(
            DB::raw('SUM(selling_products.total) AS totalKey'),
            DB::raw('SUM(sellings.total_ppn) AS ppnKey'),
            DB::raw('SUM(selling_products.total) AS totalDiscountKey'),
            DB::raw('MONTH(sellings.date) AS monthKey'),
        )
        ->join('selling_products', 'sellings.id' , '=', 'selling_products.selling_id')
        ->whereYear('sellings.date', '=', $year)
        ->groupBy('monthKey')
        ->get();

        $sellings->makeHidden([
            'grand_total', 
            'total_qty', 
            'total_discount', 
            'total_payment', 
            'total_return', 
            'total_qty_return', 
            'total_ppn_return',
            'total_return_discount'
        ])->toArray();
        $month_selling_array = ['Jan', 'Feb', 'Mar','Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $total_selling_array = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach($sellings as $key){
            $index = $key->monthKey - 1;
            $total_selling_array[$index] = abs($key->totalKey + $key->ppnKey + $key->totalDiscountKey);
        }

        $purchases = Purchase::select(
            DB::raw('SUM(purchase_products.total) AS totalKey'),
            DB::raw('SUM(purchases.total_ppn) AS ppnKey'),
            DB::raw('SUM(purchase_products.discount_amount) AS totalDiscountKey'),
            DB::raw('MONTH(purchases.date) AS monthKey'),
        )
        ->join('purchase_products', 'purchases.id' , '=', 'purchase_products.purchase_id')
        ->whereYear('purchases.date', '=', $year)
        ->groupBy('monthKey')
        ->get();

        $purchases->makeHidden([
            'grand_total', 
            'total_qty',
            'total_discount',
            'total_payment', 
            'total_return', 
            'total_qty_return', 
            'total_ppn_return',
            'total_return_discount'
        ])->toArray();
        $month_purchase_array = ['Jan', 'Feb', 'Mar','Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $total_purchase_array = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        foreach($purchases as $key){
            $index = $key->monthKey - 1;
            $total_purchase_array[$index] = abs($key->totalKey + $key->ppnKey + $key->totalDiscountKey);
        }

        $results['year'] = $year;
        $results['purchase']['month'] = $month_selling_array;
        $results['purchase']['total'] = $total_selling_array;

        $results['selling']['month'] = $month_purchase_array;
        $results['selling']['total'] = $total_purchase_array;

        return response()->json([
            'status' => true,
            'message' => 'Success fetch sellings',
            'results' => $results
        ]);
    }

    public function top_ten($order_by)
    {
        if($order_by == ''){
            $order_by = 'qtyKey';
        }

        $products = PurchaseProduct::select(
            'product_id',
            'products.product_name',
            DB::raw('SUM(purchase_products.qty) AS qtyKey'),
            DB::raw('SUM(purchase_products.total) AS totalKey'),
        )
        ->join('products', 'products.id' , '=', 'purchase_products.product_id')
        ->groupBy('purchase_products.product_id')
        ->orderBy($order_by, 'desc')
        ->where('products.product_name', '!=', 'Penjualan / Pembelian')
        ->limit(10)
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success fetch top 10',
            'results' => $products
        ]);
    }
}
