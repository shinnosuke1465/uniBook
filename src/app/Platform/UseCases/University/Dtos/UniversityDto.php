<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University\Dtos;

readonly class UniversityDto
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}
}

