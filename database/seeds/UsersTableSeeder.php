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
                'name' => 'Nurul Hasanah',
                'username' => 'nurul',
                'email' => 'nurulhasanah1796@gmail.com',
                'password' => bcrypt('nurul'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ],
            [
                'name' => 'Azhar Ghaliyyah',
                'username' => 'azhar',
                'email' => 'liaazhar2@gmail.com',
                'password' => bcrypt('azhar'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ],
            [
                'name' => 'Hamzah',
                'username' => 'hamzah',
                'email' => 'nurulhasanah1796@gmail.com',
                'password' => bcrypt('hamzah'),
                'roles' => 'ADMIN',
                'active' => 'Y',
            ]
        ]);
    }
}
