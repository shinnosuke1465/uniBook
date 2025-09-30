<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Deal;

use App\Exceptions\DomainException;
use App\Exceptions\DuplicateKeyException;
use App\Exceptions\NotFoundException;
use App\Models\Deal as DealDB;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealRepositoryInterface;
use App\Platform\Domains\Textbook\TextbookId;

readonly class DealRepository implements DealRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(Deal $deal): void
    {
        if ($this->hasDuplicate($deal->id)) {
            throw new DuplicateKeyException('取引が重複しています。');
        }
        DealDB::create([
            'id' => $deal->id->value,
            'seller_id' => $deal->seller->userId->value,
            'buyer_id' => $deal->buyer?->userId->value,
            'textbook_id' => $deal->textbookId->value,
            'deal_status' => $deal->dealStatus->value,
        ]);
    }

    /**
     * @throws DomainException
     */
    public function findByTextbookId(TextbookId $textbookId): ?Deal
    {
        $dealModel = DealDB::where('textbook_id', $textbookId->value)->first();
        if ($dealModel === null) {
            return null;
        }
        return DealFactory::create($dealModel);
    }

    /**
     * @throws NotFoundException
     */
    public function update(Deal $deal): void
    {
        $dealModel = DealDB::find($deal->id->value);
        if ($dealModel === null) {
            throw new NotFoundException('取引が存在しません。');
        }

        $dealModel->update([
            'buyer_id' => $deal->buyer?->userId->value,
            'deal_status' => $deal->dealStatus->value,
        ]);
    }

    private function hasDuplicate(DealId $dealId): bool
    {
        return DealDB::find($dealId->value) !== null;
    }
}
