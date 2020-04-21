<?php

use Illuminate\Database\Seeder;

class AdjustmentProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('adjustment_products')->insert([
            [
                'adjustment_id' => 1,
                'product_id' => 1,
                'description' => 'Sarung Atlas',
                'qty' => 100,
                'unit' => 'Pcs',
                'unit_price' => 25000,
                'total' => 2500000,
            ],
            [
                'adjustment_id' => 1,
                'product_id' => 2,
                'description' => 'Sarung Gajah Duduk',
                'qty' => 100,
                'unit' => 'Pcs',
                'unit_price' => 20000,
                'total' => 2000000,
            ],
            [
                'adjustment_id' => 1,
                'product_id' => 4,
                'description' => 'Mukena Hidayah',
                'qty' => 100,
                'unit' => 'Set',
                'unit_price' => 50000,
                'total' => 5000000,
            ],
            [
                'adjustment_id' => 1,
                'product_id' => 5,
                'description' => 'Kerudung Sans',
                'qty' => 100,
                'unit' => 'Lusin',
                'unit_price' => 120000,
                'total' => 12000000,
            ],
            [
                'adjustment_id' => 1,
                'product_id' => 3,
                'description' => 'Peci Supreme',
                'qty' => 100,
                'unit' => 'Pcs',
                'unit_price' => 120000,
                'total' => 12000000,
            ],
        ]);

        
    }
}
