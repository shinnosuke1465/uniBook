<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\Address;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;

readonly class Address
{
    public function __construct(
        public String255 $address,
    ) {
    }

    /**
     *@throws DomainException
     */
    public static function create(
        string $address,
    ): self {
        return new self(
            new String255($address),
        );
    }

    public function getValue(): string
    {
        return $this->address->value;
    }
}
