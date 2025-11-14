<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (!$view->offsetExists('cityName')) {
                $selectedCity = getSelectedCity();
                $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
                
                $view->with('cityName', $cityName);
                $view->with('selectedCity', $selectedCity);
            }
        });
    }
}
