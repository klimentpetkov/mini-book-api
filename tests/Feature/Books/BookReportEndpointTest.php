<?php

namespace Tests\Feature\Books;

use App\Jobs\GenerateBookReportJob;
use App\Models\Book;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BookReportEndpointTest extends TestCase
{
    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();

        Passport::actingAs($this->admin, [], 'api');
    }

    public function test_report_endpoint_returns_404_if_report_not_ready(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson('/api/v1/books/'.$book->id.'/report');

        $response
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Report not ready yet',
            ]);
    }

    public function test_report_endpoint_returns_report_when_exists(): void
    {
        $book = Book::factory()->create([
            'title'        => 'Reportable',
            'author'       => 'Reporter',
            'published_at' => '2024-01-01 10:00:00',
        ]);

        // generate report synchronously
        $job = new GenerateBookReportJob($book->id);
        $job->handle();

        $response = $this->getJson('/api/v1/books/'.$book->id.'/report');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'book_id',
                    'summary',
                    'stats',
                    'generated_at',
                    'created_at',
                    'updated_at',
                ],
            ])
            ->assertJsonFragment([
                'book_id' => $book->id,
            ]);
    }
}
