<?php

namespace Tests\Unit\Jobs;

use App\Jobs\GenerateBookReportJob;
use App\Models\Book;
use App\Models\BookReport;
use Tests\TestCase;

class GenerateBookReportJobTest extends TestCase
{
    public function test_handle_creates_or_updates_book_report(): void
    {
        // different books by one author
        $book1 = Book::factory()->create([
            'title'        => 'First',
            'author'       => 'Same Author',
            'published_at' => '2023-01-01 10:00:00',
        ]);

        $book2 = Book::factory()->create([
            'title'        => 'Second',
            'author'       => 'Same Author',
            'published_at' => '2024-01-01 10:00:00',
        ]);

        // job за book2
        $job = new GenerateBookReportJob($book2->id);

        $job->handle();

        $report = BookReport::where('book_id', $book2->id)->first();

        $this->assertNotNull($report);
        $this->assertNotNull($report->generated_at);

        $this->assertStringContainsString($book2->title, $report->summary);
        $this->assertStringContainsString($book2->author, $report->summary);

        $this->assertEquals(2, $report->stats['books_by_author']);
        $this->assertEquals(mb_strlen('Second'), $report->stats['title_length']);
        $this->assertEquals('2024', $report->stats['year']);
    }

    public function test_handle_does_nothing_if_book_missing(): void
    {
        $job = new GenerateBookReportJob(99999);

        $job->handle();

        $this->assertDatabaseCount('book_reports', 0);
    }
}
