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
        // Share cityName with all views
        view()->composer('*', function ($view) {
            $request = request();
            
            // Если cityName уже установлена в view, не переопределяем
            if (!$view->offsetExists('cityName')) {
                $selectedCity = $request->input('city', $request->cookie('selectedCity', 'moscow'));
                $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
                $view->with('cityName', $cityName);
                $view->with('selectedCity', $selectedCity);
            }
        });
    }
}
