<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use AppLog;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\Domains\User\UserRepositoryInterface;
use App\Platform\Infrastructures\Textbook\TextbookRepository;
use App\Platform\UseCases\Textbook\Dtos\TextbookWithRelationsDto;

readonly class GetTextbooksAction
{
    public function __construct(
        private TextbookRepositoryInterface $textbookRepository,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @return TextbookWithRelationsDto[]
     */
    public function __invoke(
        GetTextbooksActionValuesInterface $actionValues,
    ): array {
        AppLog::start(__METHOD__);

        try {
            // ログインユーザー情報取得
            $authenticatedUser = $this->userRepository->getAuthenticatedUser();
            $currentUserId = $authenticatedUser?->getUserId()?->value;

            $textbookModels = $this->textbookRepository->findAllWithRelations();

            return $textbookModels->map(function($textbookModel) use ($currentUserId) {
                return TextbookWithRelationsDto::fromEloquentModel($textbookModel, $currentUserId);
            })->all();

        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
