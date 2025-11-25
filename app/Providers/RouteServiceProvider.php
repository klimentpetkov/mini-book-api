<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    public function boot(): void
    {
        $this->configureRateLimiting();

        parent::boot();
    }

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $user = $request->user();
            return Limit::perMinute(60)->by(optional($user)->id ?: $request->ip());
        });
    }
}
