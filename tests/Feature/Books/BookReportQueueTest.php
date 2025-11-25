<?php

namespace Tests\Feature\Books;

use App\Jobs\GenerateBookReportJob;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Queue;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BookReportQueueTest extends TestCase
{
    protected User $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();

        // tell Passport we have an api guard logged user
        Passport::actingAs($this->admin, [], 'api');
    }

    public function test_creating_book_dispatches_generate_book_report_job(): void
    {
        Queue::fake();

        $payload = [
            'title'        => 'Queued report book',
            'author'       => 'Queue Tester',
            'published_at' => '2025-01-01 10:00:00',
        ];

        $response = $this->postJson('/api/v1/books', $payload);

        $response->assertStatus(201);

        Queue::assertPushed(GenerateBookReportJob::class, function ($job) {
            $this->assertIsInt($job->bookId);

            return true;
        });
    }
}
