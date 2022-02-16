<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageUploadService;
use Illuminate\Contracts\Routing\UrlGenerator;

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
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(UrlGenerator $url)
    {
        //
        if (env('ENFORCE_SSL', false)) {

            $url->forceScheme('https');

        }
    }
}
