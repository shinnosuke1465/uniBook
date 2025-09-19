<?php

declare(strict_types=1);

namespace App\Platform\Domains\Textbook;

use App\Exceptions\DomainException;

enum ConditionType: string
{
    case NEW = 'new';
    case NEAR_NEW = 'near_new';
    case NO_DAMAGE = 'no_damage';
    case SLIGHT_DAMAGE = 'slight_damage';
    case DAMAGE = 'damage';
    case POOR_CONDITION = 'poor_condition';

    /**
     * @throws DomainException
     */
    public static function create(string $type): self
    {
        return match ($type) {
            self::NEW->value => self::NEW,
            self::NEAR_NEW->value => self::NEAR_NEW,
            self::NO_DAMAGE->value => self::NO_DAMAGE,
            self::SLIGHT_DAMAGE->value => self::SLIGHT_DAMAGE,
            self::DAMAGE->value => self::DAMAGE,
            self::POOR_CONDITION->value => self::POOR_CONDITION,
            default => throw new DomainException('Invalid condition type: ' . $type),
        };
    }
}
