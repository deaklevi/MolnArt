<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("services")->insert(
            [
                [
                    'id' => 1,
                    'name' => 'Női hajvágás',
                    'time' => 60,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 2,
                    'name' => 'Gyermek hajvágás',
                    'time' => 30,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 3,
                    'name' => 'Férfi hajvágás',
                    'time' => 45,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 4,
                    'name' => 'Férfi hajvágás szinezés',
                    'time' => 70,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 5,
                    'name' => 'Férfi hajfestés + vágás',
                    'time' => 70,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 6,
                    'name' => 'Konzultáció',
                    'time' => 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 7,
                    'name' => 'Egész melír',
                    'time' => 180,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 8,
                    'name' => 'Részleges melír',
                    'time' => 120,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 9,
                    'name' => 'Esküvői és alkalmi frizura',
                    'time' => 90,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 10,
                    'name' => 'Alkalmi smink',
                    'time' => 60,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 11,
                    'name' => 'Frufru vágás',
                    'time' => 15,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 12,
                    'name' => 'Női szárítás (rövid)',
                    'time' => 45,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 13,
                    'name' => 'Női szárítás (hosszú)',
                    'time' => 65,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 14,
                    'name' => 'Ombre/Balayage festés',
                    'time' => 180,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 15,
                    'name' => 'Ombre/Balayage festés vágással',
                    'time' => 210,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 16,
                    'name' => 'Ombre/Balayage festés vágással extra hosszu',
                    'time' => 240,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 17,
                    'name' => 'Színezés',
                    'time' => 75,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 18,
                    'name' => 'Színezés-vágás',
                    'time' => 105,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 19,
                    'name' => 'Színváltoztatás - sötétebbre',
                    'time' => 120,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 20,
                    'name' => 'Színváltoztatás - világosra',
                    'time' => 180,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 21,
                    'name' => 'Tartós hajkiegyenesítés és kezelés Brasil Cacau',
                    'time' => 180,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 22,
                    'name' => 'Tartós hajkiegyenesítés Brasil Cacau extra hosszu',
                    'time' => 240,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 23,
                    'name' => 'Teljes festés - vágás',
                    'time' => 120,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 24,
                    'name' => 'Teljes festés',
                    'time' => 90,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 25,
                    'name' => 'Tőfestés',
                    'time' => 90,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 26,
                    'name' => 'Tőfestés-vágás',
                    'time' => 120,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 27,
                    'name' => 'Dauer férfi',
                    'time' => 90,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => 28,
                    'name' => 'Dauer noi',
                    'time' => 180,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
    }
}
