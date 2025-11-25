<?php

namespace Tests\Feature\Books;

use App\Events\BookCreated;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LogBookCreatedListenerTest extends TestCase
{
    public function test_it_logs_book_created(): void
    {
        Log::spy();

        $book = \App\Models\Book::factory()->create();

        event(new BookCreated($book));

        Log::shouldHaveReceived('info')->with('BookCreated', \Mockery::on(function ($data) use ($book) {
            return $data['book_id'] === $book->id;
        }))->once();
    }
}
