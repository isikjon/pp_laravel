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
                $city = getCurrentCity();
                $cityName = $city ? $city->name : 'Москва';
                $selectedCity = $city ? $city->code : 'moscow';
                
                $view->with('cityName', $cityName);
                $view->with('selectedCity', $selectedCity);
                $view->with('currentCity', $city);
            }
        });
    }
}
