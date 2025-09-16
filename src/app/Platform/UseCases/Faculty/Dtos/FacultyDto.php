<?php

declare(strict_types=1);

namespace App\Platform\UseCases\Faculty\Dtos;

use App\Platform\Domains\Faculty\FacultyList;

readonly class FacultyDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $universityId,
    ) {
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
