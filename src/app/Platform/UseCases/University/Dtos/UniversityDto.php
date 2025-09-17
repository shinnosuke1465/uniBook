<?php

declare(strict_types=1);

namespace App\Platform\UseCases\University\Dtos;

use App\Platform\Domains\University\University;
use App\Platform\Domains\University\UniversityId;

readonly class UniversityDto
{
    public function __construct(
        public string $id,
        public string $name,
    ) {}

    public static function create(
        University $university,
    ): self {
        return new self(
            $university->id->value,
            $university->name->value,
        );
    }
}

