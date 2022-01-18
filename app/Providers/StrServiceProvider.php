<?php

namespace App\Providers;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\ServiceProvider;

class StrServiceProvider extends ServiceProvider
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
        Str::macro('myMask', function ($string, $character, $index, $length = null, $encoding = 'UTF-8') {
			if ($character === '') {
                return $string;
            }

            if (is_null($length) && PHP_MAJOR_VERSION < 8) {
                $length = mb_strlen($string, $encoding);
            }

            $segment = mb_substr($string, $index, $length, $encoding);

            if ($segment === '') {
                return $string;
            }

            $start = mb_substr($string, 0, $index, $encoding);
            $end = mb_substr($string, mb_strlen($start, $encoding) + mb_strlen($segment, $encoding));

            return $start.str_repeat(mb_substr($character, 0, 1, $encoding), mb_strlen($segment, $encoding)).$end;
		});

		Stringable::macro('myMask', function ($character, $index, $length = null, $encoding = 'UTF-8') {
            return new static(Str::myMask($this->value, $character, $index, $length, $encoding));
		});
    }
}
