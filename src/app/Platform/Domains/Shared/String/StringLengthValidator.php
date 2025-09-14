<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\String;

use App\Exceptions\DomainException;

readonly class StringLengthValidator
{
    /**
     * $textの文字数が0以上、$length以下であることを検証する。
     *
     * @throws DomainException
     */
    public static function check(
        int $length,
        string $text
    ): void {
        if ($length < 1) {
            throw new DomainException('文字列の最大長は1以上を指定してください。');
        }

        if (mb_strlen($text) > $length) {
            throw new DomainException($length . '文字以内の文字列を指定してください。');
        }
    }
}
