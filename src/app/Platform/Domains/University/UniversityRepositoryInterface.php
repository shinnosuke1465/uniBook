<?php

declare(strict_types=1);

namespace App\Platform\Domains\University;

interface UniversityRepositoryInterface
{
    public function findById(UniversityId $universityId): ?University;

    public function insert(University $university): void;
}
