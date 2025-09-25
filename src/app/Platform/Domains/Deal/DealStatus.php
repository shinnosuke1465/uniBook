<?php

declare(strict_types=1);

namespace App\Platform\Domains\Deal;

use App\Exceptions\DomainException;

enum DealStatus: string
{
    case Listing = 'Listing';
    case Purchased = 'Purchased';
    case Shipping = 'Shipping';
    case Completed = 'Completed';
    case Cancelled = 'Cancelled';

    /**
     * @throws DomainException
     */
    public static function create(string $status): self
    {
        return match ($status) {
            self::Listing->value => self::Listing,
            self::Purchased->value => self::Purchased,
            self::Shipping->value => self::Shipping,
            self::Completed->value => self::Completed,
            self::Cancelled->value => self::Cancelled,
            default => throw new DomainException('Invalid deal status: ' . $status),
        };
    }
}
