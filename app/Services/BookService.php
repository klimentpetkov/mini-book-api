<?php

namespace App\Services;

use App\DTOs\BookData;
use App\Events\BookCreated;
use App\Models\Book;
use App\Repositories\BookRepository;

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
