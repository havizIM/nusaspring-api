<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReturnProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_return_products')->insert([
            [
                'purchase_return_id' => 1,
                'product_id' => 4,
                'description' => 'Mukena Hidayah',
                'qty' => -5,
                'unit' => 'Set',
                'unit_price' => 50000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -250000,
            ],
            [
                'purchase_return_id' => 1,
                'product_id' => 5,
                'description' => 'Kerudung Sans',
                'qty' => -5,
                'unit' => 'Lusin',
                'unit_price' => 120000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -600000,
            ],
            [
                'purchase_return_id' => 2,
                'product_id' => 3,
                'description' => 'Peci Supreme',
                'qty' => -5,
                'unit' => 'Pcs',
                'unit_price' => 120000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -600000,
            ],
            [
                'purchase_return_id' => 2,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => -5,
                'unit' => 'Pcs',
                'unit_price' => 20000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -100000,
            ],
        ]);
    }
}
