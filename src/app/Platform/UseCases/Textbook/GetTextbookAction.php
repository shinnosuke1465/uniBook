<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use App\Exceptions\DomainException;
use App\Exceptions\NotFoundException;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\UseCases\Shared\HandleUseCaseLogs;
use AppLog;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\UseCases\Textbook\Dtos\TextbookDto;

readonly class GetTextbookAction
{
    public function __construct(
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @throws NotFoundException
     * @throws DomainException
     */
    public function __invoke(
        GetTextbookActionValuesInterface $actionValues,
        string $textbookIdString
    ): TextbookDto {
        AppLog::start(__METHOD__);

        $textbookId = new TextbookId($textbookIdString);

        $requestParams = [
            'textbook_id' => $textbookId->value,
        ];

        try {
            AppLog::info(__METHOD__, [
                'request' => $requestParams,
            ]);

            $textbook = $this->textbookRepository->findById($textbookId);

            if ($textbook === null) {
                throw new NotFoundException('教科書が見つかりません。');
            }

            return TextbookDto::create($textbook);
        } catch (NotFoundException $e) {
            HandleUseCaseLogs::execMessage(__METHOD__, $e->getMessage(), $requestParams);
            throw $e;
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}