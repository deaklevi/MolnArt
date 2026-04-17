<?php

// database/seeders/CustomerSeeder.php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    use WithoutModelEvents;

    public function run(): void
    {
        
        // Use the actual user names from DatabaseSeeder
        $janos = User::where('user_name', 'admin_janos')->first();
        $kata  = User::where('user_name', 'admin_kata')->first();
        $bela  = User::where('user_name', 'admin_bela')->first();

        Customer::insert([
            [
                'name'         => 'Anna Kiss',
                'email'        => 'anna@example.com',
                'phone_number' => '+36201234567',
                'user_id'      => $janos->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Eva Nagy',
                'email'        => 'eva@example.com',
                'phone_number' => '+36309876543',
                'user_id'      => $kata->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'name'         => 'Péter Szabó',
                'email'        => 'peter@example.com',
                'phone_number' => '+36701234567',
                'user_id'      => $bela->id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}
