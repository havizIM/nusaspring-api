<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellingReturnsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('selling_returns')->insert([
            [
                'selling_id' => 2,
                'return_number' => 'SR-00001',
                'date' => '2020-04-16',
                'total_ppn' => 170000,
            ],
            [
                'selling_id' => 3,
                'return_number' => 'SR-00002',
                'date' => '2020-04-16',
                'total_ppn' => 140000,
            ],
        ]);
    }
}
