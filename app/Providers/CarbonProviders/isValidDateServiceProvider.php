<?php

namespace App\Providers\CarbonProviders;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class isValidDateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::macro('isValidDate', function (
            $date,
            $month = null,
            $day = null
        ) {
            if (!is_null($day)) {
                $date = "{$date}-{$month}-{$day}";
            }

            $parsed = date_parse($date);

            return $parsed['error_count'] == 0 && ($parsed['warning_count'] == 0 || !in_array('The parsed date was invalid',$parsed['warnings'],));
        });
    }
}
