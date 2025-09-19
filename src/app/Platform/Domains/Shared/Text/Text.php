<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\Text;

use App\Platform\Domains\Shared\String\StringLengthValidator;

readonly class Text
{
    private const MAX_LENGTH = 1000;

    public function __construct(
        private string $value,
    ) {
        StringLengthValidator::check(self::MAX_LENGTH, $value);
    }

    public function equals(Text $text): bool
    {
        return $this->value === $text->value;
    }
}
