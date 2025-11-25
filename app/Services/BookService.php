<?php

namespace App\Services;

use App\Jobs\GenerateBookReportJob;
use App\Repositories\BookRepository;
use App\DTOs\BookData;
use App\Events\BookCreated;
use App\Models\Book;

class BookService
{
    public function __construct(private BookRepository $repo)
    {
    }

    public function create(BookData $data): Book
    {
        $book = $this->repo->create($data);
        event(new BookCreated($book));

        return $book;
    }
}
