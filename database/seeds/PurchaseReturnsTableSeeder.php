<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReturnsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_returns')->insert([
            [
                'contact_id' => 1,
                'purchase_id' => 2,
                'return_number' => 'PR-00001',
                'date' => '2020-04-16',
                'total_ppn' => -85000,
            ],
            [
                'contact_id' => 2,
                'purchase_id' => 3,
                'return_number' => 'PR-00002',
                'date' => '2020-04-16',
                'total_ppn' => -70000,
            ],
        ]);
    }
}
