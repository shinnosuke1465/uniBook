<?php

declare(strict_types=1);

namespace App\Platform\Infrastructures\Deal;

use App\Exceptions\DomainException;
use App\Models\Deal as DealDB;
use App\Platform\Domains\Deal\Buyer;
use App\Platform\Domains\Deal\Deal;
use App\Platform\Domains\Deal\DealId;
use App\Platform\Domains\Deal\DealStatus;
use App\Platform\Domains\Deal\Seller;
use App\Platform\Domains\Textbook\TextbookId;
use App\Platform\Domains\User\UserId;

class DealFactory
{
    /**
     * @throws DomainException
     */
    public static function create(
        DealDB $dealDB
    ): Deal {
        return new Deal(
            new DealId($dealDB->id),
            new Seller(new UserId($dealDB->seller_id)),
            new Buyer(new UserId($dealDB->buyer_id)),
            new TextbookId($dealDB->textbook_id),
            DealStatus::create($dealDB->deal_status),
        );
    }
}