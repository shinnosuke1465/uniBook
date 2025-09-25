<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealEvent;

use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\User\UserId;

readonly class DealEvent
{
    public function __construct(
        public DealEventId $id,
        public UserId $userId,
        public DealId $dealId,
        public ActorType $actorType,
        public EventType $eventType,
    ) {
    }

    public static function create(
        UserId $userId,
        DealId $dealId,
        ActorType $actorType,
        EventType $eventType,
    ): self {
        return new self(
            new DealEventId(),
            $userId,
            $dealId,
            $actorType,
            $eventType,
        );
    }
}