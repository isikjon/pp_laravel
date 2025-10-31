<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        
    }

    public function boot(): void
    {
        $this->loadModules();
    }

    protected function loadModules(): void
    {
        $modulesPath = app_path('Modules');
        
        if (!is_dir($modulesPath)) {
            return;
        }

        $modules = array_filter(scandir($modulesPath), function ($item) use ($modulesPath) {
            return is_dir($modulesPath . '/' . $item) && !in_array($item, ['.', '..']);
        });

        foreach ($modules as $module) {
            $this->loadModuleRoutes($module);
            $this->loadModuleViews($module);
        }
    }

    protected function loadModuleRoutes(string $module): void
    {
        $routesPath = app_path("Modules/{$module}/Routes/web.php");
        
        if (file_exists($routesPath)) {
            Route::middleware('web')
                ->group($routesPath);
        }
    }

    protected function loadModuleViews(string $module): void
    {
        $viewsPath = app_path("Modules/{$module}/Views");
        
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, strtolower($module));
        }
    }
}

