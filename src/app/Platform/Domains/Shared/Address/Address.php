<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\Address;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;

readonly class Address
{
    public function __construct(
        public String255 $name,
    ) {
    }

    /**
     *@throws DomainException
     */
    public static function create(
        string $name,
    ): self {
        return new self(
            new String255($name),
        );
    }

    public function getValue(): string
    {
        return $this->name->value;
    }
}
