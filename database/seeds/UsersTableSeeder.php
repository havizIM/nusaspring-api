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
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin.nusaspring@gmail.com',
                'password' => bcrypt('admin'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ]
        ]);
    }
}
