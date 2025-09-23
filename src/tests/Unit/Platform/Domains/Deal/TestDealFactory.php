<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class TestDealFactory
{
    public static function create(
        DealId $id = new DealId(),
        UserId $sellerId = new UserId(),
        UserId $buyerId = new UserId(),
        TextbookId $textbookId = new TextbookId(),
        DealStatus $dealStatus = DealStatus::Listing,
    ): Deal {
        return new Deal(
            $id,
            $sellerId,
            $buyerId,
            $textbookId,
            $dealStatus,
        );
    }
}