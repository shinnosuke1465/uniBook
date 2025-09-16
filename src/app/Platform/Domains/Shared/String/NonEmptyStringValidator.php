<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\String;

use App\Exceptions\DomainException;

class NonEmptyStringValidator
{
    /**
     * @throws DomainException
     */
    public static function check(
        string $text
    ): void {
        if ($text === '') {
            throw new DomainException('空文字は許可されていません。');
        }
    }
}
