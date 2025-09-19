<?php

declare(strict_types=1);

namespace App\Platform\Domains\Textbook;

use App\Exceptions\DomainException;

readonly class Price
{
    /**
     * @throws DomainException
     */
    public function __construct(
        public int $value,
    ) {
        if ($value < 0) {
            throw new DomainException('価格は0以上である必要があります');
        }
    }

    public function equals(Price $price): bool
    {
        return $this->value === $price->value;
    }
}