<?php

namespace Tests\Feature\Auth;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoginTest extends TestCase
{
    // protected static bool $passportInstalled = false;

    public function setUp(): void
    {
        parent::setUp();

        // Run seeders: AdminUserSeeder + RolePermissionSeeder
        $this->seed(DatabaseSeeder::class);

        Artisan::call('passport:keys', ['--force' => true]);

        // personal client for personal access tokens
        Artisan::call('passport:client', [
            '--personal'       => true,
            '--name'           => 'Test Personal Access Client',
            '--no-interaction' => true,
        ]);
    }

    public function test_login_success_returns_token(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'admin@example.com',
            'password' => 'password',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['token']);

        $this->assertIsString($response->json('token'));
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/login', [
            'email'    => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/me');

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_me_returns_authenticated_user(): void
    {
        // 1) Login through API to take token
        $login = $this->postJson('/api/v1/login', [
            'email'    => 'admin@example.com',
            'password' => 'password',
        ]);

        $token = $login->json('token');

        // 2) Hit /me with Bearer token
        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept'        => 'application/json',
        ])->getJson('/api/v1/me');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'email' => 'admin@example.com',
            ]);
    }
}
