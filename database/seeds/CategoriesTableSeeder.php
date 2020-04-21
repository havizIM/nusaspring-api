<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->insert([
            [
                'category_name' => 'Sarung',
            ],
            [
                'category_name' => 'Peci',
            ],
            [
                'category_name' => 'Mukena',
            ],
            [
                'category_name' => 'Kerudung',
            ],
        ]);
    }
}
