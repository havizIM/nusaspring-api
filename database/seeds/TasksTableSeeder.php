<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->insert([
            [
                'user_id' => 2,
                'title' => 'Coba Task',
                'description' => 'Mantap Betul',
                'color' => 'info',
            ],
            [
                'user_id' => 2,
                'title' => 'Coba Task 2',
                'description' => 'Mantap Betul 2',
                'color' => 'danger',
            ],
        ]);
    }
}
