<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_products')->insert([
            [
                'purchase_id' => 1,
                'product_id' => 1,
                'description' => 'Sarung Atlas',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 25000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 250000,
            ],
            [
                'purchase_id' => 1,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 20000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 200000,
            ],
            [
                'purchase_id' => 2,
                'product_id' => 4,
                'description' => 'Mukena Hidayah',
                'qty' => 10,
                'unit' => 'Set',
                'unit_price' => 50000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 500000,
            ],
            [
                'purchase_id' => 2,
                'product_id' => 5,
                'description' => 'Kerudung Sans',
                'qty' => 10,
                'unit' => 'Lusin',
                'unit_price' => 120000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 1200000,
            ],
            [
                'purchase_id' => 3,
                'product_id' => 3,
                'description' => 'Peci Supreme',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 120000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 1200000,
            ],
            [
                'purchase_id' => 3,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 20000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 200000,
            ],
            [
                'purchase_id' => 4,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 20000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 200000,
            ],
            [
                'purchase_id' => 4,
                'product_id' => 1,
                'description' => 'Sarung Atlas',
                'qty' => 10,
                'unit' => 'Pcs',
                'unit_price' => 25000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 250000,
            ],
        ]);
    }
}
