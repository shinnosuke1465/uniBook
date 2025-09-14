<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\MailAddress;

use App\Exceptions\DomainException;
use App\Platform\Domains\Shared\String\String255;

readonly class MailAddress
{
    /**
     * @throws DomainException
     */
    public function __construct(
        public String255 $mailAddress,
    ) {
        if(!$this->isValid()) {
            throw new DomainException('メールアドレスが不正です。' . $this->mailAddress->value);
        }
    }

    private function isValid(): bool
    {
        return filter_var($this->mailAddress->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     *@throws DomainException
     */
    public static function create(
       string $mailAddress,
    ): self {
        return new self(
            new String255($mailAddress),
        );
    }

    public function getValue(): string
    {
        return $this->mailAddress->value;
    }
}
