<?php

namespace Database\Seeders;

use App\Models\Service;
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
                'profile_image' => "/storage/avatars/kep1.jpg"
            ],
            [
                'user_name' => 'admin_kata',
                'password' => Hash::make('jelszo456'),
                'description' => 'Tartalomkezelő',
                'profile_image' => "/storage/avatars/kep2.jpg"
            ],
            [
                'user_name' => 'admin_bela',
                'password' => Hash::make('admin789'),
                'description' => 'Rendszergazda',
                'profile_image' => "/storage/avatars/kep3.jpg"
            ],
        ];

        foreach ($admins as $admin) {
            User::create($admin);
        }

       $this->call([
            ServiceSeeder::class,
            ReviewSeeder::class,
            CustomerSeeder::class,
            AppointmentSeeder::class,
        ]);

       $allServiceIds = Service::pluck('id')->toArray();

        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->services()->attach($allServiceIds);
        }
    }
}
