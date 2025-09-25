<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\DealEvent;

use App\Exceptions\DuplicateKeyException;
use App\Models\DealEvent as DealEventDB;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\DealEvent\DealEventId;
use App\Platform\Domains\DealEvent\DealEventRepositoryInterface;

readonly class DealEventRepository implements DealEventRepositoryInterface
{
    /**
     * @throws DuplicateKeyException
     */
    public function insert(DealEvent $dealEvent): void
    {
        if ($this->hasDuplicate($dealEvent->id)) {
            throw new DuplicateKeyException('取引イベントが重複しています。');
        }
        DealEventDB::create([
            'id' => $dealEvent->id->value,
            'user_id' => $dealEvent->userId->value,
            'deal_id' => $dealEvent->dealId->value,
            'actor_type' => $dealEvent->actorType->value,
            'event_type' => $dealEvent->eventType->value,
        ]);
    }

    private function hasDuplicate(DealEventId $dealEventId): bool
    {
        return DealEventDB::find($dealEventId->value) !== null;
    }
}