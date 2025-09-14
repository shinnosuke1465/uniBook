<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\String;

use App\Exceptions\DomainException;

readonly class String255
{
    private const MAX_LENGTH = 255;

    /**
     * @throws DomainException
     */
    public function __construct(
        public string $value,
    ) {
        StringLengthValidator::check(self::MAX_LENGTH, $value);
    }

    public function equals(String255 $string255): bool
    {
        return $this->value === $string255->value;
    }
}
