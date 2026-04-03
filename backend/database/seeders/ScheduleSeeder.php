<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $users = User::all();

        foreach ($users as $user) {

            // Monday (1) to Friday (5)
            for ($day = 1; $day <= 5; $day++) {
                Schedule::create([
                    'user_id' => $user->id,
                    'day'     => $day,
                    'start'   => '08:00:00',
                    'end'     => '17:00:00',
                ]);
            }


        }
    }
}
