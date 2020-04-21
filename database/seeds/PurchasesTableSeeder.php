<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchases')->insert([
            [
                'contact_id' => 1,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Barat',
                'purchase_number' => 'PO-00001',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => 45000,
            ],
            [
                'contact_id' => 1,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Barat',
                'purchase_number' => 'PO-00002',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => 170000,
            ],
            [
                'contact_id' => 2,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Timur',
                'purchase_number' => 'PO-00003',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => 140000,
            ],
            [
                'contact_id' => 2,
                'email' => 'viz.ndinq@gmail.com',
                'address' => 'Jakarta Timur',
                'purchase_number' => 'PO-00004',
                'date' => '2020-04-16',
                'due_date' => '2020-05-16',
                'total_ppn' => 45000,
            ],
        ]);
    }
}
