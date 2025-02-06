<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_post(): void
    {
        $user = User::factory()->withToken()->create();
        $this->actingAs($user, 'sanctum');

        $payload = [
            'title'   => 'Test Post',
            'content' => 'This is a test post content.',
        ];

        $response = $this->postJson('/api/posts', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Test Post']);
    }

    public function test_get_post_by_slug(): void
    {
        $post = Post::factory()->create([
            'title'   => 'Sample Post',
            'content' => 'Sample content.',
            'slug'    => 'sample-post',
        ]);

        $response = $this->getJson("/api/posts/{$post->slug}");

        $response->assertStatus(200)
            ->assertJsonFragment(['slug' => $post->slug]);
    }

    public function test_unauthorized_user_cannot_create_post(): void
    {
        $response = $this->postJson('/api/posts', [
            'title'   => 'Unauthorized Post',
            'content' => 'This should not be allowed.',
        ]);

        $response->assertStatus(401);
    }

    public function test_post_creation_fails_with_validation_errors(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/posts', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_api_returns_paginated_posts(): void
    {
        Post::factory()->count(15)->create();

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'links'])
            ->assertJsonPath('meta.per_page', 10);
    }

    public function test_post_can_be_assigned_categories(): void
    {
        $post = Post::factory()->create();
        $categories = Category::factory()->count(2)->create();

        $post->categories()->attach($categories);

        $this->assertCount(2, $post->categories);
    }
}
