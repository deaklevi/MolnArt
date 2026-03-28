<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admins = [
            [
                'user_name' => 'admin_janos',
                'password' => Hash::make('titkos123'),
                'description' => 'Főadminisztrátor',
            ],
            [
                'user_name' => 'admin_kata',
                'password' => Hash::make('jelszo456'),
                'description' => 'Tartalomkezelő',
            ],
            [
                'user_name' => 'admin_bela',
                'password' => Hash::make('admin789'),
                'description' => 'Rendszergazda',
            ],
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }

        $this->call(ServiceSeeder::class);
    }
}
