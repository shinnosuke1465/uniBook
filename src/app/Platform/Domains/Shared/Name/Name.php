<?php

declare(strict_types=1);

namespace App\Platform\Domains\Shared\Name;

readonly class Name
{
    public function __construct(
        public string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
