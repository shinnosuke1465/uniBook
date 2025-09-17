<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty\Dtos;

use App\Platform\Domains\Faculty\Faculty;
use App\Platform\Domains\Faculty\FacultyList;

readonly class FacultyDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $universityId,
    ) {
    }

    public static function create(
        Faculty $faculty,
    ): self {
        return new self(
            $faculty->id->value,
            $faculty->name->value,
            $faculty->universityId->value,
        );
    }

    public static function createFromList(FacultyList $facultyList): array
    {
        return collect($facultyList->toArray())->map(
            fn ($f) => new self(
                $f->id->value,
                $f->name->value,
                $f->universityId->value,
            )
        )->all();
    }
}
