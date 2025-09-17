<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Authenticate;

use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\UseCases\Authenticate\Dtos\AuthenticateTokenDto;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use Exception;
use Illuminate\Auth\AuthenticationException;

readonly class CreateTokenAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ){
    }

    /**
     * @throws Exception
     * @throws AuthenticationException
     */
    public function __invoke(
        CreateTokenActionValuesInterface $actionValues,
    ): AuthenticateTokenDto
    {
        AppLog::start(__METHOD__);

        $requestParams = [];

        try {
            $loginId = $actionValues->getEmail();
            $password = $actionValues->getUserPassword();

            $requestParams = [
                'loginId' => $loginId->mailAddress->value,
            ];

            //ログ出力
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $user = $this->userRepository->findByLoginId($loginId);

            if ($user->password !== $password || $user === null) {
                throw new AuthenticationException('認証に失敗しました');
            }

            $authenticateToken = $this->userRepository->createToken();

            return AuthenticateTokenDto::create($authenticateToken);

        } catch (AuthenticationException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw new AuthenticationException('ログインに失敗しました。');
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
