<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchasePaymentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('purchase_payments')->insert([
            [
                'purchase_id' => 1,
                'payment_number' => 'RV-00001',
                'type' => 'Cash',
                'description' => 'Pembayaran 1',
                'date' => '2020-04-16',
                'amount' => -100000,
            ],
            [
                'purchase_id' => 2,
                'payment_number' => 'RV-00002',
                'type' => 'Cash',
                'description' => 'Pembayaran 1',
                'date' => '2020-04-16',
                'amount' => -100000,
            ],
        ]);
    }
}
