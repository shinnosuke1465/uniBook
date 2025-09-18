<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Authenticate;

use App\Exceptions\IllegalUserException;
use App\Exceptions\InvalidValueException;
use App\Platform\Domains\User\UserRepositoryInterface;
use AppLog;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;

readonly class LogoutAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ){
    }
    /**
     * @throws Throwable
     * @throws IllegalUserException
     * @throws InvalidValueException
     */
    public function __invoke(
        LogoutActionValuesInterface $actionValues,
    ): void {
        try {
            AppLog::start(__METHOD__);

            $requestParams = [];

            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $this->userRepository->deleteToken();

            //ログアウト処理（API認証ではトークン削除のみ）
        } catch (InvalidValueException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), []);
            throw new InvalidValueException('ログアウトに失敗しました。');
        } catch (IllegalUserException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), []);
            throw new IllegalUserException('ログアウトに失敗しました。');
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
