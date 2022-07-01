<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Services\ImageUploadService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ImageUploadService::class);
        $this->app->alias(ImageUploadService::class, 'ImageUpload');

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.enforce_ssl')) {
            URL::forceScheme('https');
        }
    }
}
