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
        // Share cityName and selectedCity with all views
        view()->composer('*', function ($view) {
            $request = request();
            
            // Если cityName уже установлена в view, не переопределяем
            if (!$view->offsetExists('cityName')) {
                // Приоритет: URL параметр > cookie > default
                $selectedCity = $request->input('city');
                
                if (!$selectedCity) {
                    $selectedCity = $request->cookie('selectedCity', 'moscow');
                }
                
                // Validate city
                if (!in_array($selectedCity, ['moscow', 'spb'])) {
                    $selectedCity = 'moscow';
                }
                
                $cityName = $selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
                
                $view->with('cityName', $cityName);
                $view->with('selectedCity', $selectedCity);
            }
        });
    }
}
