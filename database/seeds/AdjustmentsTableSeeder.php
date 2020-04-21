<?php

use Illuminate\Database\Seeder;

class AdjustmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('adjustments')->insert([
            [
                'category' => 'Qty Awal',
                'reference_number' => 'QTA-00001',
                'date' => '2020-04-16',
            ],
        ]);
    }
}
