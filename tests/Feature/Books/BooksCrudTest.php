<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BooksCrudTest extends TestCase
{
    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        // Migrations + seeders (AdminUser + roles/permissions)
        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();

        // Tell Passport: "this user is logged through api guard
        Passport::actingAs($this->admin, [], 'api');
    }

    public function test_index_returns_paginated_books_list(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_store_creates_book(): void
    {
        // title','author','published_at
        $payload = [
            'title'        => 'Test book',
            'author'       => 'Some Author',
            'published_at' => '2025-01-01 14:21:25',
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'title'  => 'Test book',
                'author' => 'Some Author',
            ]);

        $this->assertDatabaseHas('books', [
            'title'  => 'Test book',
            'author' => 'Some Author',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/v1/books', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    public function test_show_returns_single_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson('/api/v1/books/'.$book->id);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'id'    => $book->id,
                'title' => $book->title,
            ]);
    }

    public function test_update_modifies_book(): void
    {
        $book = Book::factory()->create([
            'title' => 'Old title',
        ]);

        $response = $this->putJson('/api/v1/books/'.$book->id, [
            'title'       => 'New title',
            'author'      => $book->author,
            'published_at' => $book->published_at,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'New title',
            ]);

        $this->assertDatabaseHas('books', [
            'id'    => $book->id,
            'title' => 'New title',
        ]);
    }

    public function test_destroy_deletes_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson('/api/v1/books/'.$book->id);

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}
