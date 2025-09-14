<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\Uuid;

use App\Exceptions\DomainException;
use Illuminate\Support\Str;

abstract readonly class Uuid
{
    public string $value;

    /**
     * @throws DomainException
     */
    public function __construct(
        string $value = null
    ) {
        $this->value = $value ?? (string)Str::orderedUuid();

        if (!Str::isUuid($this->value)) {
            throw new DomainException('uuid is invalid. value: ' . $this->value);
        }
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->value === $uuid->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
