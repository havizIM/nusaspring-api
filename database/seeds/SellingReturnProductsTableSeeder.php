<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellingReturnProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('selling_return_products')->insert([
            [
                'selling_return_id' => 1,
                'product_id' => 4,
                'description' => 'Mukena Hidayah',
                'qty' => 5,
                'unit' => 'Set',
                'unit_price' => 100000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 500000,
            ],
            [
                'selling_return_id' => 1,
                'product_id' => 5,
                'description' => 'Kerudung Sans',
                'qty' => 5,
                'unit' => 'Lusin',
                'unit_price' => 240000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 1200000,
            ],
            [
                'selling_return_id' => 2,
                'product_id' => 3,
                'description' => 'Peci Supreme',
                'qty' => 5,
                'unit' => 'Pcs',
                'unit_price' => 240000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 1200000,
            ],
            [
                'selling_return_id' => 2,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => 5,
                'unit' => 'Pcs',
                'unit_price' => 40000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => 200000,
            ],
        ]);
    }
}
