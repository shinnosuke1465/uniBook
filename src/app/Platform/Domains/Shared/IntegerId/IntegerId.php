<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\IntegerId;

use App\Exceptions\DomainException;

abstract readonly class IntegerId
{
    public int $value;

    /**
     * @throws DomainException
     */
    public function __construct(
        int $value
    ){
        if ($value < 1) {
            throw new DomainException('不正な引数が入力されています。');
        }
        $this->value = $value;
    }

    public function equals(IntegerId $integerId): bool
    {
        return $this->value === $integerId->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
