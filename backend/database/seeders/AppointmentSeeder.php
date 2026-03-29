<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {
            // Minden ügyfélhez dobjunk be véletlenszerűen 1-3 időpontot
            Appointment::factory(rand(1, 3))->create([
                'customer_id' => $customer->id
            ]);
        }
    }
}
