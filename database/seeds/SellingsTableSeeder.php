<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SellingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sellings')->insert([
            [
                'contact_id' => 3,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Selatan',
                'selling_number' => 'SL-00001',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => -90000,
            ],
            [
                'contact_id' => 3,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Selatan',
                'selling_number' => 'SL-00002',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => -340000,
            ],
            [
                'contact_id' => 4,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Pusat',
                'selling_number' => 'SL-00003',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => -280000,
            ],
            [
                'contact_id' => 4,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Pusat',
                'selling_number' => 'SL-00004',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => -90000,
            ],
        ]);
    }
}
