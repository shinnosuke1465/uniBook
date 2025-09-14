<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\PostCode;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;

readonly class PostCode
{
    private const POST_CODE_LENGTH = 7;

    public function __construct(
        public String255 $postCode,
    ) {
        if (!$this->isValid()) {
            throw new DomainException('郵便番号は7桁の数字で入力してください。');
        }
    }

    /**
     *@throws DomainException
     */
    public static function create(
       string $postCode,
    ): self {

        return new self(
            new String255($postCode),
        );
    }

    private function isValid(): bool
    {
        return preg_match('/^\\d{' . self::POST_CODE_LENGTH . '}$/', $this->postCode->value) === 1;
    }

    public function getValue(): string
    {
        return $this->postCode->value;
    }
}
