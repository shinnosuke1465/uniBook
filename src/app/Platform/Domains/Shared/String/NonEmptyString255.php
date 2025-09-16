<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\String;

use App\Exceptions\DomainException;

readonly class NonEmptyString255
{
    private const MAX_LENGTH = 255;

    /**
     * @throws DomainException
     */
    public function __construct(
        public string $value,
    ) {
        NonEmptyStringValidator::check($value);
        StringLengthValidator::check( self::MAX_LENGTH, $value);
    }

    public function equals(NonEmptyString255 $string): bool
    {
        return $this->value === $string->value;
    }
}
