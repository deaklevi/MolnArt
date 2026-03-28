<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('reviews')->insert([
            [
                'profile_image' => null,
                'name' => 'John Doe',
                'rating' => 5,
                'comment' => 'Amazing service, highly recommended!',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Jane Smith',
                'rating' => 4,
                'comment' => 'Very good experience overall.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Mike Johnson',
                'rating' => 3,
                'comment' => 'It was okay, could be better.',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // More reviews
            [
                'profile_image' => null,
                'name' => 'Emily Davis',
                'rating' => 5,
                'comment' => 'Absolutely loved it!',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Chris Brown',
                'rating' => 2,
                'comment' => 'Not satisfied with the service.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Sophia Wilson',
                'rating' => 4,
                'comment' => 'Pretty good, will come again.',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Daniel Martinez',
                'rating' => 5,
                'comment' => 'Top-notch quality!',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Laura Garcia',
                'rating' => 3,
                'comment' => 'Average experience.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'David Anderson',
                'rating' => 4,
                'comment' => 'Good service and friendly staff.',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Olivia Thomas',
                'rating' => 5,
                'comment' => 'Perfect experience!',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 10 more
            [
                'profile_image' => null,
                'name' => 'James Taylor',
                'rating' => 4,
                'comment' => 'Very solid service.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Isabella Moore',
                'rating' => 5,
                'comment' => 'Exceeded expectations!',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'William Jackson',
                'rating' => 2,
                'comment' => 'Could improve a lot.',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Mia White',
                'rating' => 4,
                'comment' => 'Nice and clean environment.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Benjamin Harris',
                'rating' => 3,
                'comment' => 'Decent but not great.',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Charlotte Clark',
                'rating' => 5,
                'comment' => 'Loved every moment!',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Lucas Lewis',
                'rating' => 4,
                'comment' => 'Good value for money.',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Amelia Walker',
                'rating' => 5,
                'comment' => 'Fantastic!',
                'user_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Henry Hall',
                'rating' => 3,
                'comment' => 'Not bad, not great.',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'profile_image' => null,
                'name' => 'Evelyn Allen',
                'rating' => 5,
                'comment' => 'Will definitely return!',
                'user_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
