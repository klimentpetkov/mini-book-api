<?php

namespace App\Repositories;

use App\DTOs\BookData;
use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookRepository
{
    /** @return LengthAwarePaginator<int, Book> */
    public function list(int $perPage = 10): LengthAwarePaginator
    {
        return Book::query()->orderByDesc('id')->paginate($perPage);
    }

    public function create(BookData $data): Book
    {
        return Book::create($data->toArray());
    }
}
