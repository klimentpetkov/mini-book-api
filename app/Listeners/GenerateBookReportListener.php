<?php

namespace App\Listeners;

use App\Events\BookCreated;
use App\Jobs\GenerateBookReportJob;

class GenerateBookReportListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookCreated $evt): void
    {
        GenerateBookReportJob::dispatch($evt->book->id);
    }
}
