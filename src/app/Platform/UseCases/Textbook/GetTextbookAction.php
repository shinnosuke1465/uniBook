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
            $currentUserId = null;
            try {
                $authenticatedUser = $this->userRepository->getAuthenticatedUser();
                $currentUserId = $authenticatedUser?->getUserId()?->value;
            } catch (\Exception $e) {
                // 認証エラーの場合はnullのまま（未認証ユーザー）
            }

            if ($this->textbookRepository instanceof TextbookRepository) {
                $textbookModel = $this->textbookRepository->findByIdWithRelations($textbookId);

                if ($textbookModel === null) {
                    throw new NotFoundException('教科書が見つかりません。');
                }

                return TextbookWithRelationsDto::fromEloquentModel($textbookModel, $currentUserId);
            }

            // フォールバック（通常のfindById）
            $textbook = $this->textbookRepository->findById($textbookId);

            if ($textbook === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            return new TextbookWithRelationsDto(
                $textbook->id->value,
                $textbook->name->value,
                $textbook->price->value,
                $textbook->description->value,
                $textbook->imageIdList->toArray(),
                '',
                '',
                $textbook->conditionType->value,
                null,
                [], // comments
                false,
            );
        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}
