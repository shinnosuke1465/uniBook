<?php

declare(strict_types=1);

namespace Tests\Unit\Platform\Domains\Deal;

use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class TestDealFactory
{
    public static function create(
        DealId $id = new DealId(),
        Seller $seller = new Seller(new UserId()),
        Buyer $buyer = new Buyer(new UserId()),
        TextbookId $textbookId = new TextbookId(),
        DealStatus $dealStatus = DealStatus::Listing,
    ): Deal {
        return new Deal(
            $id,
            $seller,
            $buyer,
            $textbookId,
            $dealStatus,
        );
    }
}