<?php

namespace App\Listeners;

use App\Events\BookCreated;
use Illuminate\Support\Facades\Log;

class LogBookCreatedListener
{
    public function handle(BookCreated $event): void
    {
        Log::info('BookCreated', [
            'book_id' => $event->book->id,
            'title'   => $event->book->title,
            'author'  => $event->book->author,
        ]);
    }
}
