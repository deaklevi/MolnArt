<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

<<<<<<< HEAD
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
=======
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
>>>>>>> 9c00869fac303a97c058f4480a66c501c93583d7
    }
}
