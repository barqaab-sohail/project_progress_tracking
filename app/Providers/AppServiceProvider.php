<?php

namespace App\Providers;

use App\Observers\ProgressObserver;
use App\Models\BuildingActualProgress;
use Illuminate\Support\ServiceProvider;
use App\Models\BuildingScheduleProgress;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        BuildingActualProgress::observe(ProgressObserver::class);
        BuildingScheduleProgress::observe(ProgressObserver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
