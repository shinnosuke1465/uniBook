<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\DealEvent;

use App\Exceptions\DomainException;
use App\Models\DealEvent as DealEventDB;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\DealEvent\ActorType;
use App\Platform\Domains\DealEvent\DealEvent;
use App\Platform\Domains\DealEvent\DealEventId;
use App\Platform\Domains\DealEvent\EventType;
use App\Platform\Domains\User\UserId;

class DealEventFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        DealEventDB $dealEventDB
    ): DealEvent {
        return new DealEvent(
            new DealEventId($dealEventDB->id),
            new UserId($dealEventDB->user_id),
            new DealId($dealEventDB->deal_id),
            ActorType::create($dealEventDB->actor_type),
            EventType::create($dealEventDB->event_type),
        );
    }
}