<?php

declare(strict_types=1);

namespace App\Services\App;

use Log;

class AppLogger
{
    public function debug(string $method, array $context = []): void
    {
        Log::debug($method, $context);
    }

    public function info(string $method, array $context = []): void
    {
        Log::info($method, $context);
    }

    public function start(string $method): void
    {
        Log::info($method, ['START']);
    }

    public function end(string $method): void
    {
        Log::info($method, ['END']);
    }

    public function warning(string $method, string $message, array $parameters = []): void
    {
        Log::warning($method, [
            'parameter' => $parameters,
            'warning' => [
                'message' => $message,
            ],
        ]);
    }

    public function error(string $method, string $message, array $parameters = []): void
    {
        Log::error($method, [
            'parameter' => $parameters,
            'error' => [
                'message' => $message,
            ],
        ]);
    }

    public function slack(string $method, string $message, array $parameters = []): void
    {
        Log::channel('slack')->error($method, [
            'parameter' => $parameters,
            'error' => [
                'message' => $message,
            ],
        ]);
    }
}
