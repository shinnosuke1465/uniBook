<?php

declare(strict_types=1);

namespace App\Platform\Domains\University;

use App\Platform\Domains\Shared\String\String255;

interface UniversityRepositoryInterface
{
    public function findAll(): array;

    public function findById(UniversityId $universityId): ?University;

    public function findByName(String255 $name): ?University;

    public function insert(University $university): void;
}
