<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Keressük meg azt a felhasználót (szolgáltatót), akihez a naptár tartozik.
        // Itt feltételezzük, hogy az 1-es ID-jú user a naptár tulajdonosa.
        $user = User::find(1);

        if (!$user) {
            $this->command->error('User nem található az 1-es ID-val!');
            return;
        }

        // --- 1. ADAT: FOGLALÁS MÁRA ---
        // Létrehozzuk a vevőt a Customer táblában
        $customer1 = Customer::create([
            'name' => 'Kovács Gellért',
            'email' => 'gellert@pelda.hu',
            'phone_number' => '+36301112233',
            'user_id' => $user->id, // A vevőt a szolgáltatóhoz kötjük
        ]);

        // Létrehozzuk a hozzá tartozó foglalást
        Appointment::create([
            'service' => 'Klasszikus Hajvágás',
            'customer_id' => $customer1->id, // Itt adjuk át a frissen létrehozott vevő ID-ját
            'appointment_from' => Carbon::today()->setHour(9)->setMinute(15)->format('Y-m-d H:i:s'),
            'appointment_to' => Carbon::today()->setHour(10)->setMinute(45)->format('Y-m-d H:i:s'),
        ]);

        // --- 2. ADAT: FOGLALÁS HOLNAPRA ---
        // Létrehozzuk a második vevőt
        $customer2 = Customer::create([
            'name' => 'Szabó Bence',
            'email' => 'bence@pelda.hu',
            'phone_number' => '+36204445566',
            'user_id' => $user->id,
        ]);

        // Létrehozzuk a holnapi foglalást
        Appointment::create([
            'service' => 'Szakáll igazítás + Mosás',
            'customer_id' => $customer2->id,
            'appointment_from' => Carbon::tomorrow()->setHour(14)->setMinute(30)->format('Y-m-d H:i:s'),
            'appointment_to' => Carbon::tomorrow()->setHour(15)->setMinute(15)->format('Y-m-d H:i:s'),
        ]);

        $this->command->info('A két teszt foglalás és a vevők sikeresen létrehozva!');
    }
}
