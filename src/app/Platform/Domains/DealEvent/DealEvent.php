<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealEvent;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\Seller;
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

    public static function listed(Seller $seller, DealId $dealId): self
    {
        return new self(
            new DealEventId(),
            $seller->userId,
            $dealId,
            ActorType::Seller,
            EventType::Listing,
        );
    }

    public static function purchased(Buyer $buyer, DealId $dealId): self
    {
        return new self(
            new DealEventId(),
            $buyer->userId,
            $dealId,
            ActorType::Buyer,
            EventType::Purchase,
        );
    }

    public static function delivered(Seller $seller, DealId $dealId): self
    {
        return new self(
            new DealEventId(),
            $seller->userId,
            $dealId,
            ActorType::Seller,
            EventType::ReportDelivery,
        );
    }

    public static function received(Buyer $buyer, DealId $dealId): self
    {
        return new self(
            new DealEventId(),
            $buyer->userId,
            $dealId,
            ActorType::Buyer,
            EventType::ReportReceipt,
        );
    }

    public static function cancelled(Seller $seller, DealId $dealId): self
    {
        return new self(
            new DealEventId(),
            $seller->userId,
            $dealId,
            ActorType::Seller,
            EventType::Cancel,
        );
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