<?php

namespace App\Exceptions;

use App\Exceptions\Constracts\WarningExceptionInterface;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Psr\Log\LogLevel;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, LogLevel::*>
     */
    protected $levels = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * 異常系: クライアント側
     * 400:Bad Request(不正なリクエスト)
     * 401:Unauthorized（認証）
     * 404:Not Found（存在しないページ兼認可）
     * 409:Conflict(コンフリクト）
     * 419:Invalid CSRF Token(CSRF Tokenの期限切れ)
     * 422:Unprocessable Content(バリデーション)
     * 429:Too Many Requests(リクエスト数超過)
     *
     * 異常系: サーバー側
     * 500:Internal Server Error
     * 504:Gateway Timeout(サーバータイムアウト)
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof NotFoundException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
        if ($e instanceof DomainException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($e instanceof IllegalUserException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($e instanceof IllegalRequestParameterException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
        if ($e instanceof DuplicateKeyException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 409);
        }
        if ($e instanceof RepositoryException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($e instanceof InvalidValueException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($e instanceof UseCaseException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }

        return parent::render($request, $e);
    }

    public function report(Throwable $e): void
    {
        // リクエストが存在する場合は、リクエストパラメータを取得
        $requestParams = app()->bound('request') ? request()->all() : [];

        // リクエストパラメータをログに記録
        match (true) {
            $e instanceof WarningExceptionInterface => Log::warning(
                '警告の内容:',
                [
                    'requestParams' => $requestParams,
                    'exception' => (string)$e,
                ]
            ),
            default => Log::error(
                'エラーの内容:',
                [
                    'requestParams' => $requestParams,
                    'exception' => (string)$e,
                ]
            ),
        };
    }
}
