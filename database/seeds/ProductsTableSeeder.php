<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert([
            [
                'product_name' => 'Sarung Atlas',
                'sku' => 'ATLAS-001',
                'category_id' => 1,
                'unit_id' => 1,
                'purchase_price' => 25000,
                'selling_price' => 50000,
            ],
            [
                'product_name' => 'Sarung Gajah Duduk',
                'sku' => 'GD-001',
                'category_id' => 1,
                'unit_id' => 1,
                'purchase_price' => 20000,
                'selling_price' => 40000,
            ],
            [
                'product_name' => 'Peci Supreme',
                'sku' => 'SUPREME-001',
                'category_id' => 2,
                'unit_id' => 2,
                'purchase_price' => 120000,
                'selling_price' => 240000,
            ],
            [
                'product_name' => 'Mukena Hidayah',
                'sku' => 'HDY-001',
                'category_id' => 3,
                'unit_id' => 3,
                'purchase_price' => 50000,
                'selling_price' => 100000,
            ],
            [
                'product_name' => 'Kerudung Sans',
                'sku' => 'SANS-001',
                'category_id' => 4,
                'unit_id' => 2,
                'purchase_price' => 120000,
                'selling_price' => 240000,
            ],
        ]);
    }
}
