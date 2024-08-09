<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $faker = Faker::create();

        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            for ($j = 0; $j < 3; $j++) {
                Post::create([
                    'user_id' => $user->id,
                    'message' => $faker->sentence(),
                ]);
            }
        }
    }
}
