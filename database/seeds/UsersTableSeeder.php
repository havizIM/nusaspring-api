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
                'phone' => '08987748441',
                'address' => '-',
                'roles' => 'HELPDESK',
                'active' => 'Y',
            ],
            [
                'name' => 'Nurul Hasanah',
                'username' => 'nurul',
                'email' => 'nurulhasanah1796@gmail.com',
                'password' => bcrypt('nurul'),
                'phone' => '08971785087',
                'address' => 'Jl. Radar Auri No. 2 Cisalak PS, Kec. Cimaggis Kota Depok Jawa Barat 16452',
                'roles' => 'ADMIN',
                'active' => 'Y',
            ],
            [
                'name' => 'Azhar Ghaliyyah',
                'username' => 'azhar',
                'email' => 'liaazhar2@gmail.com',
                'password' => bcrypt('azhar'),
                'phone' => '082128723208',
                'address' => 'Jl. Radar Auri No. 2 Cisalak PS, Kec. Cimaggis Kota Depok Jawa Barat 16452',
                'roles' => 'ADMIN',
                'active' => 'Y',
            ],
            [
                'name' => 'Hamzah',
                'username' => 'hamzah',
                'email' => 'nurulhasanah1796@gmail.com',
                'password' => bcrypt('hamzah'),
                'phone' => '081280999733',
                'address' => 'Jl. Radar Auri No. 2 Cisalak PS, Kec. Cimaggis Kota Depok Jawa Barat 16452',
                'roles' => 'ADMIN',
                'active' => 'Y',
            ]
        ]);
    }
}
