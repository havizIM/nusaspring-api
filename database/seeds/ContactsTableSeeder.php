<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contacts')->insert([
            [
                'contact_name' => 'PT. Maju Mundur Abadi',
                'type' => 'Supplier',
                'pic' => 'Dian Ratna Sari',
                'phone' => '0216301019',
                'handphone' => '08987748441',
                'address' => 'Jakarta Barat',
            ],
            [
                'contact_name' => 'PT. CodeManiac ID',
                'type' => 'Supplier',
                'pic' => 'Haviz Indra Maulana',
                'phone' => '0216301019',
                'handphone' => '08987748441',
                'address' => 'Jakarta Timur',
            ],
            [
                'contact_name' => 'PT. Sarana Indah Pratama',
                'type' => 'Customer',
                'pic' => 'Devan Dirgantara Putra',
                'phone' => '0216301019',
                'handphone' => '08987748441',
                'address' => 'Jakarta Selatan',
            ],
            [
                'contact_name' => 'PT. Makmur Sentosa',
                'type' => 'Customer',
                'pic' => 'Kalyssa Innara Putri',
                'phone' => '0216301019',
                'handphone' => '08987748441',
                'address' => 'Jakarta Pusat',
            ],
        ]);
    }
}
