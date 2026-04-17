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
        $users  = User::all();
        $monday = now()->startOfWeek(1); // következő hét hétfőtől

        foreach ($users as $user) {
            for ($i = 0; $i < 5; $i++) {
                Schedule::create([
                    'user_id' => $user->id,
                    'date'    => $monday->copy()->addDays($i)->toDateString(),
                    'start'   => '08:00',
                    'end'     => '17:00',
                ]);
            }
        }
    }
}
