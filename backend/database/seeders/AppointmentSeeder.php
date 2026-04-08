<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Product;
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

        $hairDye   = Product::where('name', 'Hair Dye')->first();
        $shampoo   = Product::where('name', 'Shampoo')->first();
        $hairSpray = Product::where('name', 'Hair Spray')->first();

        if (!$hairDye || !$shampoo || !$hairSpray) {
            $this->command->error('Products not found! Run ProductSeeder first.');
            return;
        }

        $appt1 = Appointment::create([
            'service'          => 'Klasszikus Hajvágás',
            'customer_id'      => $anna->id,
            'user_id'          => $janos->id,
            'appointment_from' => Carbon::today()->setHour(9)->setMinute(15),
            'appointment_to'   => Carbon::today()->setHour(10)->setMinute(0),
        ]);

        $appt1->products()->attach([
            $shampoo->id  => ['quantity' => 30],
            $hairSpray->id => ['quantity' => 15],
        ]);

        $appt2 = Appointment::create([
            'service'          => 'Szakáll igazítás + Mosás',
            'customer_id'      => $eva->id,
            'user_id'          => $janos->id,
            'appointment_from' => Carbon::tomorrow()->setHour(14)->setMinute(30),
            'appointment_to'   => Carbon::tomorrow()->setHour(15)->setMinute(15),
        ]);

        $appt2->products()->attach([
            $hairDye->id  => ['quantity' => 120],
            $shampoo->id  => ['quantity' => 50],
        ]);
    }
}