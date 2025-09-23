<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealEvent;

use App\Exceptions\DomainException;

enum ActorType: string
{
    case Seller = 'seller';
    case Buyer = 'buyer';

    /**
     * @throws DomainException
     */
    public static function create(string $type): self
    {
        return match ($type) {
            self::Seller->value => self::Seller,
            self::Buyer->value => self::Buyer,
            default => throw new DomainException('Invalid actor type: ' . $type),
        };
    }
}
