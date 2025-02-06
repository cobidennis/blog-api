<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Data provider for user login scenarios.
     */
    public static function loginDataProvider(): array
    {
        return [
            'valid credentials' => [
                'email' => 'test1@example.com',
                'password' => 'password123',
                'expectedStatus' => 200,
                'expectedJsonStructure' => ['user' => ['id', 'name', 'email'], 'token'],
                'expectedJsonErrors' => ['email'],
                'shouldPass' => true,
            ],
            'invalid credentials' => [
                'email' => 'test1@example.com',
                'password' => 'wrongpassword',
                'expectedStatus' => 422,
                'expectedJsonStructure' => [],
                'expectedJsonErrors' => ['email'],
                'shouldPass' => false,
            ],
        ];
    }

    #[DataProvider('loginDataProvider')]
    public function test_user_login_scenarios(
        string $email,
        string $password,
        int $expectedStatus,
        array $expectedJsonStructure = [],
        array $expectedJsonErrors = [],
        bool $shouldPass,
    ): void {
        // Create a user with known credentials.
        User::factory()->create([
            'email' => 'test1@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Attempt to log in.
        $response = $this->postJson('/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        // Assert the expected response status.
        $response->assertStatus($expectedStatus);

        if ($shouldPass) {
            // If the test should pass, check for token and user structure.
            $response->assertJsonStructure($expectedJsonStructure);
        } else {
            // If the test should fail, check for validation errors.
            $response->assertJsonValidationErrors($expectedJsonErrors);
        }
    }

    /**
     * Test that a user can log in with valid credentials.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        // Create a user with known credentials.
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user'  => ['id', 'name', 'email'],
                'token'
            ]);
    }

    /**
     * Test that login fails with invalid credentials.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that the /me endpoint returns the authenticated user's details.
     */
    public function test_me_endpoint_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Create a token for the user.
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJson([
                'id'    => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * Test the logout functionality.
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out']);
    }
}
