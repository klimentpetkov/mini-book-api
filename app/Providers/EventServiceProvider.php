<?php

namespace App\Providers;

use App\Events\BookCreated;
use App\Listeners\GenerateBookReportListener;
use App\Listeners\LogBookCreatedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BookCreated::class => [
            GenerateBookReportListener::class,
            LogBookCreatedListener::class,
        ],
    ];
}
