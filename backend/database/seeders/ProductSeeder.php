<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            ['name' => 'Hair Dye',      'unit' => 'ml',  'stock' => 500, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shampoo',       'unit' => 'ml',  'stock' => 1000,'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hair Spray',    'unit' => 'ml',  'stock' => 300, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
