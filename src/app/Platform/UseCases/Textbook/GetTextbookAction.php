<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\UseCases\Textbook\Dtos\TextbookWithRelationsDto;

readonly class GetTextbookAction
{
    public function __construct(
        private TextbookRepositoryInterface $textbookRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(
        GetTextbookActionValuesInterface $actionValues,
        string $textbookIdString
    ): TextbookWithRelationsDto {
        AppLog::start(__METHOD__);

        $textbookId = new TextbookId($textbookIdString);

        $requestParams = [
            'textbook_id' => $textbookId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            // ログインユーザー情報取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            $currentUserId = $authenticatedUser?->getUserId()?->value;

            $textbookModel = $this->textbookRepository->findByIdWithRelations($textbookId);

            if ($textbookModel === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            return TextbookWithRelationsDto::fromEloquentModel($textbookModel, $currentUserId);

        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
