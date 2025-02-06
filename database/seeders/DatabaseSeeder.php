<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create a personal access token for the user.
        $token = $user->createToken('default')->plainTextToken;

        $this->command->info("User Access Token: {$token}");

        // Create 5 categories.
        $categories = Category::factory()->count(5)->create();

        // Create 20 posts.
        $posts = Post::factory()->count(20)->create();

        // Attach 1 to 3 random categories to each post.
        foreach ($posts as $post) {
            $post->categories()->sync(
                $categories->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
    }
}
