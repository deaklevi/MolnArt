<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generálunk egy kezdő dátumot -2 nap és +2 hónap között
        $start = fake()->dateTimeBetween('-2 days', '+2 months');
        // A vége legyen 30-90 perccel később
        $end = (clone $start)->modify('+' . rand(30, 90) . ' minutes');

        return [
            'appointment_from' => $start,
            'appointment_to' => $end,
            'service' => fake()->randomElement(['Tanácsadás', 'Hajvágás', 'Masszázs', 'Személyes edzés']),
            'customer_id' => Customer::factory(),
        ];
    }
}
