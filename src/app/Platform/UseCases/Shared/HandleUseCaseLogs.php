<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Shared;

use App\Exceptions\DomainException;
use App\Exceptions\IllegalRequestParameterException;
use App\Exceptions\NotFoundException;
use App\Exceptions\SrmsException;
use AppLog;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

readonly class HandleUseCaseLogs
{
    /**
     * ログ出力
     *
     * @param array<string, mixed> $requestParams
     */
    public static function execMessage(string $methodName, string $message, array $requestParams): void
    {
        Log::notice($message, [
            'method' => $methodName,
            'params' => $requestParams,
        ]);
    }

    /**
     * ログ出力

     *
     * @param array<string, mixed> $requestParams
     */
    public static function exec(Exception $e, array $requestParams): void
    {
        if ($e instanceof DomainException
            || $e instanceof NotFoundException
            || $e instanceof AuthorizationException
            || $e instanceof IllegalRequestParameterException
            || $e instanceof SrmsException
        ) {
            AppLog::warning(
                __METHOD__,
                '警告の内容:',
                ['request' => $requestParams, 'exception' => (string)$e]
            );
        } else {
            AppLog::error(
                __METHOD__,
                'エラーの内容:',
                ['request' => $requestParams, 'exception' => (string)$e]
            );
        }
    }
}
