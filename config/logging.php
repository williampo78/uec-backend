<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
     */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
     */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'sentry'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'sentry' => [
            'driver' => 'sentry',
            'level' => env('LOG_LEVEL', 'error'),
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
        'changepoint' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/changepoint/changepoint.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 365,
        ],
        'tappay_api_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/tappay/tappay_api_log.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 365,
        ],
        'shipment' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/shipment/shipment.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 365,
        ],
        'refund_error' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/refund/error/refund.log'),
        ],
        'refund_params' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/refund/refund_params/refund.log'),
        ],
        'batch_upload' => [
            'driver' => 'daily',
            'path' => storage_path('logs/batch_upload/batch.log'),
        ],
    ],

];
