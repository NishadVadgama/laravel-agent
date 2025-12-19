<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
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
        // Create admin user
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create regular test user
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 10 additional regular users
        $users = User::factory(10)->create();

        // Create articles for admin (5 articles)
        Article::factory(5)->create([
            'user_id' => $admin->id,
        ]);

        // Create articles for test user (3 articles)
        Article::factory(3)->create([
            'user_id' => $testUser->id,
        ]);

        // Create articles for each regular user (random 2-5 articles per user)
        foreach ($users as $user) {
            Article::factory(rand(2, 5))->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
