<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            [
                'unit_name' => 'Pcs',
            ],
            [
                'unit_name' => 'Lusin',
            ],
            [
                'unit_name' => 'Set',
            ],
        ]);
    }
}
