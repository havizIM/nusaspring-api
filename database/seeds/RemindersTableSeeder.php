<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemindersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reminders')->insert([
            [
                'user_id' => 2,
                'description' => 'Reminder 1',
                'start_date' => '2020-04-25 00:00:00',
                'end_date' => '2020-04-27 00:00:00',
                'color' => 'info',
            ],
            [
                'user_id' => 2,
                'description' => 'Reminder 2',
                'start_date' => '2020-04-16 00:00:00',
                'end_date' => '2020-04-20 00:00:00',
                'color' => 'danger',
            ],
        ]);
    }
}
