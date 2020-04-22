<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Helpdesk',
                'username' => 'helpdesk',
                'email' => 'viz.ndinq@gmail.com',
                'password' => bcrypt('h3lpd35k'),
                'roles' => 'HELPDESK',
                'active' => 'Y',
            ],
            [
                'name' => 'Nurul',
                'username' => 'nurul',
                'email' => 'admin1.nusaspring@gmail.com',
                'password' => bcrypt('admin'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ],
            [
                'name' => 'Azhar',
                'username' => 'azhar',
                'email' => 'admin2.nusaspring@gmail.com',
                'password' => bcrypt('admin'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ]
        ]);
    }
}
