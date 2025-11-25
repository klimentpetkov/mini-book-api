<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Prevents: "Specified key was too long" for older MySQL versions
        Schema::defaultStringLength(191);

        // Avoids unnecessary wrapping in `data` in API resources
        JsonResource::withoutWrapping();
    }
}
