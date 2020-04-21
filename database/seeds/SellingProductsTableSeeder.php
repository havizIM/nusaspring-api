<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellingProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('selling_products')->insert([
            [
                'selling_id' => 1,
                'product_id' => 1,
                'description' => 'Sarung Atlas',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 50000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -500000,
            ],
            [
                'selling_id' => 1,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 40000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -400000,
            ],
            [
                'selling_id' => 2,
                'product_id' => 4,
                'description' => 'Mukena Hidayah',
                'qty' => -10,
                'unit' => 'Set',
                'unit_price' => 100000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -1000000,
            ],
            [
                'selling_id' => 2,
                'product_id' => 5,
                'description' => 'Kerudung Sans',
                'qty' => -10,
                'unit' => 'Lusin',
                'unit_price' => 240000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -2400000,
            ],
            [
                'selling_id' => 3,
                'product_id' => 3,
                'description' => 'Peci Supreme',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 240000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -2400000,
            ],
            [
                'selling_id' => 3,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 40000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -400000,
            ],
            [
                'selling_id' => 4,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 40000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -400000,
            ],
            [
                'selling_id' => 4,
                'product_id' => 1,
                'description' => 'Sarung Atlas',
                'qty' => -10,
                'unit' => 'Pcs',
                'unit_price' => 50000,
                'ppn' => 'Y',
                'discount_percent' => 0,
                'discount_amount' => 0,
                'total' => -500000,
            ],
        ]);
    }
}
