<?php

declare(strict_types=1);

namespace App\Platform\Domains\DealEvent;

use App\Exceptions\DomainException;

enum EventType: string
{
    case Listing = 'Listing';
    case Purchase = 'Purchase';
    case ReportDelivery = 'ReportDelivery';
    case ReportReceipt = 'ReportReceipt';
    case Cancel = 'Cancel';

    /**
     * @throws DomainException
     */
    public static function create(string $type): self
    {
        return match ($type) {
            self::Listing->value => self::Listing,
            self::Purchase->value => self::Purchase,
            self::ReportDelivery->value => self::ReportDelivery,
            self::ReportReceipt->value => self::ReportReceipt,
            self::Cancel->value => self::Cancel,
            default => throw new DomainException('Invalid event type: ' . $type),
        };
    }
}
