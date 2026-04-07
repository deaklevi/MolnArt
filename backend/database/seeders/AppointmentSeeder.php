<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppointmentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $janos = User::where('user_name', 'admin_janos')->first();

        if (!$janos) {
            $this->command->error('admin_janos user not found!');
            return;
        }

        $anna = Customer::where('email', 'anna@example.com')->first();
        $eva  = Customer::where('email', 'eva@example.com')->first();

        if (!$anna || !$eva) {
            $this->command->error('Customers not found!');
            return;
        }

        Appointment::create([
            'service'          => 'Klasszikus Hajvágás',
            'customer_id'      => $anna->id,
            'user_id'          => $janos->id, 
            'appointment_from' => Carbon::today()->setHour(9)->setMinute(15),
            'appointment_to'   => Carbon::today()->setHour(10)->setMinute(0),
        ]);

        Appointment::create([
            'service'          => 'Szakáll igazítás + Mosás',
            'customer_id'      => $eva->id,
            'user_id'          => $janos->id, 
            'appointment_from' => Carbon::tomorrow()->setHour(14)->setMinute(30),
            'appointment_to'   => Carbon::tomorrow()->setHour(15)->setMinute(15),
        ]);


    }
}