<?php

declare(strict_types=1);

namespace App\Platform\Domains\University;

use App\Platform\Domains\Shared\String\String255;

readonly class University
{
    public function __construct(
        public UniversityId $id,
        public String255 $name,
    ) {
    }

    public static function create(
        String255 $name,
    ): self {
        return new self(
            new UniversityId(),
            $name,
        );
    }
}
