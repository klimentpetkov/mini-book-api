<?php

namespace App\Jobs;

use App\Models\Book;
use App\Models\BookReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;

class GenerateBookReportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    /** @var int|array<int,int> */
    public int|array $backoff = [5, 10, 20, 40];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $bookId)
    {
        //
    }

    /** @return array<int, object> */
    public function middleware(): array
    {
        return [
            (new WithoutOverlapping('book:' . $this->bookId))->releaseAfter(30),
        ];
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $book = Book::find($this->bookId);

        if (!$book) {
            // the book is deleted/missing - there is nothing to report
            return;
        }

        // stats
        $booksByAuthor = Book::where('author', $book->author)->count();
        $titleLength   = mb_strlen($book->title);
        $year          = optional($book->published_at)->format('Y');

        $summary = sprintf(
            "Book '%s' by %s. Total books by this author: %d.",
            $book->title,
            $book->author,
            $booksByAuthor,
        );

        BookReport::updateOrCreate(
            ['book_id' => $book->id],
            [
                'summary'      => $summary,
                'stats'        => [
                    'title_length'     => $titleLength,
                    'books_by_author'  => $booksByAuthor,
                    'year'             => $year,
                ],
                'generated_at' => now(),
            ]
        );

        logger()->info('Creating report for book ID: '.$this->bookId);
    }
}
