<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Textbook;

use AppLog;
use App\Platform\Domains\Textbook\TextbookRepositoryInterface;
use App\Platform\UseCases\Textbook\Dtos\TextbookDto;

readonly class GetTextbooksAction
{
    public function __construct(
        private TextbookRepositoryInterface $textbookRepository,
    ) {
    }

    /**
     * @return TextbookDto[]
     */
    public function __invoke(
        GetTextbooksActionValuesInterface $actionValues,
    ): array {
        AppLog::start(__METHOD__);

        try {
            $textbooks = $this->textbookRepository->findAll();

            return collect($textbooks)->map(
                fn($textbook) => TextbookDto::create($textbook)
            )->all();
        } finally {
            AppLog::end(__METHOD__);
        }
    }
}